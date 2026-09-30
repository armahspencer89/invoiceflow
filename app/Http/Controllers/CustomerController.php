<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        // Start with the authenticated user's customers.
        // This keeps the query scoped to the current user's records.
        //This means the search cannot accidentally return another user's customers.
        $query = auth()->user()->customers();

        // Only apply the search conditions when the user entered a search term.
        $search = request('search');

        if ($search) {
            $query->where(function ($query) use ($search) {
                // Search across the customer name, company, and email.
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        // validated() returns only the fields that passed the Form Request rules.
        $request->user()->customers()->create($request->validated());

        return redirect()->route('customers.index');
    }

    public function show(Customer $customer): View
    {
        // Ask Laravel's authorization system to check the CustomerPolicy.
        // If the policy denies access, Laravel automatically throws a 403 response.
        Gate::authorize('view', $customer);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        // Only the owner of this customer should be able to edit it.
        Gate::authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    ): RedirectResponse {
        // Check authorization before modifying the customer.
        Gate::authorize('update', $customer);

        // Only validated fields are allowed to be written.
        $customer->update($request->validated());

        return redirect()->route('customers.show', $customer);
    }

    public function deactivate(Customer $customer): RedirectResponse
    {
        // Deactivation changes the customer's state, so the authenticated
        // user must have permission to update this customer.
        Gate::authorize('update', $customer);

        // Keep the customer in the database for historical records.
        // We only change its active/inactive state.
        $customer->update([
            'is_active' => false,
        ]);

        return redirect()->route('customers.show', $customer);
    }

    public function reactivate(Customer $customer): RedirectResponse
    {
        // Reactivation changes the customer's state, so the authenticated
        // user must have permission to update this customer.
        Gate::authorize('update', $customer);

        // Restore the customer without creating a new database record.
        $customer->update([
            'is_active' => true,
        ]);

        return redirect()->route('customers.show', $customer);
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Check that the authenticated user owns this customer
        // before allowing the record to be deleted.
        Gate::authorize('delete', $customer);

        // Delete the customer from the database.
        $customer->delete();

        // Return to the customer list after successful deletion.
        return redirect()->route('customers.index');
    }
}
