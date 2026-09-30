<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // A customer belongs to a user through the customer.user_id field.
        // Only the owner should be able to view the customer's information.
        return $customer->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Reuse the same ownership rule for updates.
        return $customer->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Only the customer owner can delete their customer record.
        return $customer->user_id === $user->id;
    }
}
