@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Invoice {{ $invoice->invoice_number }}</h1>

        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            <div>
                <label for="customer_id">Customer</label>

                <select name="customer_id" id="customer_id" required>
                    @foreach ($customers as $customer)
                        <option
                            value="{{ $customer->id }}"
                            @selected(old('customer_id', $invoice->customer_id) == $customer->id)
                        >
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
                    value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}"
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
                    value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}"
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
                    value="{{ old('vat_rate', $invoice->vat_rate) }}"
                    min="0"
                    max="100"
                    step="0.01"
                    required
                >

                @error('vat_rate')
                <p>{{ $message }}</p>
                @enderror
            </div>

            <h2>Invoice Items</h2>

            <div id="invoice-lines">
                @foreach ($invoice->lines as $index => $line)
                    <div class="invoice-line">
                        <div>
                            <label>Description</label>

                            <input
                                type="text"
                                name="lines[{{ $index }}][description]"
                                value="{{ old("lines.$index.description", $line->description) }}"
                                required
                            >

                            @error("lines.$index.description")
                            <p>{{ $message }}</p>
                            @enderror

                        </div>

                        <div>
                            <label>Quantity</label>

                            <input
                                type="number"
                                name="lines[{{ $index }}][quantity]"
                                value="{{ old("lines.$index.quantity", $line->quantity) }}"
                                min="0.01"
                                step="0.01"
                                required
                            >

                            @error("lines.$index.quantity")
                            <p>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label>Unit Price (€)</label>

                            <input
                                type="number"
                                name="lines[{{ $index }}][unit_price]"
                                value="{{ old("lines.$index.unit_price", $line->unit_price) }}"
                                min="0"
                                step="0.01"
                                required
                            >

                            @error("lines.$index.unit_price")
                            <p>{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="button" class="remove-line">Remove Item</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-line">Add Item</button>

            <button type="submit">Update Invoice</button>
        </form>

        <a href="{{ route('invoices.show', $invoice) }}">Cancel</a>
    </div>

    <<script>
        let lineIndex = {{ $invoice->lines->count() }};

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


