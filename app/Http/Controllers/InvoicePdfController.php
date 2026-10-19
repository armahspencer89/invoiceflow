<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InvoicePdfController extends Controller
{
    public function download(Invoice $invoice): Response|RedirectResponse
    {
        // Verify invoice ownership.
        Gate::authorize('view', $invoice);

        // Load invoice relationships.
        $invoice->load([
            'customer.user.businessProfile',
            'lines',
            'payments',
        ]);

        // Retrieve the issuing business profile.
        $businessProfile = $invoice->customer->user->businessProfile;

        if (!$businessProfile) {
            return redirect()
                ->route('business-profile.edit')
                ->with(
                    'error',
                    'Please complete your business profile before downloading invoices.'
                );
        }

        // Read the dedicated PDF stylesheet.
        $css = file_get_contents(
            resource_path('css/pages/invoice-pdf.css')
        );

        // Render the invoice Blade template.
        $html = view('invoices.pdf', [
            'invoice' => $invoice,
            'businessProfile' => $businessProfile,
        ])->render();

        // Inject the external stylesheet into the rendered HTML.
        $html = str_replace(
            '</head>',
            '<style>' . $css . '</style></head>',
            $html
        );

        // Generate the PDF.
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            $invoice->invoice_number . '.pdf'
        );
    }
}
