<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_calculates_financial_totals_correctly(): void
    {
        $user = User::factory()->create();

        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Dashboard Metrics Customer';
        $customer->email = 'metrics@example.com';
        $customer->is_active = true;
        $customer->save();

        $this->createInvoice($user, $customer, 'draft', 100);
        $this->createInvoice($user, $customer, 'sent', 200);
        $this->createInvoice($user, $customer, 'sent', 300, 100);
        $this->createInvoice($user, $customer, 'paid', 150, 150);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalInvoices', 4);
        $response->assertViewHas('outstandingCents', 40000);
        $response->assertViewHas('paymentsReceivedCents', 25000);
        $response->assertViewHas('overdueInvoices', 0);
    }

    public function test_dashboard_counts_overdue_invoices_correctly(): void
    {
        $user = User::factory()->create();

        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Overdue Test Customer';
        $customer->email = 'overdue@example.com';
        $customer->is_active = true;
        $customer->save();

        // Draft: past due date, but must not be overdue.
        $draft = $this->createInvoice($user, $customer, 'draft', 100);
        $draft->due_date = today()->subDays(5);
        $draft->save();

        // Sent: unpaid and past due date.
        $unpaid = $this->createInvoice($user, $customer, 'sent', 200);
        $unpaid->due_date = today()->subDays(5);
        $unpaid->save();

        // Partially paid: still overdue because money is outstanding.
        $partial = $this->createInvoice($user, $customer, 'sent', 300, 100);
        $partial->due_date = today()->subDays(3);
        $partial->save();

        // Paid: past due date, but no longer overdue.
        $paid = $this->createInvoice($user, $customer, 'paid', 150, 150);
        $paid->due_date = today()->subDays(10);
        $paid->save();

        // Sent: due today, so not overdue.
        $dueToday = $this->createInvoice($user, $customer, 'sent', 50);
        $dueToday->due_date = today();
        $dueToday->save();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();

        $response->assertViewHas('totalInvoices', 5);
        $response->assertViewHas('overdueInvoices', 2);
        $response->assertViewHas('outstandingCents', 45000);
        $response->assertViewHas('paymentsReceivedCents', 25000);

        // Verify status calculations directly.
        $this->assertTrue($unpaid->isOverdue());
        $this->assertTrue($partial->isOverdue());

        $this->assertFalse($draft->isOverdue());
        $this->assertFalse($paid->isOverdue());
        $this->assertFalse($dueToday->isOverdue());

        $this->assertSame('Overdue', $partial->displayStatus());
    }
    private function createInvoice(
        User $user,
        Customer $customer,
        string $status,
        int $totalEuros,
        int $paidEuros = 0
    ): Invoice {
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = $status === 'draft'
            ? null
            : 'INV-2026-' . str_pad(
                (string) (Invoice::count() + 1),
                4,
                '0',
                STR_PAD_LEFT
            );
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = $status;
        $invoice->vat_rate = 0;
        $invoice->issued_at = $status === 'draft' ? null : now();
        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Dashboard metrics test',
            'quantity' => 1,
            'unit_price' => $totalEuros,
        ]);

        if ($paidEuros > 0) {
            $invoice->payments()->create([
                'amount_cents' => $paidEuros * 100,
                'payment_date' => today(),
                'method' => 'bank_transfer',
            ]);
        }

        return $invoice;
    }
}
