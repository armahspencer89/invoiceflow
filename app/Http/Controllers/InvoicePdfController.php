<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InvoicePdfController extends Controller
{
    public function download(
        Invoice $invoice,
        InvoicePdfService $pdfService
    ): Response|RedirectResponse {
        Gate::authorize('view', $invoice);

        $invoice->load('customer.user.businessProfile');

        $businessProfile = $invoice
            ->customer
            ->user
            ->businessProfile;

        if (!$businessProfile) {
            return redirect()
                ->route('business-profile.edit')
                ->with(
                    'error',
                    'Please complete your business profile before downloading invoices.'
                );
        }

        $pdfContent = $pdfService->generate(
            $invoice,
            $businessProfile
        );

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>
                'attachment; filename="' .
                $invoice->invoice_number . '.pdf"',
        ]);
    }
}
