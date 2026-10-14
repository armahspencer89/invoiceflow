
@extends('layouts.app')

@section('title', 'Create Invoice - InvoiceFlow')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Create Invoice</h1>
            <p class="page-subtitle">
                Create a new invoice for one of your customers.
            </p>
        </div>

        <a href="{{ route('invoices.index') }}" class="btn-secondary">
            ← Back to Invoices
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            Please correct the highlighted fields and try again.
        </div>
    @endif

    <form method="POST"
          action="{{ route('invoices.store') }}"
          id="create-invoice-form">

        @csrf

        <!-- INVOICE INFORMATION -->
        <section class="card invoice-form-card">

            <div class="invoice-form-heading">
                <h2 class="card-title">Invoice Information</h2>
                <p class="card-description">
                    Select a customer and enter the invoice details.
                </p>
            </div>

            <div class="invoice-form-grid">

                <div class="form-group invoice-form-full">
                    <label for="customer_id" class="form-label">
                        Customer *
                    </label>

                    <select
                        name="customer_id"
                        id="customer_id"
                        class="form-select"
                        required
                        aria-invalid="{{ $errors->has('customer_id') ? 'true' : 'false' }}"
                    >
                        <option value="">Select a customer</option>

                        @foreach ($customers as $customer)
                            <option
                                value="{{ $customer->id }}"
                                @selected(old('customer_id') == $customer->id)
                            >
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('customer_id')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="invoice_date" class="form-label">
                        Invoice Date *
                    </label>

                    <input
                        type="date"
                        name="invoice_date"
                        id="invoice_date"
                        class="form-input"
                        value="{{ old('invoice_date', now()->format('Y-m-d')) }}"
                        required
                        aria-invalid="{{ $errors->has('invoice_date') ? 'true' : 'false' }}"
                    >

                    @error('invoice_date')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="due_date" class="form-label">
                        Due Date *
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        id="due_date"
                        class="form-input"
                        value="{{ old('due_date') }}"
                        required
                        aria-invalid="{{ $errors->has('due_date') ? 'true' : 'false' }}"
                    >

                    @error('due_date')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="vat_rate" class="form-label">
                        VAT Rate (%) *
                    </label>

                    <input
                        type="number"
                        name="vat_rate"
                        id="vat_rate"
                        class="form-input"
                        value="{{ old('vat_rate', '21.00') }}"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                        aria-invalid="{{ $errors->has('vat_rate') ? 'true' : 'false' }}"
                    >

                    @error('vat_rate')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </section>

        <!-- INVOICE ITEMS -->
        <section class="card invoice-form-card">

            <div class="invoice-form-heading invoice-items-heading">
                <div>
                    <h2 class="card-title">Invoice Items</h2>
                    <p class="card-description">
                        Add the products or services included in this invoice.
                    </p>
                </div>

                <button type="button"
                        id="add-line"
                        class="btn-secondary">
                    + Add Item
                </button>
            </div>

            @error('lines')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <div id="invoice-lines" class="invoice-lines-list">

                @foreach (old('lines', [['description' => '', 'quantity' => '1.00', 'unit_price' => '']]) as $index => $line)

                    <div class="invoice-line" data-line-index="{{ $index }}">

                        <div class="invoice-line-heading">
                            <h3 class="invoice-line-title">
                                Invoice Item
                            </h3>

                            <button type="button"
                                    class="invoice-remove-line">
                                Remove
                            </button>
                        </div>

                        <div class="invoice-line-grid">

                            <div class="form-group">
                                <label for="lines_{{ $index }}_description"
                                       class="form-label">
                                    Description *
                                </label>

                                <input
                                    type="text"
                                    name="lines[{{ $index }}][description]"
                                    id="lines_{{ $index }}_description"
                                    class="form-input"
                                    value="{{ $line['description'] ?? '' }}"
                                    placeholder="Service or product description"
                                    required
                                    aria-invalid="{{ $errors->has('lines.' . $index . '.description') ? 'true' : 'false' }}"
                                >

                                @error('lines.' . $index . '.description')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="lines_{{ $index }}_quantity"
                                       class="form-label">
                                    Quantity *
                                </label>

                                <input
                                    type="number"
                                    name="lines[{{ $index }}][quantity]"
                                    id="lines_{{ $index }}_quantity"
                                    class="form-input invoice-quantity"
                                    value="{{ $line['quantity'] ?? '1.00' }}"
                                    min="0.01"
                                    step="0.01"
                                    required
                                    aria-invalid="{{ $errors->has('lines.' . $index . '.quantity') ? 'true' : 'false' }}"
                                >

                                @error('lines.' . $index . '.quantity')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="lines_{{ $index }}_unit_price"
                                       class="form-label">
                                    Unit Price (€) *
                                </label>

                                <input
                                    type="number"
                                    name="lines[{{ $index }}][unit_price]"
                                    id="lines_{{ $index }}_unit_price"
                                    class="form-input invoice-unit-price"
                                    value="{{ $line['unit_price'] ?? '' }}"
                                    min="0.01"
                                    step="0.01"
                                    required
                                    aria-invalid="{{ $errors->has('lines.' . $index . '.unit_price') ? 'true' : 'false' }}"
                                >

                                @error('lines.' . $index . '.unit_price')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                @endforeach

            </div>

            <!-- ESTIMATED TOTAL -->
            <div class="invoice-preview">

                <h3>Estimated Invoice Total</h3>

                <div class="invoice-preview-row">
                    <span>Subtotal</span>
                    <strong id="invoice-preview-subtotal">€0.00</strong>
                </div>

                <div class="invoice-preview-row">
                    <span>VAT</span>
                    <strong id="invoice-preview-vat">€0.00</strong>
                </div>

                <div class="invoice-preview-row invoice-preview-total">
                    <span>Total</span>
                    <strong id="invoice-preview-total">€0.00</strong>
                </div>

                <p class="invoice-preview-note">
                    This is an estimate. Final totals are calculated
                    and validated by InvoiceFlow when the invoice is saved.
                </p>

            </div>

        </section>

        <!-- ACTIONS -->
        <div class="invoice-form-actions">

            <a href="{{ route('invoices.index') }}"
               class="btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn-primary">
                Create Invoice
            </button>

        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const linesContainer = document.getElementById('invoice-lines');
            const addLineButton = document.getElementById('add-line');
            const vatInput = document.getElementById('vat_rate');
            const invoiceDate = document.getElementById('invoice_date');
            const dueDate = document.getElementById('due_date');

            let lineIndex = Array.from(
                linesContainer.querySelectorAll('.invoice-line')
            ).reduce((max, line) => {
                return Math.max(max, Number(line.dataset.lineIndex));
            }, -1) + 1;

            const formatter = new Intl.NumberFormat('en-IE', {
                style: 'currency',
                currency: 'EUR'
            });

            function roundCents(value) {
                return Math.round((value + Number.EPSILON) * 100);
            }

            function updateTotals() {
                let subtotalCents = 0;

                linesContainer.querySelectorAll('.invoice-line').forEach(line => {
                    const quantity = Number(
                        line.querySelector('.invoice-quantity').value
                    );

                    const unitPrice = Number(
                        line.querySelector('.invoice-unit-price').value
                    );

                    if (
                        Number.isFinite(quantity) &&
                        Number.isFinite(unitPrice) &&
                        quantity > 0 &&
                        unitPrice > 0
                    ) {
                        subtotalCents += roundCents(quantity * unitPrice);
                    }
                });

                const vatRate = Number(vatInput.value) || 0;
                const vatCents = roundCents((subtotalCents / 100) * vatRate / 100);
                const totalCents = subtotalCents + vatCents;

                document.getElementById('invoice-preview-subtotal').textContent =
                    formatter.format(subtotalCents / 100);

                document.getElementById('invoice-preview-vat').textContent =
                    formatter.format(vatCents / 100);

                document.getElementById('invoice-preview-total').textContent =
                    formatter.format(totalCents / 100);
            }

            function updateRemoveButtons() {
                const buttons = linesContainer.querySelectorAll('.invoice-remove-line');
                const disableRemove = buttons.length === 1;

                buttons.forEach(button => {
                    button.disabled = disableRemove;
                });
            }

            addLineButton.addEventListener('click', function () {
                const index = lineIndex++;

                const line = document.createElement('div');
                line.className = 'invoice-line';
                line.dataset.lineIndex = index;

                line.innerHTML = `
            <div class="invoice-line-heading">
                <h3 class="invoice-line-title">Invoice Item</h3>
                <button type="button" class="invoice-remove-line">
                    Remove
                </button>
            </div>

            <div class="invoice-line-grid">
                <div class="form-group">
                    <label for="lines_${index}_description" class="form-label">
                        Description *
                    </label>
                    <input
                        type="text"
                        name="lines[${index}][description]"
                        id="lines_${index}_description"
                        class="form-input"
                        placeholder="Service or product description"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="lines_${index}_quantity" class="form-label">
                        Quantity *
                    </label>
                    <input
                        type="number"
                        name="lines[${index}][quantity]"
                        id="lines_${index}_quantity"
                        class="form-input invoice-quantity"
                        value="1.00"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="lines_${index}_unit_price" class="form-label">
                        Unit Price (€) *
                    </label>
                    <input
                        type="number"
                        name="lines[${index}][unit_price]"
                        id="lines_${index}_unit_price"
                        class="form-input invoice-unit-price"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>
            </div>
        `;

                linesContainer.appendChild(line);
                updateRemoveButtons();
                updateTotals();

                line.querySelector('input').focus();
            });

            linesContainer.addEventListener('click', function (event) {
                if (!event.target.classList.contains('invoice-remove-line')) {
                    return;
                }

                if (linesContainer.querySelectorAll('.invoice-line').length <= 1) {
                    return;
                }

                event.target.closest('.invoice-line').remove();
                updateRemoveButtons();
                updateTotals();
            });

            linesContainer.addEventListener('input', updateTotals);
            vatInput.addEventListener('input', updateTotals);

            invoiceDate.addEventListener('change', function () {
                if (!dueDate.value || dueDate.value < invoiceDate.value) {
                    dueDate.value = invoiceDate.value;
                }
            });

            updateRemoveButtons();
            updateTotals();
        });
    </script>

@endsection
