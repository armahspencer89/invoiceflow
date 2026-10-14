
@extends('layouts.app')

@section('title', 'Dashboard - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>

            <p class="page-subtitle">
                Welcome back, {{ auth()->user()->name }}!
                Here's an overview of your business.
            </p>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn-primary">
            + Create Invoice
        </a>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="dashboard-stats">

        <div class="card dashboard-stat">
            <span class="dashboard-stat-label">Total Invoices</span>

            <div class="dashboard-stat-value">
                {{ $totalInvoices }}
            </div>

            <span class="dashboard-stat-description">
            All invoices created
        </span>
        </div>

        <div class="card dashboard-stat">
            <span class="dashboard-stat-label">Outstanding Balance</span>

            <div class="dashboard-stat-value">
                €{{ number_format($outstandingCents / 100, 2) }}
            </div>

            <span class="dashboard-stat-description">
            Unpaid balance on issued invoices
        </span>
        </div>

        <div class="card dashboard-stat">
            <span class="dashboard-stat-label">Overdue Invoices</span>

            <div class="dashboard-stat-value">
                {{ $overdueInvoices }}
            </div>

            <span class="dashboard-stat-description">
            Invoices past their due date
        </span>
        </div>

        <div class="card dashboard-stat">
            <span class="dashboard-stat-label">Payments Received</span>

            <div class="dashboard-stat-value">
                €{{ number_format($paymentsReceivedCents / 100, 2) }}
            </div>

            <span class="dashboard-stat-description">
            Total recorded payments
        </span>
        </div>

    </div>

    <!-- RECENT INVOICES -->
    <div class="card dashboard-recent">

        <div class="card-header">
            <div>
                <h2 class="card-title">Recent Invoices</h2>
                <p class="card-description">
                    Your five most recently created invoices.
                </p>
            </div>

            <a href="{{ route('invoices.index') }}" class="btn-secondary">
                View All
            </a>
        </div>

        @if ($recentInvoices->isEmpty())

            <div class="dashboard-empty">
                <h3>No invoices yet</h3>

                <p>
                    Create your first invoice to start tracking
                    your business activity.
                </p>

                <a href="{{ route('invoices.create') }}" class="btn-primary">
                    Create Invoice
                </a>
            </div>

        @else

            <div class="table-container">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($recentInvoices as $invoice)
                        <tr>
                            <td>
                                {{ $invoice->invoice_number }}
                            </td>

                            <td>
                                {{ $invoice->customer->name }}
                            </td>

                            <td>
                                {{ $invoice->due_date->format('d M Y') }}
                            </td>

                            <td>
                                €{{ number_format($invoice->totalCents() / 100, 2) }}
                            </td>

                            <td>
                                @php
                                    $status = strtolower($invoice->displayStatus());
                                @endphp

                                <span class="status-badge status-{{ $status }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}">
                                    View →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </div>

@endsection
