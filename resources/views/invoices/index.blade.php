
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Invoices</h1>

        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        {{-- Search and filter invoices --}}
        <form method="GET" action="{{ route('invoices.index') }}">
            <div>
                <label for="search">Search invoices</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Invoice number or customer name"
                >
            </div>

            <div>
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">All statuses</option>

                    @foreach (['draft', 'sent', 'paid', 'overdue'] as $status)
                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit">Filter</button>
            <a href="{{ route('invoices.index') }}">Clear Filters</a>
        </form>

        {{-- Invoice list --}}
        @if ($invoices->isEmpty())
            <p>No invoices found.</p>
        @else
            <table>
                <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Customer</th>
                    <th>Invoice Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>VAT Rate</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>

                        <td>{{ $invoice->customer->name }}</td>
                        <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($invoice->status) }}</td>
                        <td>{{ $invoice->vat_rate }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        {{-- Pagination --}}
        {{ $invoices->links() }}
    </div>
@endsection
