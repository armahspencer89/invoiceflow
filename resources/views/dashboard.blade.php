
@extends('layouts.app')

@section('title', 'Dashboard - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, {{ auth()->user()->name }}!
                Here's an overview of your InvoiceFlow workspace.
            </p>
        </div>

        <a href="{{ route('invoices.create') }}" class="btn-primary">
            + Create Invoice
        </a>
    </div>

    <div class="card">
        <h2>Welcome to InvoiceFlow</h2>

        <p>
            Manage your customers, create invoices, and track payments
            from one place.
        </p>

        <div class="flex flex-wrap gap-3 mt-5">
            <a href="{{ route('customers.index') }}" class="btn-secondary">
                View Customers
            </a>

            <a href="{{ route('invoices.index') }}" class="btn-primary">
                View Invoices
            </a>
        </div>
    </div>

@endsection
