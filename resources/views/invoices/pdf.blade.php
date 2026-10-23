
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        {{ $invoice->status === 'draft'
            ? 'Draft Invoice #' . $invoice->id
            : 'Invoice ' . $invoice->invoice_number }}
    </title>
</head>

<body>

{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td>
            <div class="brand">InvoiceFlow</div>
            <div class="brand-subtitle">
                PROFESSIONAL INVOICING
            </div>
        </td>

        <td class="invoice-heading">
            @if ($invoice->status === 'draft')
                <h1>DRAFT INVOICE</h1>
                <div class="invoice-number">
                    Draft #{{ $invoice->id }}
                </div>
            @else
                <h1>INVOICE</h1>
                <div class="invoice-number">
                    {{ $invoice->invoice_number }}
                </div>
            @endif
        </td>
    </tr>
</table>

<div class="divider"></div>
@if ($invoice->status === 'draft')
    <p style="text-align: center; font-weight: bold; color: #b45309;">
        DRAFT — Not an officially issued invoice.
        Do not use this document for payment.
    </p>
@endif

{{-- BUSINESS AND CUSTOMER DETAILS --}}
<table class="party-table">
    <tr>
        <td>
            <div class="section-label">From</div>

            <div class="party-name">
                {{ $businessProfile->business_name }}
            </div>

            <div class="party-info">
                @if($businessProfile->address)
                    {!! nl2br(e($businessProfile->address)) !!}
                    <br>
                @endif

                @if($businessProfile->email)
                    {{ $businessProfile->email }}<br>
                @endif

                @if($businessProfile->phone)
                    {{ $businessProfile->phone }}<br>
                @endif

                @if($businessProfile->vat_number)
                    VAT: {{ $businessProfile->vat_number }}
                @endif
            </div>
        </td>

        <td>
            <div class="section-label">Bill To</div>

            <div class="party-name">
                {{ $invoice->customer->name }}
            </div>

            <div class="party-info">
                @if($invoice->customer->company)
                    {{ $invoice->customer->company }}<br>
                @endif

                @if($invoice->customer->address)
                    {!! nl2br(e($invoice->customer->address)) !!}
                    <br>
                @endif

                @if($invoice->customer->email)
                    {{ $invoice->customer->email }}<br>
                @endif

                @if($invoice->customer->phone)
                    {{ $invoice->customer->phone }}<br>
                @endif

                @if($invoice->customer->vat_number)
                    VAT: {{ $invoice->customer->vat_number }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- INVOICE METADATA --}}
<div class="metadata">
    <table class="details-table">
        <tr>
            <td>
                <div class="detail-label">Invoice Date</div>
                <div class="detail-value">
                    {{ $invoice->invoice_date->format('d M Y') }}
                </div>
            </td>

            <td>
                <div class="detail-label">Due Date</div>
                <div class="detail-value">
                    {{ $invoice->due_date->format('d M Y') }}
                </div>
            </td>

            <td>
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    {{ ucfirst($invoice->displayStatus()) }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- INVOICE ITEMS --}}
<table class="items-table">
    <thead>
    <tr>
        <th style="width: 44%;">Description</th>
        <th class="number" style="width: 14%;">Quantity</th>
        <th class="number" style="width: 21%;">Unit Price</th>
        <th class="number" style="width: 21%;">Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach($invoice->lines as $line)
        <tr>
            <td>{{ $line->description }}</td>

            <td class="number">
                {{ number_format((float) $line->quantity, 2) }}
            </td>

            <td class="number">
                €{{ number_format((float) $line->unit_price, 2) }}
            </td>

            <td class="number">
                €{{ number_format(
                            (float) $line->quantity * (float) $line->unit_price,
                            2
                        ) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- FINANCIAL SUMMARY --}}
<table class="totals-table">
    <tr>
        <td>Subtotal</td>
        <td class="amount">
            €{{ number_format($invoice->subtotalCents() / 100, 2) }}
        </td>
    </tr>

    <tr>
        <td>VAT ({{ $invoice->vat_rate }}%)</td>
        <td class="amount">
            €{{ number_format($invoice->vatAmountCents() / 100, 2) }}
        </td>
    </tr>

    <tr class="grand-total">
        <td>Grand Total</td>
        <td class="amount">
            €{{ number_format($invoice->totalCents() / 100, 2) }}
        </td>
    </tr>

    <tr>
        <td>Payments Received</td>
        <td class="amount">
            €{{ number_format($invoice->paidCents() / 100, 2) }}
        </td>
    </tr>

    <tr class="balance-due">
        <td>Balance Due</td>
        <td class="amount">
            €{{ number_format($invoice->balanceCents() / 100, 2) }}
        </td>
    </tr>
</table>

{{-- PAYMENT INFORMATION --}}
@if($businessProfile->iban || $businessProfile->bic)
    <div class="payment-section">
        <h3>Payment Information</h3>

        @if($businessProfile->iban)
            <p>
                <strong>IBAN:</strong>
                {{ $businessProfile->iban }}
            </p>
        @endif

        @if($businessProfile->bic)
            <p>
                <strong>BIC:</strong>
                {{ $businessProfile->bic }}
            </p>
        @endif

        @if ($invoice->status !== 'draft')
            <p>
                <strong>Payment Reference:</strong>
                {{ $invoice->invoice_number }}
            </p>
        @endif
    </div>
@endif

{{-- FOOTER --}}
<div class="footer">
    Generated with InvoiceFlow
</div>

</body>
</html>
