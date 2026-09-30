@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <div>
        <h1>Edit Customer</h1>

        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf

            {{-- HTML forms don't support PUT directly.
                 Laravel's method spoofing lets us send a PUT request. --}}
            @method('PUT')

            <div>
                <label for="name">Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $customer->name) }}"
                    required
                >

                @error('name')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="company">Company</label>

                <input
                    type="text"
                    id="company"
                    name="company"
                    value="{{ old('company', $customer->company) }}"
                >

                @error('company')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $customer->email) }}"
                    required
                >

                @error('email')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone">Phone</label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                >

                @error('phone')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address">Address</label>

                <textarea
                    id="address"
                    name="address"
                >{{ old('address', $customer->address) }}</textarea>

                @error('address')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="vat_number">VAT Number</label>

                <input
                    type="text"
                    id="vat_number"
                    name="vat_number"
                    value="{{ old('vat_number', $customer->vat_number) }}"
                >

                @error('vat_number')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Update Customer</button>
        </form>

        <a href="{{ route('customers.show', $customer) }}">
            Cancel
        </a>
    </div>
@endsection
