<?php

namespace App\Mail;

use App\Models\BusinessProfile;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public BusinessProfile $businessProfile
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice ' . $this->invoice->invoice_number
            . ' from ' . $this->businessProfile->business_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => app(InvoicePdfService::class)->generate(
                    $this->invoice,
                    $this->businessProfile
                ),
                $this->invoice->invoice_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
