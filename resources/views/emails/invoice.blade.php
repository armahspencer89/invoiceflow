<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
</head>
<body>
<p>Hello {{ $invoice->customer->name }},</p>

<p>
    Please find the details of invoice
    <strong>{{ $invoice->invoice_number }}</strong>
    from {{ $businessProfile->business_name }}.
</p>

<p>
    <strong>Invoice date:</strong>
    {{ $invoice->invoice_date->format('d/m/Y') }}
</p>

<p>
    <strong>Due date:</strong>
    {{ $invoice->due_date->format('d/m/Y') }}
</p>

<p>
    <strong>Total amount:</strong>
    €{{ number_format($invoice->totalCents() / 100, 2) }}
</p>

<p>
    <strong>Outstanding balance:</strong>
    €{{ number_format($invoice->balanceCents() / 100, 2) }}
</p>

@if ($businessProfile->iban)
    <p>
        <strong>IBAN:</strong>
        {{ $businessProfile->iban }}
    </p>
@endif

<p>
    Please use the invoice number as the payment reference.
    The PDF invoice will be attached to this email.
</p>

<p>
    Kind regards,<br>
    {{ $businessProfile->business_name }}
</p>
</body>
</html>
