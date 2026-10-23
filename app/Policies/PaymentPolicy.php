<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether a user can record a payment.
     *
     * A payment is allowed only when:
     * 1. The invoice belongs to the authenticated user.
     * 2. The invoice's customer belongs to the authenticated user.
     * 3. The invoice has been issued and is not fully paid.
     */
    public function create(User $user, Invoice $invoice): bool
    {
        return (int) $invoice->user_id === (int) $user->id
            && (int) $invoice->customer->user_id === (int) $user->id
            && $invoice->status === 'sent';
    }
}
