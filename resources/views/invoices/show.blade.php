
@extends('layouts.app')

@section('title', 'Invoice Details - InvoiceFlow')

@section('content')

    @php
        $status = strtolower($invoice->displayStatus());
        $totalCents = $invoice->totalCents();
        $paidCents = $invoice->paidCents();
        $balanceCents = $invoice->balanceCents();

        $money = fn ($cents) => '€' . number_format($cents / 100, 2);
    @endphp

        <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Invoice {{ $invoice->invoice_number }}
            </h1>

            <p class="page-subtitle">
                View invoice details, track payments, and manage its status.
            </p>
        </div>

        <a href="{{ route('invoices.index') }}" class="btn-secondary">
            ← Back to Invoices
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($invoice->isOverdue())
        <div class="alert alert-warning" role="alert">
            <strong>Payment overdue!</strong>
            This invoice was due on
            {{ $invoice->due_date->format('d M Y') }}.
        </div>
    @endif

    <!-- INVOICE SUMMARY -->
    <div class="invoice-detail-layout">

        <section class="card invoice-detail-card">

            <div class="card-header">
                <div>
                    <h2 class="card-title">Invoice Information</h2>
                    <p class="card-description">
                        General information about this invoice.
                    </p>
                </div>

                <span class="status-badge status-{{ $status }}">
                {{ $status === 'partial' ? 'Partially Paid' : ucfirst($status) }}
            </span>
            </div>

            <dl class="invoice-detail-grid">

                <div class="invoice-detail-item">
                    <dt>Invoice Number</dt>
                    <dd>{{ $invoice->invoice_number }}</dd>
                </div>

                <div class="invoice-detail-item">
                    <dt>Customer</dt>
                    <dd>
                        <a href="{{ route('customers.show', $invoice->customer) }}">
                            {{ $invoice->customer->name }}
                        </a>
                    </dd>
                </div>

                <div class="invoice-detail-item">
                    <dt>Invoice Date</dt>
                    <dd>{{ $invoice->invoice_date->format('d M Y') }}</dd>
                </div>

                <div class="invoice-detail-item">
                    <dt>Due Date</dt>
                    <dd>{{ $invoice->due_date->format('d M Y') }}</dd>
                </div>

                <div class="invoice-detail-item">
                    <dt>VAT Rate</dt>
                    <dd>{{ $invoice->vat_rate }}%</dd>
                </div>

                <div class="invoice-detail-item">
                    <dt>Payment Status</dt>
                    <dd>
                        {{ $status === 'partial' ? 'Partially Paid' : ucfirst($status) }}
                    </dd>
                </div>

            </dl>

        </section>

        <!-- INVOICE ACTIONS -->
        <section class="card invoice-actions-card">

            <h2 class="card-title">Invoice Actions</h2>

            <p class="card-description">
                Manage this invoice based on its current status.
            </p>

            @if ($invoice->status === 'draft')

                <div class="invoice-actions-list">

                    <a href="{{ route('invoices.edit', $invoice) }}"
                       class="btn-secondary invoice-action-button">
                        Edit Invoice
                    </a>

                    <form method="POST"
                          action="{{ route('invoices.send', $invoice) }}"
                          onsubmit="return confirm('Mark this invoice as sent? You will no longer be able to edit it.')">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="btn-primary invoice-action-button">
                            Mark as Sent
                        </button>
                    </form>

                </div>

                <div class="invoice-danger-zone">
                    <h3>Danger Zone</h3>

                    <p>
                        Permanently delete this draft invoice.
                        This action cannot be undone.
                    </p>

                    <form method="POST"
                          action="{{ route('invoices.destroy', $invoice) }}"
                          onsubmit="return confirm('Are you sure you want to delete this invoice? This cannot be undone.')">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn-danger invoice-action-button">
                            Delete Invoice
                        </button>
                    </form>
                </div>

            @else

                <div class="invoice-status-message">
                    @if ($invoice->status === 'paid')
                        This invoice has been fully paid.
                    @else
                        This invoice has been issued and can no longer be edited.
                    @endif
                </div>

            @endif

        </section>

    </div>

    <!-- INVOICE ITEMS -->
    <section class="card invoice-detail-section">

        <div class="invoice-section-heading">
            <h2 class="card-title">Invoice Items</h2>
            <p class="card-description">
                Products and services included in this invoice.
            </p>
        </div>

        <div class="table-container">
            <table class="data-table invoice-items-table">

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
                        <td>
                            €{{ number_format((float) $line->unit_price, 2) }}
                        </td>
                        <td class="invoices-money">
                            €{{ number_format(
                                (float) $line->quantity * (float) $line->unit_price,
                                2
                            ) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        <!-- TOTALS -->
        <div class="invoice-detail-totals">

            <div class="invoice-total-row">
                <span>Subtotal</span>
                <strong>{{ $money($invoice->subtotalCents()) }}</strong>
            </div>

            <div class="invoice-total-row">
                <span>VAT ({{ $invoice->vat_rate }}%)</span>
                <strong>{{ $money($invoice->vatAmountCents()) }}</strong>
            </div>

            <div class="invoice-total-row invoice-grand-total">
                <span>Invoice Total</span>
                <strong>{{ $money($totalCents) }}</strong>
            </div>

        </div>

    </section>

    <!-- PAYMENT MANAGEMENT -->
    <section class="card invoice-detail-section">

        <div class="invoice-section-heading">
            <h2 class="card-title">Payment Management</h2>
            <p class="card-description">
                Track payments and monitor the outstanding balance.
            </p>
        </div>

        <div class="invoice-payment-summary">

            <div class="invoice-payment-stat">
                <span>Invoice Total</span>
                <strong>{{ $money($totalCents) }}</strong>
            </div>

            <div class="invoice-payment-stat">
                <span>Total Paid</span>
                <strong>{{ $money($paidCents) }}</strong>
            </div>

            <div class="invoice-payment-stat invoice-payment-balance">
                <span>Outstanding Balance</span>
                <strong>{{ $money($balanceCents) }}</strong>
            </div>

        </div>

        @if ($invoice->status === 'sent' && $balanceCents > 0)

            <!-- RECORD PAYMENT -->
            <div class="invoice-payment-form-section">

                <h3>Record Payment</h3>

                <p class="card-description">
                    Record a full or partial payment received from the customer.
                </p>

                <form method="POST"
                      action="{{ route('invoices.payments.store', $invoice) }}">

                    @csrf

                    <div class="invoice-payment-form-grid">

                        <div class="form-group">
                            <label for="amount" class="form-label">
                                Payment Amount (€) *
                            </label>

                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                class="form-input"
                                step="0.01"
                                min="0.01"
                                max="{{ \App\Support\Money::format($balanceCents) }}"
                                value="{{ old('amount') }}"
                                placeholder="0.00"
                                required
                                aria-invalid="{{ $errors->has('amount') ? 'true' : 'false' }}"
                            >

                            @error('amount')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_date" class="form-label">
                                Payment Date *
                            </label>

                            <input
                                type="date"
                                id="payment_date"
                                name="payment_date"
                                class="form-input"
                                value="{{ old('payment_date', now()->toDateString()) }}"
                                max="{{ now()->toDateString() }}"
                                required
                                aria-invalid="{{ $errors->has('payment_date') ? 'true' : 'false' }}"
                            >

                            @error('payment_date')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="method" class="form-label">
                                Payment Method *
                            </label>

                            <select id="method"
                                    name="method"
                                    class="form-select"
                                    required
                                    aria-invalid="{{ $errors->has('method') ? 'true' : 'false' }}">

                                <option value="">Select payment method</option>

                                @foreach ([
                                    'bank_transfer' => 'Bank Transfer',
                                    'cash' => 'Cash',
                                    'card' => 'Card',
                                    'other' => 'Other'
                                ] as $value => $label)
                                    <option value="{{ $value }}"
                                        @selected(old('method') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach

                            </select>

                            @error('method')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reference" class="form-label">
                                Payment Reference
                            </label>

                            <input
                                type="text"
                                id="reference"
                                name="reference"
                                class="form-input"
                                maxlength="255"
                                value="{{ old('reference') }}"
                                placeholder="Optional transaction reference"
                            >

                            @error('reference')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group invoice-payment-full">
                            <label for="notes" class="form-label">
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                class="form-textarea"
                                rows="3"
                                maxlength="2000"
                                placeholder="Optional payment notes"
                            >{{ old('notes') }}</textarea>

                            @error('notes')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="invoice-payment-actions">
                        <button type="submit" class="btn-primary">
                            Record Payment
                        </button>
                    </div>

                </form>

            </div>

        @elseif ($invoice->status === 'paid')

            <div class="invoice-status-message">
                This invoice has been fully paid.
            </div>

        @elseif ($invoice->status === 'draft')

            <div class="invoice-status-message">
                Mark this invoice as Sent before recording payments.
            </div>

        @endif

    </section>

    <!-- PAYMENT HISTORY -->
    <section class="card invoice-detail-section">

        <div class="invoice-section-heading">
            <h2 class="card-title">Payment History</h2>
            <p class="card-description">
                All payments recorded for this invoice.
            </p>
        </div>

        @if ($invoice->payments->isEmpty())

            <div class="invoice-history-empty">
                No payments have been recorded for this invoice.
            </div>

        @else

            <div class="table-container">
                <table class="data-table invoice-history-table">

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

                            <td class="invoices-money">
                                {{ $money($payment->amount_cents) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        @endif

    </section>

@endsection
