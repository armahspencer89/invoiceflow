
@extends('layouts.app')

@section('title', 'Edit Customer - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Customer</h1>

            <p class="page-subtitle">
                Update the contact and business information for
                {{ $customer->name }}.
            </p>
        </div>

        <a href="{{ route('customers.show', $customer) }}"
           class="btn-secondary">
            ← Back to Customer
        </a>
    </div>

    <div class="card customer-form-card">

        <div class="customer-form-heading">
            <h2 class="card-title">Customer Information</h2>

            <p class="card-description">
                Update the details below.
                Fields marked with * are required.
            </p>
        </div>

        <form method="POST"
              action="{{ route('customers.update', $customer) }}">

            @csrf
            @method('PUT')

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
                        value="{{ old('name', $customer->name) }}"
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
                        value="{{ old('company', $customer->company) }}"
                        placeholder="Company name (optional)"
                        autocomplete="organization"
                        aria-invalid="{{ $errors->has('company') ? 'true' : 'false' }}"
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
                        value="{{ old('email', $customer->email) }}"
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
                        value="{{ old('phone', $customer->phone) }}"
                        placeholder="+32 123 45 67 89"
                        autocomplete="tel"
                        aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
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
                        aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}"
                    >{{ old('address', $customer->address) }}</textarea>

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
                        value="{{ old('vat_number', $customer->vat_number) }}"
                        placeholder="e.g. BE0123456789"
                        aria-invalid="{{ $errors->has('vat_number') ? 'true' : 'false' }}"
                    >

                    @error('vat_number')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- FORM ACTIONS -->
            <div class="customer-form-actions">

                <a href="{{ route('customers.show', $customer) }}"
                   class="btn-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn-primary">
                    Save Changes
                </button>

            </div>

        </form>

    </div>

@endsection
