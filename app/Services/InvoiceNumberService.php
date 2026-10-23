<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class InvoiceNumberService
{
    public function issue(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            // Lock the invoice to prevent simultaneous issuance.
            $lockedInvoice = Invoice::query()
                ->whereKey($invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedInvoice->status !== 'draft') {
                throw new LogicException(
                    'Only draft invoices can be issued.'
                );
            }

            if ($lockedInvoice->invoice_number !== null) {
                throw new LogicException(
                    'This draft already has an invoice number.'
                );
            }

            // Ensure the invoice still belongs to its customer owner.
            if (
                (int) $lockedInvoice->user_id !==
                (int) $lockedInvoice->customer->user_id
            ) {
                throw new LogicException(
                    'Invoice ownership does not match customer ownership.'
                );
            }

            $issuedAt = now();

            // MVP policy: the invoice date must be the issuance date.
            // This prevents backdating and future-dating at issuance.
            if (
                !$lockedInvoice->invoice_date->isSameDay($issuedAt)
            ) {
                throw ValidationException::withMessages([
                    'invoice_date' =>
                        'The invoice date must be today before issuing. '
                        . 'Edit the draft and update its invoice date.',
                ]);
            }

            $year = (int) $issuedAt->format('Y');

            // Create the annual sequence if it does not exist.
            InvoiceSequence::query()->insertOrIgnore([
                'user_id' => $lockedInvoice->user_id,
                'year' => $year,
                'last_number' => 0,
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]);

            // Lock the business's annual counter.
            $sequence = InvoiceSequence::query()
                ->where('user_id', $lockedInvoice->user_id)
                ->where('year', $year)
                ->lockForUpdate()
                ->firstOrFail();

            $nextNumber = $sequence->last_number + 1;

            if ($nextNumber > 999999999) {
                throw new LogicException(
                    'The invoice numbering sequence has reached its limit.'
                );
            }

            $sequence->last_number = $nextNumber;
            $sequence->save();

            // Assign protected fields explicitly.
            $lockedInvoice->invoice_number = sprintf(
                'INV-%d-%04d',
                $year,
                $nextNumber
            );

            $lockedInvoice->status = 'sent';
            $lockedInvoice->issued_at = $issuedAt;
            $lockedInvoice->save();

            return $lockedInvoice->fresh();
        }, 5);
    }
}
