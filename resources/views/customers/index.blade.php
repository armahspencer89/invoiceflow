
@extends('layouts.app')

@section('title', 'Customers - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Customers</h1>

            <p class="page-subtitle">
                Manage your customers and keep their information organized.
            </p>
        </div>

        <a href="{{ route('customers.create') }}" class="btn-primary">
            + Add Customer
        </a>
    </div>

    <div class="card customers-card">

        <!-- SEARCH TOOLBAR -->
        <div class="customers-toolbar">

            <form method="GET"
                  action="{{ route('customers.index') }}"
                  class="customers-search-form">

                <label for="search" class="form-label">
                    Search customers
                </label>

                <div class="customers-search-controls">
                    <input
                        type="search"
                        id="search"
                        name="search"
                        class="form-input"
                        value="{{ request('search') }}"
                        placeholder="Search by name, company, or email"
                    >

                    <button type="submit" class="btn-primary">
                        Search
                    </button>

                    @if (request('search'))
                        <a href="{{ route('customers.index') }}"
                           class="btn-secondary">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

        </div>

        @if ($customers->isEmpty())

            <!-- EMPTY STATE -->
            <div class="customers-empty">

                <div class="customers-empty-icon">
                    ♙
                </div>

                @if (request('search'))

                    <h2>No customers found</h2>

                    <p>
                        No customers matched your search.
                        Try another name, company, or email address.
                    </p>

                    <a href="{{ route('customers.index') }}"
                       class="btn-secondary">
                        Clear Search
                    </a>

                @else

                    <h2>No customers yet</h2>

                    <p>
                        Add your first customer to start creating
                        and managing invoices.
                    </p>

                    <a href="{{ route('customers.create') }}"
                       class="btn-primary">
                        + Add Your First Customer
                    </a>

                @endif

            </div>

        @else

            <!-- CUSTOMER TABLE -->
            <div class="table-container">
                <table class="data-table customers-table">

                    <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($customers as $customer)
                        <tr>

                            <td>
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="customers-name">
                                    {{ $customer->name }}
                                </a>
                            </td>

                            <td>
                                {{ $customer->company ?: '—' }}
                            </td>

                            <td>
                                @if ($customer->email)
                                    <a href="mailto:{{ $customer->email }}"
                                       class="customers-email">
                                        {{ $customer->email }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                {{ $customer->phone ?: '—' }}
                            </td>

                            <td>
                                @if ($customer->is_active)
                                    <span class="status-badge customers-active">
                                        Active
                                    </span>
                                @else
                                    <span class="status-badge customers-inactive">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="customers-view-link">
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
