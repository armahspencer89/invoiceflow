
@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

{{-- Invoice status action --}}
@if ($invoice->status === 'draft')
    <form method="POST" action="{{ route('invoices.send', $invoice) }}">
        @csrf
        @method('PATCH')

        <button
            type="submit"
            onclick="return confirm('Mark this invoice as sent? You will no longer be able to edit it.')"
        >
            Mark as Sent
        </button>
    </form>
@endif

@if ($invoice->status === 'draft')
    <form method="POST"
          action="{{ route('invoices.destroy', $invoice) }}"
          onsubmit="return confirm('Are you sure you want to delete this invoice? This cannot be undone.')">

        @csrf
        @method('DELETE')

        <button type="submit">Delete Invoice</button>
    </form>
@endif

<h2>Invoice Items</h2>

<table>
    <thead>
    <tr>
        <th>Description</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Line Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoice->lines as $line)
        <tr>
            <td>{{ $line->description }}</td>
            <td>{{ $line->quantity }}</td>
            <td>€{{ number_format((float) $line->unit_price, 2) }}</td>
            <td>
                €{{ number_format((float) $line->quantity * (float) $line->unit_price, 2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div>
    <p>
        <strong>Subtotal:</strong>
        €{{ number_format($invoice->subtotal(), 2) }}
    </p>

    <p>
        <strong>VAT ({{ $invoice->vat_rate }}%):</strong>
        €{{ number_format($invoice->vatAmount(), 2) }}
    </p>

    <p>
        <strong>Total:</strong>
        €{{ number_format($invoice->total(), 2) }}
    </p>
</div>
