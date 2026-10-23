<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create an invoice belonging to the given user.
     */
    private function createInvoice(
        User $user,
        string $status = 'sent',
        string $vatRate = '21.00'
    ): Invoice {
        $customer = new Customer();

        $customer->user_id = $user->id;
        $customer->name = 'PDF Test Customer';
        $customer->email = 'customer-' . Str::uuid() . '@example.com';
        $customer->is_active = true;

        $customer->save();

        $invoice = new Invoice();

        $invoice->user_id = $user->id;
        $invoice->customer_id = $customer->id;

        $invoice->invoice_number = $status === 'draft'
            ? null
            : 'INV-' . now()->format('Y') . '-0001';

        $invoice->invoice_date = today()->subDays(5);
        $invoice->due_date = today()->addDays(15);

        $invoice->status = $status;
        $invoice->vat_rate = $vatRate;

        $invoice->issued_at = $status === 'draft'
            ? null
            : now();

        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Website Development',
            'quantity' => 2,
            'unit_price' => 100,
        ]);

        return $invoice->fresh();
    }

    /**
     * Create the issuing user's business profile.
     */
    private function createBusinessProfile(User $user): void
    {
        $user->businessProfile()->create([
            'business_name' => 'InvoiceFlow Test Business',
            'email' => 'billing@example.com',
            'phone' => '+32 470 00 00 00',
            'address' => 'Test Street 10, Antwerp',
            'vat_number' => 'BE0123456789',
            'iban' => 'BE00000000000000',
            'bic' => 'GEBABEBB',
        ]);
    }

    /**
     * Guest users must authenticate before downloading PDFs.
     */
    public function test_guest_cannot_download_invoice_pdf(): void
    {
        $user = User::factory()->create();

        $invoice = $this->createInvoice($user);

        $this->get(route('invoices.pdf', $invoice))
            ->assertRedirect(route('login'));
    }

    /**
     * Invoice owners can download their invoices.
     */
    public function test_owner_can_download_invoice_pdf(): void
    {
        $user = User::factory()->create();

        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $response = $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice));

        $response->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            $response->headers->get('Content-Type', '')
        );
    }

    /**
     * Users cannot download invoices belonging to other users.
     */
    public function test_user_cannot_download_another_users_invoice(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->createBusinessProfile($owner);

        $invoice = $this->createInvoice($owner);

        $this->actingAs($otherUser)
            ->get(route('invoices.pdf', $invoice))
            ->assertForbidden();
    }

    /**
     * A business profile is required to generate a PDF.
     */
    public function test_missing_business_profile_redirects_to_settings(): void
    {
        $user = User::factory()->create();

        $invoice = $this->createInvoice($user);

        $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice))
            ->assertRedirect(route('business-profile.edit'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('business_profiles', 0);
    }

    /**
     * The response must contain the correct PDF filename.
     */
    public function test_pdf_has_correct_filename(): void
    {
        $user = User::factory()->create();

        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $response = $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice));

        $response->assertOk();

        $contentDisposition = $response->headers->get(
            'Content-Disposition',
            ''
        );

        $this->assertStringContainsString(
            $invoice->invoice_number . '.pdf',
            $contentDisposition
        );
    }

    /**
     * The downloaded response must contain PDF data.
     */
    public function test_downloaded_file_contains_valid_pdf_header(): void
    {
        $user = User::factory()->create();

        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        $response = $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice));

        $response->assertOk();

        $this->assertStringStartsWith(
            '%PDF-',
            $response->getContent()
        );
    }

    /**
     * PDF generation must support invoices with partial payments.
     */
    public function test_pdf_can_be_generated_for_partially_paid_invoice(): void
    {
        $user = User::factory()->create();

        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user);

        // Invoice: €200 + 21% VAT = €242.
        $invoice->payments()->create([
            'amount_cents' => 10000,
            'payment_date' => today()->toDateString(),
            'method' => 'bank_transfer',
            'reference' => 'PDF-TEST-PAYMENT',
        ]);

        $invoice->refresh();

        $this->assertSame(20000, $invoice->subtotalCents());
        $this->assertSame(4200, $invoice->vatAmountCents());
        $this->assertSame(24200, $invoice->totalCents());
        $this->assertSame(10000, $invoice->paidCents());
        $this->assertSame(14200, $invoice->balanceCents());

        $this->assertSame(
            'Partially Paid',
            $invoice->displayStatus()
        );

        $response = $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice));

        $response->assertOk();

        $this->assertStringStartsWith(
            '%PDF-',
            $response->getContent()
        );
    }

    /**
     * PDF generation must also support draft invoices.
     */
    public function test_draft_invoice_can_be_downloaded_as_pdf(): void
    {
        $user = User::factory()->create();

        $this->createBusinessProfile($user);

        $invoice = $this->createInvoice($user, 'draft');

        $this->assertSame('Draft', $invoice->displayStatus());

        $response = $this->actingAs($user)
            ->get(route('invoices.pdf', $invoice));

        $response->assertOk();

        $this->assertStringStartsWith(
            '%PDF-',
            $response->getContent()
        );
    }
}
