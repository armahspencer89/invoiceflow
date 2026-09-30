@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div>
        <h1>Customers</h1>

        <form method="GET" action="{{ route('customers.index') }}">
            <label for="search">Search customers</label>

            <input
                type="search"
                id="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by name, company, or email"
            >

            <button type="submit">Search</button>

            @if (request('search'))
                <a href="{{ route('customers.index') }}">
                    Clear
                </a>
            @endif
        </form>

        @if ($customers->isEmpty())
            @if (request('search'))
                <p>No customers matched your search.</p>
            @else
                <p>You don't have any customers yet.</p>
            @endif
        @else
            <ul>
                @foreach ($customers as $customer)
                    <li>
                        {{-- The customer model is passed to the named route.
                             Laravel uses its ID to generate /customers/{customer}. --}}
                        <a href="{{ route('customers.show', $customer) }}">
                            {{ $customer->name }}
                        </a>

                        @if ($customer->company)
                            — {{ $customer->company }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
