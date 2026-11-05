<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_excludes_invoices_and_payments_with_mismatched_ownership(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Customer belongs to User A.
        $customer = new Customer();

        $customer->user_id = $userA->id;
        $customer->name = 'Test Customer';
        $customer->email = 'dashboard-test@example.com';
        $customer->is_active = true;

        $customer->save();

        // Invoice belongs to User B, but references User A's customer.
        $invoice = new Invoice();
        $invoice->user_id = $userB->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = 'INV-2026-0001';
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'sent';
        $invoice->vat_rate = 21;
        $invoice->issued_at = now();
        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Security test service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        // Record a payment against the mismatched invoice.
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount_cents' => 5000,
            'payment_date' => today(),
            'method' => 'bank_transfer',
        ]);

        // User A must not see the mismatched invoice or its payment.
        $response = $this->actingAs($userA)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalInvoices', 0);
        $response->assertViewHas('outstandingCents', 0);
        $response->assertViewHas('overdueInvoices', 0);
        $response->assertViewHas('paymentsReceivedCents', 0);

        $response->assertViewHas('recentInvoices', function ($invoices) {
            return $invoices->isEmpty();
        });
    }
}
