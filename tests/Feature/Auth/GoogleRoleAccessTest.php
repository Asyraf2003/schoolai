<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeVerifiedGoogleLogin(
    string $id,
    string $email,
    string $name
): void {
    $googleUser = (new GoogleUser())->map([
        'id' => $id,
        'email' => $email,
        'name' => $name,
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

it('makes only the first Google account an admin', function (): void {
    fakeVerifiedGoogleLogin(
        'google-admin-1',
        'first-admin@example.test',
        'First Admin'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $first = User::query()
        ->where('email', 'first-admin@example.test')
        ->firstOrFail();

    expect($first->role)->toBe(User::ROLE_ADMIN)
        ->and($first->google_id)->toBe('google-admin-1');

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    fakeVerifiedGoogleLogin(
        'google-user-2',
        'regular-user@example.test',
        'Regular User'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('account.locked'));

    $second = User::query()
        ->where('email', 'regular-user@example.test')
        ->firstOrFail();

    expect($second->role)->toBe(User::ROLE_USER)
        ->and($second->google_id)->toBe('google-user-2')
        ->and(
            User::query()
                ->where('role', User::ROLE_ADMIN)
                ->count()
        )->toBe(1);
});

it('rejects automatic linking to an unlinked admin email', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Unlinked Admin',
        'email' => 'unlinked-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => null,
        'role' => User::ROLE_ADMIN,
    ]);

    fakeVerifiedGoogleLogin(
        'attacker-google-identity',
        'unlinked-admin@example.test',
        'Different Google Identity'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();

    $admin->refresh();

    expect($admin->google_id)->toBeNull()
        ->and($admin->role)->toBe(User::ROLE_ADMIN);
});

it('allows an identity already linked by Google ID', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Linked Admin',
        'email' => 'linked-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'google_id' => 'linked-google-identity',
        'role' => User::ROLE_ADMIN,
    ]);

    fakeVerifiedGoogleLogin(
        'linked-google-identity',
        'linked-admin@example.test',
        'Linked Admin'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('protects every named admin route with admin middleware', function (): void {
    $adminRoutes = collect(Route::getRoutes())
        ->filter(
            fn ($route): bool => str_starts_with(
                (string) $route->getName(),
                'admin.'
            )
        );

    expect($adminRoutes->count())->toBeGreaterThan(0);

    foreach ($adminRoutes as $route) {
        expect($route->gatherMiddleware())
            ->toContain('auth')
            ->toContain('admin');
    }
});

it('blocks regular users from admin pages and actions', function (): void {
    $user = User::query()->forceCreate([
        'name' => 'Regular User',
        'email' => 'regular@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_USER,
    ]);

    $this->actingAs($user);

    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('account.locked'));

    $this->post(route('admin.stats.store'), [
        'value' => '999',
        'value_en' => '999',
        'label' => 'Tidak boleh dibuat',
        'label_en' => 'Must not be created',
    ])->assertRedirect(route('account.locked'));

    $this->assertDatabaseMissing('site_statistics', [
        'label' => 'Tidak boleh dibuat',
    ]);

    $this->withSession([
        'locale' => 'id',
    ])->get(route('account.locked'))
        ->assertOk()
        ->assertSee('Konten belum tersedia di sini')
        ->assertSee('regular@example.test');

    $this->withSession([
        'locale' => 'en',
    ])->get(route('account.locked'))
        ->assertOk()
        ->assertSee('Content is not available here yet')
        ->assertSee('regular@example.test');
});

it('keeps admins out of the regular user page', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Admin',
        'email' => 'admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin);

    $this->get(route('account.locked'))
        ->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))
        ->assertOk();
});

it('shows Google as the only login method', function (): void {
    $this->withSession([
        'locale' => 'id',
    ])->get(route('login'))
        ->assertOk()
        ->assertSee('Masuk dengan Google')
        ->assertDontSee('name="email"', false)
        ->assertDontSee('name="password"', false);

    $this->withSession([
        'locale' => 'en',
    ])->get(route('login'))
        ->assertOk()
        ->assertSee('Sign in with Google')
        ->assertDontSee('name="email"', false)
        ->assertDontSee('name="password"', false);
});

it('does not expose a manual password login route', function (): void {
    expect(Route::has('login.store'))->toBeFalse();

    $this->post('/login', [
        'email' => 'admin@example.test',
        'password' => 'password',
    ])->assertStatus(405);
});
