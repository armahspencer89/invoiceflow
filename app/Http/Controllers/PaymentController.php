<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function store(
        StorePaymentRequest $request,
        Invoice $invoice
    ): RedirectResponse {
        Gate::authorize('create', [Payment::class, $invoice]);

        $validated = $request->validated();

        $amountCents = Money::toCents(
            (string) $validated['amount']
        );

        DB::transaction(function () use (
            $invoice,
            $validated,
            $amountCents
        ) {
            // Lock the invoice until the transaction completes.
            $lockedInvoice = Invoice::query()
                ->whereKey($invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Recheck the status after acquiring the lock.
            if ($lockedInvoice->status !== 'sent') {
                throw ValidationException::withMessages([
                    'amount' => 'This invoice cannot accept payments.',
                ]);
            }

            $balanceCents = $lockedInvoice->balanceCents();

            if ($amountCents <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment amount must be positive.',
                ]);
            }

            if ($amountCents > $balanceCents) {
                throw ValidationException::withMessages([
                    'amount' =>
                        'The payment exceeds the outstanding balance of €'
                        . Money::format($balanceCents) . '.',
                ]);
            }

            $lockedInvoice->payments()->create([
                'amount_cents' => $amountCents,
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // The invoice becomes Paid only when fully settled.
            if ($amountCents === $balanceCents) {
                $lockedInvoice->status = 'paid';
                $lockedInvoice->save();
            }
        }, 3);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }
}
