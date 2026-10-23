<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoice(
        User $user,
        string $status = 'sent',
        ?string $dueDate = null
    ): Invoice {
        $customer = new Customer();

        $customer->user_id = $user->id;
        $customer->name = 'Test Customer';
        $customer->email = 'customer-' . Str::uuid() . '@example.com';
        $customer->is_active = true;

        $customer->save();


        $invoice = new Invoice();

        $invoice->user_id = $user->id;
        $invoice->customer_id = $customer->id;

        // Draft invoices do not receive official numbers.
        $invoice->invoice_number = $status === 'draft'
            ? null
            : 'INV-' . now()->format('Y') . '-0001';

        // These fixtures represent existing invoices.
        // Historical dates are needed for overdue tests.
        $invoice->invoice_date = today()->subDays(10);

        $invoice->due_date = $dueDate
            ?? today()->addDays(10)->toDateString();

        $invoice->status = $status;
        $invoice->vat_rate = 0;

        // Only issued invoices have an issuance timestamp.
        $invoice->issued_at = $status === 'draft'
            ? null
            : now();

        $invoice->save();


        // Invoice total: €100.00
        $invoice->lines()->create([
            'description' => 'Test service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        return $invoice->fresh();
    }

    private function paymentData(
        string $amount = '50.00'
    ): array {
        return [
            'amount' => $amount,
            'payment_date' => today()->toDateString(),
            'method' => 'bank_transfer',
            'reference' => 'TEST-PAYMENT',
            'notes' => 'Automated test',
        ];
    }

    public function test_guest_cannot_record_payment(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->post(
            route('invoices.payments.store', $invoice),
            $this->paymentData()
        )->assertRedirect(route('login'));

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_user_cannot_pay_another_users_invoice(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $invoice = $this->createInvoice($owner);

        $this->actingAs($otherUser)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData()
            )
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_draft_invoice_rejects_payments(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user, 'draft');

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData()
            )
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_partial_payment_updates_balance(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('40.00')
            )
            ->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh();

        $this->assertSame(10000, $invoice->totalCents());
        $this->assertSame(4000, $invoice->paidCents());
        $this->assertSame(6000, $invoice->balanceCents());

        $this->assertSame('sent', $invoice->status);
        $this->assertSame('Partially Paid', $invoice->displayStatus());

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount_cents' => 4000,
            'method' => 'bank_transfer',
        ]);
    }

    public function test_final_payment_marks_invoice_as_paid(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('40.00')
            )
            ->assertRedirect();

        $this->post(
            route('invoices.payments.store', $invoice),
            $this->paymentData('60.00')
        )->assertRedirect();

        $invoice->refresh();

        $this->assertSame(10000, $invoice->paidCents());
        $this->assertSame(0, $invoice->balanceCents());
        $this->assertSame('paid', $invoice->status);
        $this->assertSame('Paid', $invoice->displayStatus());

        $this->assertSame(2, $invoice->payments()->count());
    }

    public function test_paid_invoice_rejects_additional_payments(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user, 'paid');

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData()
            )
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_overpayment_is_rejected(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('150.00')
            )
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
        $this->assertSame('sent', $invoice->fresh()->status);
    }

    public function test_zero_payment_is_rejected(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('0.00')
            )
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_negative_payment_is_rejected(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('-10.00')
            )
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_more_than_two_decimal_places_is_rejected(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $this->paymentData('10.999')
            )
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_future_payment_date_is_rejected(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $data = $this->paymentData();
        $data['payment_date'] = today()->addDay()->toDateString();

        $this->actingAs($user)
            ->post(
                route('invoices.payments.store', $invoice),
                $data
            )
            ->assertSessionHasErrors('payment_date');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_unpaid_past_due_invoice_is_overdue(): void
    {
        $user = User::factory()->create();

        $invoice = $this->createInvoice(
            $user,
            'sent',
            today()->subDays(5)->toDateString()
        );

        $this->assertTrue($invoice->isOverdue());
        $this->assertSame('Overdue', $invoice->displayStatus());
    }

    public function test_partially_paid_past_due_invoice_is_overdue(): void
    {
        $user = User::factory()->create();

        $invoice = $this->createInvoice(
            $user,
            'sent',
            today()->subDays(5)->toDateString()
        );

        $invoice->payments()->create([
            'amount_cents' => 2500,
            'payment_date' => today()->toDateString(),
            'method' => 'cash',
        ]);

        $invoice->refresh();

        $this->assertTrue($invoice->isPartiallyPaid());
        $this->assertTrue($invoice->isOverdue());
        $this->assertSame('Overdue', $invoice->displayStatus());
        $this->assertSame(7500, $invoice->balanceCents());
    }

    public function test_paid_invoice_is_not_overdue(): void
    {
        $user = User::factory()->create();

        $invoice = $this->createInvoice(
            $user,
            'paid',
            today()->subDays(5)->toDateString()
        );

        $invoice->payments()->create([
            'amount_cents' => 10000,
            'payment_date' => today()->toDateString(),
            'method' => 'bank_transfer',
        ]);

        $invoice->refresh();

        $this->assertFalse($invoice->isOverdue());
        $this->assertSame('Paid', $invoice->displayStatus());
        $this->assertSame(0, $invoice->balanceCents());
    }
}
