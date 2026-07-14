<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeAccountRevocationGoogleLogin(User $user): void
{
    $googleUser = (new GoogleUser())->map([
        'id' => $user->google_id,
        'email' => $user->email,
        'name' => $user->name,
        'nickname' => null,
    ]);

    $googleUser->user = [
        'email_verified' => true,
    ];

    $provider = Mockery::mock();

    $provider->shouldReceive('user')
        ->once()
        ->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);
}

it('allows active admins and regular users into their own areas', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Active Admin',
        'email' => 'active-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $user = User::query()->forceCreate([
        'name' => 'Active User',
        'email' => 'active-user@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_USER,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();

    $this->actingAs($user)
        ->get(route('account.locked'))
        ->assertOk();
});

it('revokes an existing admin session immediately after disablement', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Revoked Session Admin',
        'email' => 'revoked-session-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();

    $admin->forceFill([
        'disabled_at' => now(),
    ])->save();

    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('blocks disabled regular users from authenticated pages', function (): void {
    $user = User::query()->forceCreate([
        'name' => 'Disabled User',
        'email' => 'disabled-user@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_USER,
        'disabled_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('account.locked'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects Google login for a disabled account', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Disabled Google Admin',
        'email' => 'disabled-google-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'disabled-google-admin-id',
        'role' => User::ROLE_ADMIN,
        'disabled_at' => now(),
    ]);

    fakeAccountRevocationGoogleLogin($admin);

    $this->get(route('google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
    expect($admin->fresh()->last_login_at)->toBeNull();
});

it('records the last successful Google login time', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Active Google Admin',
        'email' => 'active-google-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'active-google-admin-id',
        'role' => User::ROLE_ADMIN,
    ]);

    fakeAccountRevocationGoogleLogin($admin);

    $this->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
    expect($admin->fresh()->last_login_at)->not->toBeNull();
});
