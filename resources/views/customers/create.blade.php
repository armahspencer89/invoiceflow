@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')
    <div>
        <h1>Create Customer</h1>

        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div>
                <label for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
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
                    value="{{ old('company') }}"
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
                    value="{{ old('email') }}"
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
                    value="{{ old('phone') }}"
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
                >{{ old('address') }}</textarea>

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
                    value="{{ old('vat_number') }}"
                >

                @error('vat_number')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Create Customer</button>
        </form>

        <a href="{{ route('customers.index') }}">Back to Customers</a>
    </div>
@endsection
