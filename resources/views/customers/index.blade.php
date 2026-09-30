@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div>
        <h1>Customers</h1>

        @if ($customers->isEmpty())
            <p>You don't have any customers yet.</p>
        @else
            <ul>
                @foreach ($customers as $customer)
                    <li>
                        {{ $customer->name }}

                        @if ($customer->company)
                            — {{ $customer->company }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
