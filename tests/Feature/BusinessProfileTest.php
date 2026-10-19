<?php

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Test Data
|--------------------------------------------------------------------------
*/

function validBusinessProfileData(): array
{
    return [
        'business_name' => 'InvoiceFlow Demo Business',
        'email' => 'billing@example.com',
        'phone' => '+32 470 00 00 00',
        'address' => 'Test Street 10, 2000 Antwerp, Belgium',
        'vat_number' => 'BE0123456789',
        'iban' => 'BE00000000000000',
        'bic' => 'GEBABEBB',
    ];
}

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

test('guest cannot access business profile settings', function () {
    $this->get(route('business-profile.edit'))
        ->assertRedirect(route('login'));
});

test('guest cannot save a business profile', function () {
    $this->put(
        route('business-profile.update'),
        validBusinessProfileData()
    )->assertRedirect(route('login'));

    $this->assertDatabaseCount('business_profiles', 0);
});

test('authenticated user can view business profile settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('business-profile.edit'))
        ->assertOk()
        ->assertViewIs('business-profile.edit')
        ->assertViewHas('profile', null);
});

/*
|--------------------------------------------------------------------------
| Profile Creation
|--------------------------------------------------------------------------
*/

test('authenticated user can create a business profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('business-profile.update'), validBusinessProfileData())
        ->assertRedirect(route('business-profile.edit'))
        ->assertSessionHas(
            'success',
            'Business profile saved successfully.'
        );

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'InvoiceFlow Demo Business',
        'email' => 'billing@example.com',
        'vat_number' => 'BE0123456789',
    ]);
});

test('business profile is associated with its owner', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('business-profile.update'), validBusinessProfileData())
        ->assertRedirect(route('business-profile.edit'));

    $user->refresh();

    expect($user->businessProfile)->not->toBeNull()
        ->and($user->businessProfile->user_id)->toBe($user->id)
        ->and($user->businessProfile->user->id)->toBe($user->id);
});

/*
|--------------------------------------------------------------------------
| Profile Updates
|--------------------------------------------------------------------------
*/

test('user can update an existing business profile', function () {
    $user = User::factory()->create();

    $profile = $user->businessProfile()->create(
        validBusinessProfileData()
    );

    $updatedData = validBusinessProfileData();
    $updatedData['business_name'] = 'Updated Business Name';

    $this->actingAs($user)
        ->put(route('business-profile.update'), $updatedData)
        ->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseHas('business_profiles', [
        'id' => $profile->id,
        'user_id' => $user->id,
        'business_name' => 'Updated Business Name',
    ]);

    $this->assertDatabaseCount('business_profiles', 1);
});

test('updating a profile does not create duplicates', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->put(
        route('business-profile.update'),
        validBusinessProfileData()
    )->assertRedirect(route('business-profile.edit'));

    $updatedData = validBusinessProfileData();
    $updatedData['business_name'] = 'Second Business Name';

    $this->put(
        route('business-profile.update'),
        $updatedData
    )->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseCount('business_profiles', 1);

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'Second Business Name',
    ]);
});

test('existing business profile values appear on settings page', function () {
    $user = User::factory()->create();

    $user->businessProfile()->create(validBusinessProfileData());

    $this->actingAs($user)
        ->get(route('business-profile.edit'))
        ->assertOk()
        ->assertSee('InvoiceFlow Demo Business')
        ->assertSee('billing@example.com')
        ->assertSee('BE0123456789');
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

test('business name is required', function () {
    $user = User::factory()->create();

    $data = validBusinessProfileData();
    $data['business_name'] = '';

    $this->actingAs($user)
        ->from(route('business-profile.edit'))
        ->put(route('business-profile.update'), $data)
        ->assertRedirect(route('business-profile.edit'))
        ->assertSessionHasErrors(['business_name']);

    $this->assertDatabaseCount('business_profiles', 0);
});

test('business email must have a valid format', function () {
    $user = User::factory()->create();

    $data = validBusinessProfileData();
    $data['email'] = 'not-an-email';

    $this->actingAs($user)
        ->put(route('business-profile.update'), $data)
        ->assertSessionHasErrors(['email']);

    $this->assertDatabaseCount('business_profiles', 0);
});

test('business name cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    $data = validBusinessProfileData();
    $data['business_name'] = str_repeat('A', 256);

    $this->actingAs($user)
        ->put(route('business-profile.update'), $data)
        ->assertSessionHasErrors(['business_name']);

    $this->assertDatabaseCount('business_profiles', 0);
});

test('optional business fields can be empty', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('business-profile.update'), [
            'business_name' => 'Minimal Business',
        ])
        ->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'Minimal Business',
        'email' => null,
        'vat_number' => null,
        'iban' => null,
    ]);
});

test('invalid updates do not overwrite existing profile', function () {
    $user = User::factory()->create();

    $user->businessProfile()->create(validBusinessProfileData());

    $data = validBusinessProfileData();
    $data['business_name'] = '';
    $data['email'] = 'invalid-email';

    $this->actingAs($user)
        ->put(route('business-profile.update'), $data)
        ->assertSessionHasErrors([
            'business_name',
            'email',
        ]);

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'InvoiceFlow Demo Business',
        'email' => 'billing@example.com',
    ]);
});

/*
|--------------------------------------------------------------------------
| Authorization and User Isolation
|--------------------------------------------------------------------------
*/

test('user cannot assign a business profile to another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $data = validBusinessProfileData();
    $data['user_id'] = $otherUser->id;

    $this->actingAs($user)
        ->put(route('business-profile.update'), $data)
        ->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'InvoiceFlow Demo Business',
    ]);

    $this->assertDatabaseMissing('business_profiles', [
        'user_id' => $otherUser->id,
    ]);
});

test('users cannot view another users business profile', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherUser->businessProfile()->create([
        'business_name' => 'Private Business Name',
    ]);

    $this->actingAs($user)
        ->get(route('business-profile.edit'))
        ->assertOk()
        ->assertDontSee('Private Business Name');
});

test('users cannot modify another users business profile', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProfile = $otherUser->businessProfile()->create([
        'business_name' => 'Other User Business',
    ]);

    $this->actingAs($user)
        ->put(route('business-profile.update'), [
            'business_name' => 'My Own Business',
            'user_id' => $otherUser->id,
        ])
        ->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseHas('business_profiles', [
        'id' => $otherProfile->id,
        'user_id' => $otherUser->id,
        'business_name' => 'Other User Business',
    ]);

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $user->id,
        'business_name' => 'My Own Business',
    ]);
});

test('each user can maintain a separate business profile', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $this->actingAs($firstUser)
        ->put(route('business-profile.update'), [
            'business_name' => 'First Business',
        ])
        ->assertRedirect(route('business-profile.edit'));

    $this->actingAs($secondUser)
        ->put(route('business-profile.update'), [
            'business_name' => 'Second Business',
        ])
        ->assertRedirect(route('business-profile.edit'));

    $this->assertDatabaseCount('business_profiles', 2);

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $firstUser->id,
        'business_name' => 'First Business',
    ]);

    $this->assertDatabaseHas('business_profiles', [
        'user_id' => $secondUser->id,
        'business_name' => 'Second Business',
    ]);
});
