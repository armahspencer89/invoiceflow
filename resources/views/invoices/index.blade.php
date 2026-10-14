
@extends('layouts.app')

@section('title', 'Invoices - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Invoices</h1>

            <p class="page-subtitle">
                Manage invoices, track payments, and monitor outstanding balances.
            </p>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn-primary">
            + Create Invoice
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    <div class="card invoices-card">

        <!-- SEARCH AND FILTER TOOLBAR -->
        <form method="GET"
              action="{{ route('invoices.index') }}"
              class="invoices-filter-form">

            <div class="invoices-filter-grid">

                <div class="form-group">
                    <label for="search" class="form-label">
                        Search Invoices
                    </label>

                    <input
                        type="search"
                        name="search"
                        id="search"
                        class="form-input"
                        value="{{ request('search') }}"
                        placeholder="Invoice number or customer name"
                    >
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select"
                    >
                        <option value="">All statuses</option>

                        @foreach (['draft', 'sent', 'partial', 'paid', 'overdue'] as $status)
                            <option
                                value="{{ $status }}"
                                @selected(request('status') === $status)
                            >
                                {{ $status === 'partial' ? 'Partially Paid' : ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="invoices-filter-actions">
                    <button type="submit" class="btn-primary">
                        Apply Filters
                    </button>

                    <a href="{{ route('invoices.index') }}"
                       class="btn-secondary">
                        Clear
                    </a>
                </div>

            </div>
        </form>

        <!-- INVOICE LIST -->
        @if ($invoices->isEmpty())

            <div class="invoices-empty">

                <div class="invoices-empty-icon">
                    ▤
                </div>

                @if (request('search') || request('status'))

                    <h2>No matching invoices</h2>

                    <p>
                        No invoices match your current search or filters.
                        Try adjusting your criteria.
                    </p>

                    <a href="{{ route('invoices.index') }}"
                       class="btn-secondary">
                        Clear Filters
                    </a>

                @else

                    <h2>No invoices yet</h2>

                    <p>
                        Create your first invoice to start tracking
                        your business activity.
                    </p>

                    <a href="{{ route('invoices.create') }}"
                       class="btn-primary">
                        + Create Your First Invoice
                    </a>

                @endif

            </div>

        @else

            <div class="table-container">
                <table class="data-table invoices-table">

                    <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($invoices as $invoice)
                        @php
                            $status = strtolower($invoice->displayStatus());
                        @endphp

                        <tr>

                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="invoices-number">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>

                            <td>
                                {{ $invoice->customer->name }}
                            </td>

                            <td>
                                {{ $invoice->invoice_date->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>

                            <td class="invoices-money">
                                €{{ number_format($invoice->totalCents() / 100, 2) }}
                            </td>

                            <td class="invoices-money">
                                €{{ number_format($invoice->balanceCents() / 100, 2) }}
                            </td>

                            <td>
                                <span class="status-badge status-{{ $status }}">
                                    {{ $status === 'partial' ? 'Partially Paid' : ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="invoices-view-link">
                                    View →
                                </a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        @endif

        <!-- PAGINATION -->
        @if ($invoices->hasPages())
            <div class="invoices-pagination">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @endif

    </div>

@endsection
