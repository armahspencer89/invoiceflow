<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(
        Invoice $invoice,
        BusinessProfile $businessProfile
    ): string {
        $invoice->load([
            'customer',
            'lines',
            'payments',
        ]);

        $css = file_get_contents(
            resource_path('css/pages/invoice-pdf.css')
        );

        $html = view('invoices.pdf', [
            'invoice' => $invoice,
            'businessProfile' => $businessProfile,
        ])->render();

        $html = str_replace(
            '</head>',
            '<style>' . $css . '</style></head>',
            $html
        );

        return Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->output();
    }
}
