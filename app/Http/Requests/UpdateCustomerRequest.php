<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now, we only allow authenticated users to submit this request.
        // Resource-level ownership will be handled by a Customer Policy later.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // The same validation rules used when creating a customer.
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'vat_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
