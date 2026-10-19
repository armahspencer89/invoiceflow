@extends('layouts.app')

@section('title', 'Business Profile - InvoiceFlow')

@section('content')
    <div class="business-profile-page">

        <div class="business-profile-heading">
            <div>
                <h1>Business Profile</h1>
                <p>
                    Manage the business information that will appear
                    on your invoices.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="business-profile-alert business-profile-alert-success"
                 role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="business-profile-alert business-profile-alert-error"
                 role="alert">
                <strong>Please correct the following errors:</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('business-profile.update') }}"
              class="business-profile-form">

            @csrf
            @method('PUT')

            <section class="business-profile-section">
                <h2>Business Information</h2>
                <p>Enter the details of the business issuing invoices.</p>

                <div class="business-profile-fields">

                    <div class="business-profile-field">
                        <label for="business_name">
                            Business Name <span aria-hidden="true">*</span>
                        </label>

                        <input
                            type="text"
                            id="business_name"
                            name="business_name"
                            value="{{ old('business_name', $profile?->business_name) }}"
                            maxlength="255"
                            required
                            aria-invalid="{{ $errors->has('business_name') ? 'true' : 'false' }}"
                        >

                        @error('business_name')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="business-profile-field">
                        <label for="email">Business Email</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $profile?->email) }}"
                            maxlength="255"
                        >

                        @error('email')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="business-profile-field">
                        <label for="phone">Phone Number</label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $profile?->phone) }}"
                            maxlength="50"
                        >

                        @error('phone')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="business-profile-field">
                        <label for="vat_number">VAT Number</label>

                        <input
                            type="text"
                            id="vat_number"
                            name="vat_number"
                            value="{{ old('vat_number', $profile?->vat_number) }}"
                            maxlength="50"
                            placeholder="e.g. BE0123456789"
                        >

                        @error('vat_number')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="business-profile-field business-profile-full">
                        <label for="address">Business Address</label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            maxlength="2000"
                            placeholder="Street, number, postal code, city, country"
                        >{{ old('address', $profile?->address) }}</textarea>

                        @error('address')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </section>

            <section class="business-profile-section">
                <h2>Banking Information</h2>
                <p>
                    These details can be displayed on invoices
                    to help customers make payments.
                </p>

                <div class="business-profile-fields">

                    <div class="business-profile-field">
                        <label for="iban">IBAN</label>

                        <input
                            type="text"
                            id="iban"
                            name="iban"
                            value="{{ old('iban', $profile?->iban) }}"
                            maxlength="34"
                            placeholder="BE00 0000 0000 0000"
                        >

                        @error('iban')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="business-profile-field">
                        <label for="bic">BIC / SWIFT</label>

                        <input
                            type="text"
                            id="bic"
                            name="bic"
                            value="{{ old('bic', $profile?->bic) }}"
                            maxlength="11"
                        >

                        @error('bic')
                        <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </section>

            <div class="business-profile-actions">
                <button type="submit" class="business-profile-save">
                    Save Business Profile
                </button>
            </div>

        </form>

    </div>
@endsection
