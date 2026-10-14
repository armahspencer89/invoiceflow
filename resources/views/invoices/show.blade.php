
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

    {{-- Payment Management --}}

    <hr>

    <h2>Payment Management</h2>

    <div>
        <p>
            <strong>Invoice Total:</strong>
            €{{ number_format($invoice->totalCents() / 100, 2) }}
        </p>

        <p>
            <strong>Total Paid:</strong>
            €{{ number_format($invoice->paidCents() / 100, 2) }}
        </p>

        <p>
            <strong>Outstanding Balance:</strong>
            €{{ number_format($invoice->balanceCents() / 100, 2) }}
        </p>
    </div>

    {{-- Payment Form --}}

    @if ($invoice->status === 'sent' && $invoice->balanceCents() > 0)

        <h3>Record Payment</h3>

        @if ($errors->any())
            <div role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('invoices.payments.store', $invoice) }}"
        >
            @csrf

            <div>
                <label for="amount">Payment Amount (€)</label>

                <input
                    type="number"
                    id="amount"
                    name="amount"
                    step="0.01"
                    min="0.01"
                    max="{{ \App\Support\Money::format($invoice->balanceCents()) }}"
                    value="{{ old('amount') }}"
                    required
                >

                @error('amount')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="payment_date">Payment Date</label>

                <input
                    type="date"
                    id="payment_date"
                    name="payment_date"
                    value="{{ old('payment_date', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    required
                >
            </div>

            <div>
                <label for="method">Payment Method</label>

                <select id="method" name="method" required>
                    <option value="">Select payment method</option>

                    <option value="bank_transfer"
                        @selected(old('method') === 'bank_transfer')>
                        Bank Transfer
                    </option>

                    <option value="cash"
                        @selected(old('method') === 'cash')>
                        Cash
                    </option>

                    <option value="card"
                        @selected(old('method') === 'card')>
                        Card
                    </option>

                    <option value="other"
                        @selected(old('method') === 'other')>
                        Other
                    </option>
                </select>
            </div>

            <div>
                <label for="reference">Payment Reference (Optional)</label>

                <input
                    type="text"
                    id="reference"
                    name="reference"
                    maxlength="255"
                    value="{{ old('reference') }}"
                >
            </div>

            <div>
                <label for="notes">Notes (Optional)</label>

                <textarea
                    id="notes"
                    name="notes"
                    maxlength="2000"
                >{{ old('notes') }}</textarea>
            </div>

            <button type="submit">
                Record Payment
            </button>
        </form>

    @elseif ($invoice->status === 'paid')

        <p><strong>This invoice has been fully paid.</strong></p>

    @elseif ($invoice->status === 'draft')

        <p>
            Mark this invoice as Sent before recording payments.
        </p>

    @endif

    {{-- Payment History --}}

    <h3>Payment History</h3>

    @if ($invoice->payments->isEmpty())

        <p>No payments have been recorded for this invoice.</p>

    @else

        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Amount</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($invoice->payments as $payment)
                <tr>
                    <td>
                        {{ $payment->payment_date->format('d M Y') }}
                    </td>

                    <td>
                        {{ ucwords(str_replace('_', ' ', $payment->method)) }}
                    </td>

                    <td>
                        {{ $payment->reference ?? '—' }}
                    </td>

                    <td>
                        €{{ number_format($payment->amount_cents / 100, 2) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif
</div>
