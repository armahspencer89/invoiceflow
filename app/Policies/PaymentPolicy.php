<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
/*
 This policy checks two things:
- The invoice belongs to the authenticated user's customer.
- The invoice has a sent status.
 */
class PaymentPolicy
{
    public function create(User $user, Invoice $invoice): bool
    {
        return $invoice->customer()->where(
                'user_id',
                $user->id
            )->exists()
            && $invoice->status === 'sent';
    }
}
