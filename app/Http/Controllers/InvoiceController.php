<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display the authenticated user's invoices.
     */
    public function index(): View
    {
        $invoices = Invoice::query()
            ->whereHas('customer', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with('customer')

            // Search by invoice number or customer name.
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })

            // Filter by stored invoice status.
            ->when(request('status'), function ($query, $status) {
                if (in_array($status, ['draft', 'sent', 'paid', 'overdue'], true)) {
                    $query->where('status', $status);
                }
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(): View
    {
        // Only load active customers belonging to the authenticated user.
        $customers = auth()->user()
            ->customers()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('invoices.create', compact('customers'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        // Validate the basic invoice fields submitted by the form.
        $validated = $request->validated();

        // Find the selected customer through the authenticated user's
        // customers relationship. This prevents a user from creating
        // an invoice for another user's customer.
        $customer = auth()->user()
            ->customers()
            ->whereKey($validated['customer_id'])
            ->firstOrFail();

        // Create the invoice after the ownership check succeeds.
        $invoice = DB::transaction(function () use ($customer, $validated) {
            $invoice = $customer->invoices()->create([
                'invoice_number' => 'INV-' . Str::uuid(),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'status' => 'draft',
                'vat_rate' => $validated['vat_rate'],
            ]);

            foreach ($validated['lines'] as $line) {
                $invoice->lines()->create([
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                ]);
            }

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): View
    {
        // Authorization follows the ownership chain:
        // User → Customer → Invoice.
        Gate::authorize('view', $invoice);

        $invoice->load([
            'customer',
            'lines',
            'payments' => fn ($query) =>
            $query->orderByDesc('payment_date')
                ->orderByDesc('id'),
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice): View
    {
        Gate::authorize('update', $invoice);

        abort_if($invoice->status !== 'draft', 403);

        $invoice->load(['customer', 'lines']);

        $customers = auth()->user()
            ->customers()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(
        UpdateInvoiceRequest $request,
        Invoice $invoice
    ): RedirectResponse {
        Gate::authorize('update', $invoice);

        // Only draft invoices can be modified.
        abort_if($invoice->status !== 'draft', 403);

        $validated = $request->validated();

        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'vat_rate' => $validated['vat_rate'],
            ]);

            // Draft invoice lines can be safely replaced with the
            // newly submitted version of the invoice.
            $invoice->lines()->delete();

            foreach ($validated['lines'] as $line) {
                $invoice->lines()->create([
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                ]);
            }
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Mark a draft invoice as sent.
     */
    public function markAsSent(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('markAsSent', $invoice);

        $invoice->update([
            'status' => 'sent',
        ]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as sent successfully.');
    }

    /**
     * Delete a draft invoice.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('delete', $invoice);

        DB::transaction(function () use ($invoice) {
            $invoice->delete();
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
