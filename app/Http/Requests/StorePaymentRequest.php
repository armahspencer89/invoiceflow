<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by PaymentPolicy.
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'regex:/^\d{1,12}(\.\d{1,2})?$/',
                'decimal:0,2',
                'gt:0',
            ],

            'payment_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],

            'method' => [
                'required',
                Rule::in([
                    'bank_transfer',
                    'cash',
                    'card',
                    'other',
                ]),
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.gt' => 'The payment amount must be greater than zero.',
            'amount.regex' => 'Enter a valid amount with at most two decimal places.',
            'payment_date.before_or_equal' =>
                'The payment date cannot be in the future.',
        ];
    }
}
