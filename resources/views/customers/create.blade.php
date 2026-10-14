
@extends('layouts.app')

@section('title', 'Create Customer - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Create Customer</h1>

            <p class="page-subtitle">
                Add a new customer to your InvoiceFlow workspace.
            </p>
        </div>

        <a href="{{ route('customers.index') }}"
           class="btn-secondary">
            ← Back to Customers
        </a>
    </div>

    <div class="card customer-form-card">

        <div class="customer-form-heading">
            <h2 class="card-title">Customer Information</h2>

            <p class="card-description">
                Enter your customer's contact and business details.
                Fields marked with * are required.
            </p>
        </div>

        <form method="POST"
              action="{{ route('customers.store') }}">

            @csrf

            <div class="customer-form-grid">

                <!-- NAME -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Customer Name *
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="{{ old('name') }}"
                        placeholder="Enter customer name"
                        autocomplete="name"
                        required
                        aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                    >

                    @error('name')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- COMPANY -->
                <div class="form-group">
                    <label for="company" class="form-label">
                        Company
                    </label>

                    <input
                        type="text"
                        id="company"
                        name="company"
                        class="form-input"
                        value="{{ old('company') }}"
                        placeholder="Company name (optional)"
                        autocomplete="organization"
                    >

                    @error('company')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- EMAIL -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address *
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        placeholder="customer@example.com"
                        autocomplete="email"
                        required
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    >

                    @error('email')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PHONE -->
                <div class="form-group">
                    <label for="phone" class="form-label">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        value="{{ old('phone') }}"
                        placeholder="+32 123 45 67 89"
                        autocomplete="tel"
                    >

                    @error('phone')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ADDRESS -->
                <div class="form-group customer-form-full">
                    <label for="address" class="form-label">
                        Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        class="form-textarea"
                        rows="3"
                        placeholder="Street, house number, postal code, city, country"
                        autocomplete="street-address"
                    >{{ old('address') }}</textarea>

                    @error('address')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- VAT NUMBER -->
                <div class="form-group customer-form-full">
                    <label for="vat_number" class="form-label">
                        VAT Number
                    </label>

                    <input
                        type="text"
                        id="vat_number"
                        name="vat_number"
                        class="form-input"
                        value="{{ old('vat_number') }}"
                        placeholder="e.g. BE0123456789"
                    >

                    @error('vat_number')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- FORM ACTIONS -->
            <div class="customer-form-actions">

                <a href="{{ route('customers.index') }}"
                   class="btn-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn-primary">
                    Create Customer
                </button>

            </div>

        </form>

    </div>

@endsection
