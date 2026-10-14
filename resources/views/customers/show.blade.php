
@extends('layouts.app')

@section('title', 'Customer Details - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $customer->name }}</h1>

            <p class="page-subtitle">
                View and manage this customer's information.
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="btn-secondary">
            ← Back to Customers
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    <div class="customer-details-layout">

        <!-- CUSTOMER INFORMATION -->
        <section class="card customer-details-card">

            <div class="card-header">
                <div>
                    <h2 class="card-title">Customer Information</h2>
                    <p class="card-description">
                        Contact and business details.
                    </p>
                </div>

                @if ($customer->is_active)
                    <span class="status-badge customers-active">
                    Active
                </span>
                @else
                    <span class="status-badge customers-inactive">
                    Inactive
                </span>
                @endif
            </div>

            <dl class="customer-details-grid">

                <div class="customer-detail-item">
                    <dt>Customer Name</dt>
                    <dd>{{ $customer->name }}</dd>
                </div>

                <div class="customer-detail-item">
                    <dt>Company</dt>
                    <dd>{{ $customer->company ?: '—' }}</dd>
                </div>

                <div class="customer-detail-item">
                    <dt>Email Address</dt>
                    <dd>
                        <a href="mailto:{{ $customer->email }}">
                            {{ $customer->email }}
                        </a>
                    </dd>
                </div>

                <div class="customer-detail-item">
                    <dt>Phone Number</dt>
                    <dd>
                        @if ($customer->phone)
                            <a href="tel:{{ $customer->phone }}">
                                {{ $customer->phone }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>

                <div class="customer-detail-item customer-detail-full">
                    <dt>Address</dt>
                    <dd class="customer-address">
                        {{ $customer->address ?: '—' }}
                    </dd>
                </div>

                <div class="customer-detail-item customer-detail-full">
                    <dt>VAT Number</dt>
                    <dd>{{ $customer->vat_number ?: '—' }}</dd>
                </div>

            </dl>

        </section>

        <!-- CUSTOMER ACTIONS -->
        <section class="card customer-actions-card">

            <h2 class="card-title">Manage Customer</h2>

            <p class="card-description">
                Update this customer's information or change
                their account status.
            </p>

            <div class="customer-actions-list">

                <a href="{{ route('customers.edit', $customer) }}"
                   class="btn-primary customer-action-button">
                    Edit Customer
                </a>

                @if ($customer->is_active)

                    <form method="POST"
                          action="{{ route('customers.deactivate', $customer) }}"
                          onsubmit="return confirm('Deactivate this customer?')">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="btn-secondary customer-action-button">
                            Deactivate Customer
                        </button>
                    </form>

                @else

                    <form method="POST"
                          action="{{ route('customers.reactivate', $customer) }}">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="btn-secondary customer-action-button">
                            Reactivate Customer
                        </button>
                    </form>

                @endif

            </div>

            <div class="customer-danger-zone">

                <h3>Danger Zone</h3>

                <p>
                    Permanently delete this customer.
                    This action cannot be undone.
                </p>

                <form method="POST"
                      action="{{ route('customers.destroy', $customer) }}"
                      onsubmit="return confirm('Are you sure you want to permanently delete this customer?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn-danger customer-action-button">
                        Delete Customer
                    </button>

                </form>

            </div>

        </section>

    </div>

@endsection
