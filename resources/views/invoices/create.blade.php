@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Invoice</h1>

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <div>
                <label for="customer_id">Customer</label>

                <select name="customer_id" id="customer_id" required>
                    <option value="">Select a customer</option>

                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            @selected(old('customer_id') == $customer->id)>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>

                @error('customer_id')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="invoice_date">Invoice Date</label>

                <input
                    type="date"
                    name="invoice_date"
                    id="invoice_date"
                    value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                    required
                >

                @error('invoice_date')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="due_date">Due Date</label>

                <input
                    type="date"
                    name="due_date"
                    id="due_date"
                    value="{{ old('due_date') }}"
                    required
                >

                @error('due_date')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="vat_rate">VAT Rate (%)</label>

                <input
                    type="number"
                    name="vat_rate"
                    id="vat_rate"
                    value="{{ old('vat_rate', '21.00') }}"
                    step="0.01"
                    min="0"
                    max="100"
                    required
                >

                @error('vat_rate')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <h2>Invoice Items</h2>

            <div id="invoice-lines">
                <div class="invoice-line">
                    <div>
                        <label for="lines_0_description">Description</label>

                        <input
                            type="text"
                            name="lines[0][description]"
                            id="lines_0_description"
                            value="{{ old('lines.0.description') }}"
                            required
                        >

                        @error('lines.0.description')
                        <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lines_0_quantity">Quantity</label>

                        <input
                            type="number"
                            name="lines[0][quantity]"
                            id="lines_0_quantity"
                            value="{{ old('lines.0.quantity', '1.00') }}"
                            min="0.01"
                            step="0.01"
                            required
                        >

                        @error('lines.0.quantity')
                        <p>{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lines_0_unit_price">Unit Price (€)</label>

                        <input
                            type="number"
                            name="lines[0][unit_price]"
                            id="lines_0_unit_price"
                            value="{{ old('lines.0.unit_price') }}"
                            min="0"
                            step="0.01"
                            required
                        >

                        @error('lines.0.unit_price')
                        <p>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="button" id="add-line">Add Item</button>

            <button type="submit">Create Invoice</button>
        </form>
    </div>

    <script>
        let lineIndex = 1;

        document.getElementById('add-line').addEventListener('click', function () {
            const invoiceLines = document.getElementById('invoice-lines');

            const newLine = document.createElement('div');
            newLine.classList.add('invoice-line');

            newLine.innerHTML = `
            <div>
                <label for="lines_${lineIndex}_description">Description</label>

                <input
                    type="text"
                    name="lines[${lineIndex}][description]"
                    id="lines_${lineIndex}_description"
                    required
                >
            </div>

            <div>
                <label for="lines_${lineIndex}_quantity">Quantity</label>

                <input
                    type="number"
                    name="lines[${lineIndex}][quantity]"
                    id="lines_${lineIndex}_quantity"
                    value="1.00"
                    min="0.01"
                    step="0.01"
                    required
                >
            </div>

            <div>
                <label for="lines_${lineIndex}_unit_price">Unit Price (€)</label>

                <input
                    type="number"
                    name="lines[${lineIndex}][unit_price]"
                    id="lines_${lineIndex}_unit_price"
                    min="0"
                    step="0.01"
                    required
                >
            </div>

            <button type="button" class="remove-line">Remove Item</button>
        `;

            invoiceLines.appendChild(newLine);

            lineIndex++;
        });

        document.getElementById('invoice-lines').addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-line')) {
                event.target.closest('.invoice-line').remove();
            }
        });
    </script>
@endsection
