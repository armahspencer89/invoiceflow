<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Throwable;

class InvoiceEmailController extends Controller
{
    public function send(Invoice $invoice): RedirectResponse
    {
        // Verify that the authenticated user owns this invoice.
        Gate::authorize('view', $invoice);

        // Draft invoices cannot be emailed.
        if (!in_array($invoice->status, ['sent', 'paid'], true)) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'Draft invoices cannot be emailed. Mark the invoice as sent first.');
        }

        // Load the customer and invoice owner's business profile.
        $invoice->load('customer.user.businessProfile');

        $customer = $invoice->customer;
        $businessProfile = $customer->user->businessProfile;

        // A business profile is required for PDF generation.
        if (!$businessProfile) {
            return redirect()
                ->route('business-profile.edit')
                ->with('error', 'Please complete your business profile before emailing invoices.');
        }

        // Verify the customer has an email address.
        if (
            !$customer->email ||
            !filter_var($customer->email, FILTER_VALIDATE_EMAIL)
        ) {
            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'This customer does not have a valid email address.');
        }

        try {
            Mail::to($customer->email)->send(
                new InvoiceMail($invoice, $businessProfile)
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('error', 'The invoice email could not be sent. Please try again.');
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with(
                'success',
                'Invoice email processed successfully for ' . $customer->email . '.'
            );
    }
}
