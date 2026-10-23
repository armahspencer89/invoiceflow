<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_listing_excludes_invoices_with_mismatched_ownership(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // The customer belongs to the authenticated user.
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Ownership Test Customer';
        $customer->email = 'ownership-test@example.com';
        $customer->is_active = true;
        $customer->save();

        // Deliberately create an inconsistent invoice for security testing.
        $invoice = new Invoice();
        $invoice->user_id = $otherUser->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = null;
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'draft';
        $invoice->vat_rate = '21.00';
        $invoice->issued_at = null;
        $invoice->save();

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.index'));

        $response->assertOk();

        // Verify the actual collection passed to the Blade view.
        $response->assertViewHas('invoices', function ($invoices) use ($invoice) {
            return ! $invoices->getCollection()->contains('id', $invoice->id);
        });
    }
    public function test_customer_owner_cannot_access_invoice_owned_by_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Customer belongs to the authenticated user.
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Security Test Customer';
        $customer->email = 'security-test@example.com';
        $customer->is_active = true;
        $customer->save();

        // Invoice belongs to another user.
        $invoice = new Invoice();
        $invoice->user_id = $otherUser->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = null;
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'draft';
        $invoice->vat_rate = '21.00';
        $invoice->issued_at = null;
        $invoice->save();

        // Attempt to view the invoice directly.
        $this->actingAs($user)
            ->get(route('invoices.show', $invoice))
            ->assertForbidden();

        // Attempt to access the edit page.
        $this->actingAs($user)
            ->get(route('invoices.edit', $invoice))
            ->assertForbidden();

        // Attempt to issue the invoice.
        $this->actingAs($user)
            ->patch(route('invoices.send', $invoice))
            ->assertForbidden();

        // Verify the invoice remains unchanged.
        $invoice->refresh();

        $this->assertSame('draft', $invoice->status);
        $this->assertNull($invoice->invoice_number);
        $this->assertNull($invoice->issued_at);
    }
    public function test_customer_owner_cannot_record_payment_for_invoice_owned_by_another_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // The customer belongs to the authenticated user.
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Payment Security Customer';
        $customer->email = 'payment-security@example.com';
        $customer->is_active = true;
        $customer->save();

        // The invoice belongs to another user.
        $invoice = new Invoice();
        $invoice->user_id = $otherUser->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = 'INV-' . now()->format('Y') . '-0001';
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'sent';
        $invoice->vat_rate = '21.00';
        $invoice->issued_at = now();
        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Security test service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        // Attempt to record a payment.
        $this->actingAs($user)
            ->post(route('invoices.payments.store', $invoice), [
                'amount' => '50.00',
                'payment_date' => today()->toDateString(),
                'method' => 'bank_transfer',
            ])
            ->assertForbidden();

        // Verify no payment was recorded.
        $this->assertDatabaseMissing('payments', [
            'invoice_id' => $invoice->id,
        ]);

        // Verify the invoice status remains unchanged.
        $this->assertSame('sent', $invoice->fresh()->status);
    }
    public function test_user_cannot_override_protected_invoice_fields_during_creation(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Tampering Test Customer';
        $customer->email = 'tampering-test@example.com';
        $customer->is_active = true;
        $customer->save();

        $response = $this->actingAs($user)
            ->post(route('invoices.store'), [
                'customer_id' => $customer->id,
                'invoice_date' => today()->toDateString(),
                'due_date' => today()->addDays(14)->toDateString(),
                'vat_rate' => '21.00',

                'lines' => [
                    [
                        'description' => 'Security test service',
                        'quantity' => 1,
                        'unit_price' => 100,
                    ],
                ],

                // Malicious values submitted by the user.
                'user_id' => $otherUser->id,
                'invoice_number' => 'INV-2026-9999',
                'status' => 'paid',
                'issued_at' => now()->toDateTimeString(),
            ]);

        $response->assertSessionHasNoErrors();

        $invoice = Invoice::query()
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $response->assertRedirect(route('invoices.show', $invoice));

        // Ownership must come from the authenticated user.
        $this->assertSame($user->id, $invoice->user_id);

        // Protected fields must not be controlled by the request.
        $this->assertNull($invoice->invoice_number);
        $this->assertSame('draft', $invoice->status);
        $this->assertNull($invoice->issued_at);
    }
    public function test_user_cannot_change_invoice_to_another_users_customer(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create a customer belonging to the authenticated user.
        $ownCustomer = new Customer();
        $ownCustomer->user_id = $user->id;
        $ownCustomer->name = 'My Customer';
        $ownCustomer->email = 'my-customer@example.com';
        $ownCustomer->is_active = true;
        $ownCustomer->save();

        // Create a customer belonging to another user.
        $otherCustomer = new Customer();
        $otherCustomer->user_id = $otherUser->id;
        $otherCustomer->name = 'Other Customer';
        $otherCustomer->email = 'other-customer@example.com';
        $otherCustomer->is_active = true;
        $otherCustomer->save();

        // Create a valid draft invoice belonging to the authenticated user.
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->customer_id = $ownCustomer->id;
        $invoice->invoice_number = null;
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'draft';
        $invoice->vat_rate = '21.00';
        $invoice->issued_at = null;
        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Original service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        // Attempt to assign another user's customer.
        $response = $this->actingAs($user)
            ->put(route('invoices.update', $invoice), [
                'customer_id' => $otherCustomer->id,
                'invoice_date' => today()->toDateString(),
                'due_date' => today()->addDays(14)->toDateString(),
                'vat_rate' => '21.00',
                'lines' => [
                    [
                        'description' => 'Updated service',
                        'quantity' => 1,
                        'unit_price' => 150,
                    ],
                ],
            ]);

        // Customer ownership validation must reject the request.
        $response->assertSessionHasErrors('customer_id');

        // The original customer must remain assigned.
        $this->assertSame(
            $ownCustomer->id,
            $invoice->fresh()->customer_id
        );

        // The original invoice line must remain unchanged.
        $this->assertDatabaseHas('invoice_lines', [
            'invoice_id' => $invoice->id,
            'description' => 'Original service',
        ]);
    }
    public function test_user_cannot_override_protected_invoice_fields_during_update(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create a customer belonging to the authenticated user.
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->name = 'Update Tampering Customer';
        $customer->email = 'update-tampering@example.com';
        $customer->is_active = true;
        $customer->save();

        // Create a valid draft invoice.
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->customer_id = $customer->id;
        $invoice->invoice_number = null;
        $invoice->invoice_date = today();
        $invoice->due_date = today()->addDays(14);
        $invoice->status = 'draft';
        $invoice->vat_rate = '21.00';
        $invoice->issued_at = null;
        $invoice->save();

        $invoice->lines()->create([
            'description' => 'Original service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        // Submit a valid update containing malicious protected fields.
        $response = $this->actingAs($user)
            ->put(route('invoices.update', $invoice), [
                'customer_id' => $customer->id,
                'invoice_date' => today()->toDateString(),
                'due_date' => today()->addDays(14)->toDateString(),
                'vat_rate' => '21.00',

                'lines' => [
                    [
                        'description' => 'Updated service',
                        'quantity' => 2,
                        'unit_price' => 150,
                    ],
                ],

                // Attempt to manipulate protected fields.
                'user_id' => $otherUser->id,
                'invoice_number' => 'INV-2026-9999',
                'status' => 'paid',
                'issued_at' => now()->toDateTimeString(),
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh();

        // Protected fields must remain unchanged.
        $this->assertSame($user->id, $invoice->user_id);
        $this->assertNull($invoice->invoice_number);
        $this->assertSame('draft', $invoice->status);
        $this->assertNull($invoice->issued_at);

        // Legitimate changes must still be saved.
        $this->assertDatabaseHas('invoice_lines', [
            'invoice_id' => $invoice->id,
            'description' => 'Updated service',
        ]);

        $this->assertDatabaseMissing('invoice_lines', [
            'invoice_id' => $invoice->id,
            'description' => 'Original service',
        ]);
    }
}
