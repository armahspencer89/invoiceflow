<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceSequence;
use App\Models\User;
use App\Services\InvoiceNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use LogicException;
use Tests\TestCase;

class InvoiceNumberingTest extends TestCase
{
    use RefreshDatabase;

    private function createDraft(User $user): Invoice
    {
        $customer = new Customer();

        $customer->user_id = $user->id;
        $customer->name = 'Numbering Test Customer';
        $customer->email = 'customer-' . uniqid() . '@example.com';
        $customer->is_active = true;

        $customer->save();

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
            'description' => 'Test service',
            'quantity' => 1,
            'unit_price' => 100,
        ]);

        return $invoice->fresh();
    }

    public function test_draft_has_no_invoice_number(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDraft($user);

        $this->assertNull($invoice->invoice_number);
        $this->assertNull($invoice->issued_at);
        $this->assertSame('draft', $invoice->status);
    }

    public function test_first_issued_invoice_gets_number_one(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDraft($user);

        $issued = app(InvoiceNumberService::class)->issue($invoice);

        $this->assertSame(
            'INV-' . now()->format('Y') . '-0001',
            $issued->invoice_number
        );

        $this->assertSame('sent', $issued->status);
        $this->assertNotNull($issued->issued_at);
    }

    public function test_invoice_numbers_are_sequential(): void
    {
        $user = User::factory()->create();

        $first = $this->createDraft($user);
        $second = $this->createDraft($user);

        $service = app(InvoiceNumberService::class);

        $first = $service->issue($first);
        $second = $service->issue($second);

        $year = now()->format('Y');

        $this->assertSame(
            "INV-{$year}-0001",
            $first->invoice_number
        );

        $this->assertSame(
            "INV-{$year}-0002",
            $second->invoice_number
        );
    }

    public function test_each_user_has_independent_numbering(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $service = app(InvoiceNumberService::class);

        $first = $service->issue(
            $this->createDraft($firstUser)
        );

        $second = $service->issue(
            $this->createDraft($secondUser)
        );

        $this->assertSame(
            $first->invoice_number,
            $second->invoice_number
        );
    }

    public function test_invoice_cannot_be_issued_twice(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDraft($user);

        $service = app(InvoiceNumberService::class);

        $service->issue($invoice);

        $this->expectException(LogicException::class);

        $service->issue($invoice);
    }

    public function test_old_invoice_date_prevents_issuance(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDraft($user);

        $invoice->invoice_date = today()->subDay();
        $invoice->save();

        try {
            app(InvoiceNumberService::class)->issue($invoice);

            $this->fail('Expected invoice date validation to fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'invoice_date',
                $exception->errors()
            );
        }

        $invoice->refresh();

        $this->assertSame('draft', $invoice->status);
        $this->assertNull($invoice->invoice_number);
        $this->assertNull($invoice->issued_at);
        $this->assertDatabaseCount('invoice_sequences', 0);
    }

    public function test_numbering_resets_each_year(): void
    {
        Carbon::setTestNow(
            Carbon::parse('2026-12-31 12:00:00')
        );

        try {
            $user = User::factory()->create();
            $service = app(InvoiceNumberService::class);

            $first = $service->issue(
                $this->createDraft($user)
            );

            $this->assertSame(
                'INV-2026-0001',
                $first->invoice_number
            );

            Carbon::setTestNow(
                Carbon::parse('2027-01-01 12:00:00')
            );

            $second = $service->issue(
                $this->createDraft($user)
            );

            $this->assertSame(
                'INV-2027-0001',
                $second->invoice_number
            );
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_deleted_draft_does_not_consume_number(): void
    {
        $user = User::factory()->create();

        $draft = $this->createDraft($user);

        // Delete its lines first; the invoice is still a draft.
        $draft->lines()->delete();
        $draft->delete();

        $issued = app(InvoiceNumberService::class)->issue(
            $this->createDraft($user)
        );

        $this->assertSame(
            'INV-' . now()->format('Y') . '-0001',
            $issued->invoice_number
        );
    }

    public function test_sequence_counter_matches_issued_invoices(): void
    {
        $user = User::factory()->create();
        $service = app(InvoiceNumberService::class);

        $service->issue($this->createDraft($user));
        $service->issue($this->createDraft($user));

        $sequence = InvoiceSequence::query()
            ->where('user_id', $user->id)
            ->where('year', now()->year)
            ->firstOrFail();

        $this->assertSame(2, $sequence->last_number);
    }
}
