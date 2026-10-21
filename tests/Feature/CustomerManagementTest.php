<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customer_list(): void
    {
        $response = $this->get(route('customers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_only_sees_their_own_customers(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $ownCustomer = new Customer([
            'name' => 'Own Customer',
            'email' => 'own@example.com',
        ]);

        $userA->customers()->save($ownCustomer);

        $otherCustomer = new Customer([
            'name' => 'Other Customer',
            'email' => 'other@example.com',
        ]);

        $userB->customers()->save($otherCustomer);

        $response = $this->actingAs($userA)
            ->get(route('customers.index'));

        $response->assertOk();
        $response->assertSee('Own Customer');
        $response->assertDontSee('Other Customer');
    }

    public function test_user_cannot_view_another_users_customer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = new Customer([
            'name' => 'Private Customer',
            'email' => 'private@example.com',
        ]);

        $owner->customers()->save($customer);

        $response = $this->actingAs($otherUser)
            ->get(route('customers.show', $customer));

        $response->assertForbidden();
    }

    public function test_user_cannot_edit_another_users_customer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = $owner->customers()->create([
            'name' => 'Private Customer',
            'email' => 'private@example.com',
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('customers.edit', $customer));

        $response->assertForbidden();
    }

    public function test_user_cannot_update_another_users_customer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = $owner->customers()->create([
            'name' => 'Original Customer',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($otherUser)
            ->put(route('customers.update', $customer), [
                'name' => 'Hacked Customer',
                'email' => 'hacked@example.com',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'user_id' => $owner->id,
            'name' => 'Original Customer',
            'email' => 'original@example.com',
        ]);
    }

    public function test_user_cannot_delete_another_users_customer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = $owner->customers()->create([
            'name' => 'Protected Customer',
            'email' => 'protected@example.com',
        ]);

        $response = $this->actingAs($otherUser)
            ->delete(route('customers.destroy', $customer));

        $response->assertForbidden();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_user_cannot_deactivate_or_reactivate_another_users_customer(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = $owner->customers()->create([
            'name' => 'Protected Customer',
            'email' => 'protected@example.com',
        ]);

        // Attempt to deactivate another user's active customer.
        $this->actingAs($otherUser)
            ->patch(route('customers.deactivate', $customer))
            ->assertForbidden();

        $this->assertTrue($customer->fresh()->is_active);

        // The owner legitimately deactivates the customer.
        $this->actingAs($owner)
            ->patch(route('customers.deactivate', $customer))
            ->assertRedirect(route('customers.show', $customer));

        $this->assertFalse($customer->fresh()->is_active);

        // Attempt to reactivate another user's inactive customer.
        $this->actingAs($otherUser)
            ->patch(route('customers.reactivate', $customer))
            ->assertForbidden();

        $this->assertFalse($customer->fresh()->is_active);
    }

    public function test_customer_is_created_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'New Customer',
                'email' => 'new@example.com',
            ]);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_assign_new_customer_to_another_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($userA)
            ->post(route('customers.store'), [
                'name' => 'Protected Customer',
                'email' => 'protected@example.com',
                'user_id' => $userB->id,
                'is_active' => false,
            ]);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'user_id' => $userA->id,
            'name' => 'Protected Customer',
            'email' => 'protected@example.com',
            'is_active' => true,
        ]);

        $this->assertDatabaseMissing('customers', [
            'user_id' => $userB->id,
            'email' => 'protected@example.com',
        ]);
    }

    public function test_invalid_customer_data_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('customers.store'), [
                'name' => '',
                'email' => 'not-an-email',
            ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
        ]);

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_user_cannot_change_customer_ownership_during_update(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $customer = $owner->customers()->create([
            'name' => 'Original Customer',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($owner)
            ->put(route('customers.update', $customer), [
                'name' => 'Updated Customer',
                'email' => 'updated@example.com',
                'user_id' => $otherUser->id,
            ]);

        $response->assertRedirect(
            route('customers.show', $customer)
        );

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'user_id' => $owner->id,
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
        ]);
    }

}
