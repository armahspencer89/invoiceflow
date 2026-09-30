@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
    <div>
        <h1>Customer Details</h1>

        {{-- Display the customer's stored information. --}}
        <p><strong>Name:</strong> {{ $customer->name }}</p>

        @if ($customer->company)
            <p><strong>Company:</strong> {{ $customer->company }}</p>
        @endif

        <p><strong>Email:</strong> {{ $customer->email }}</p>

        @if ($customer->phone)
            <p><strong>Phone:</strong> {{ $customer->phone }}</p>
        @endif

        @if ($customer->address)
            <p><strong>Address:</strong> {{ $customer->address }}</p>
        @endif

        @if ($customer->vat_number)
            <p><strong>VAT Number:</strong> {{ $customer->vat_number }}</p>
        @endif

        <div>
            <strong>Status:</strong>

            @if ($customer->is_active)
                Active
            @else
                Inactive
            @endif
        </div>

        {{-- We'll add the customer's invoices here later. --}}
        <a href="{{ route('customers.edit', $customer) }}">
            Edit Customer
        </a>

        @if ($customer->is_active)
            <form
                method="POST"
                action="{{ route('customers.deactivate', $customer) }}"
            >
                @csrf

                {{-- HTML forms only support GET and POST.
                     Laravel's method spoofing lets us send a PATCH request. --}}
                @method('PATCH')

                <button type="submit">
                    Deactivate Customer
                </button>
            </form>
        @else
            <form
                method="POST"
                action="{{ route('customers.reactivate', $customer) }}"
            >
                @csrf

                {{-- Laravel's method spoofing lets this HTML form send a PATCH request. --}}
                @method('PATCH')

                <button type="submit">
                    Reactivate Customer
                </button>
            </form>
        @endif

        <form
            method="POST"
            action="{{ route('customers.destroy', $customer) }}"
        >
            @csrf

            {{-- HTML forms do not support DELETE directly.
                 Laravel's method spoofing allows us to send a DELETE request. --}}
            @method('DELETE')

            <button type="submit">
                Delete Customer
            </button>
        </form>

        <a href="{{ route('customers.index') }}">
            Back to Customers
        </a>
    </div>
@endsection
