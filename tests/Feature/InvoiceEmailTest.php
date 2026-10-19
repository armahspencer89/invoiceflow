<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvoiceEmailTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoice(
        User $user,
        string $status = 'sent'
    ): Invoice {

        $customer = new Customer();

        $customer->user_id = $user->id;
        $customer->name = 'Email Test Customer';
        $customer->email = 'customer-' . Str::uuid() . '@example.com';
        $customer->is_active = true;

        $customer->save();


        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'invoice_number' => 'EMAIL-' . Str::uuid(),
            'invoice_date' => today()->subDays(5),
            'due_date' => today()->addDays(15),
            'status' => $status,
            'vat_rate' => '21.00',
        ]);

        $invoice->lines()->create([
            'description' => 'Web Development',
            'quantity' => 2,
            'unit_price' => 100,
        ]);

        return $invoice->fresh();
    }

    private function createBusinessProfile(User $user): void
    {
        $user->businessProfile()->create([
            'business_name' => 'InvoiceFlow Test Business',
            'email' => 'billing@example.com',
            'address' => 'Test Street 10, Antwerp',
            'iban' => 'BE00000000000000',
            'bic' => 'GEBABEBB',
        ]);
    }

    public function test_guest_cannot_email_invoice(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->post(route('invoices.email', $invoice))
            ->assertRedirect(route('login'));

        Mail::assertNothingSent();
    }

    public function test_owner_can_email_sent_invoice(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertRedirect(route('invoices.show', $invoice))
            ->assertSessionHas('success');

        Mail::assertSent(InvoiceMail::class, 1);
    }

    public function test_email_is_sent_to_correct_customer(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice));

        Mail::assertSent(
            InvoiceMail::class,
            function (InvoiceMail $mail) use ($invoice) {
                return $mail->hasTo($invoice->customer->email)
                    && $mail->invoice->is($invoice);
            }
        );
    }

    public function test_user_cannot_email_another_users_invoice(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->createBusinessProfile($owner);

        $invoice = $this->createInvoice($owner);

        $this->actingAs($otherUser)
            ->post(route('invoices.email', $invoice))
            ->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_draft_invoice_cannot_be_emailed(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user, 'draft');

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertRedirect(route('invoices.show', $invoice))
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    }

    public function test_paid_invoice_can_be_emailed(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user, 'paid');

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertSessionHas('success');

        Mail::assertSent(InvoiceMail::class, 1);
    }

    public function test_missing_business_profile_prevents_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertRedirect(route('business-profile.edit'))
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    }

    public function test_invoice_status_does_not_change_after_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user, 'sent');

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertSessionHas('success');

        $this->assertSame('sent', $invoice->fresh()->status);

        Mail::assertSent(InvoiceMail::class, 1);
    }

    public function test_email_contains_pdf_attachment(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->post(route('invoices.email', $invoice))
            ->assertSessionHas('success');

        Mail::assertSent(
            InvoiceMail::class,
            function (InvoiceMail $mail) use ($invoice) {
                $attachments = $mail->attachments();

                return count($attachments) === 1
                    && $attachments[0]->as ===
                    $invoice->invoice_number . '.pdf'
                    && $attachments[0]->mime === 'application/pdf';
            }
        );
    }
}
