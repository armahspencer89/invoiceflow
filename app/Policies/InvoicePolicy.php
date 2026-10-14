<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Invoice ownership is indirect:
        // User → Customer → Invoice.
        // Only the user who owns the invoice's customer can view it.
        return $invoice->customer->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // Use the same ownership rule when modifying an invoice.
        return $invoice->customer->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $invoice->customer->user_id === $user->id
            && $invoice->status === 'draft';
    }
    public function markAsSent(User $user, Invoice $invoice): bool
    {
        return $invoice->customer->user_id === $user->id
            && $invoice->status === 'draft';
    }
}
