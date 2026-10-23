<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Verify that the user owns both the invoice
     * and the customer associated with it.
     */
    private function ownsInvoice(User $user, Invoice $invoice): bool
    {
        return (int) $invoice->user_id === (int) $user->id
            && (int) $invoice->customer->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $this->ownsInvoice($user, $invoice);
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $this->ownsInvoice($user, $invoice)
            && $invoice->status === 'draft';
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->ownsInvoice($user, $invoice)
            && $invoice->status === 'draft';
    }

    /**
     * Determine whether the user can issue the invoice.
     */
    public function markAsSent(User $user, Invoice $invoice): bool
    {
        return $this->ownsInvoice($user, $invoice)
            && $invoice->status === 'draft';
    }
}
