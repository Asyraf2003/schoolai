<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeGoogleLogin(
    string $id,
    string $email,
    string $name,
    array $rawUser
): void {
    $googleUser = (new GoogleUser())->map([
        'id' => $id,
        'email' => $email,
        'name' => $name,
        'nickname' => null,
    ]);

    $googleUser->user = $rawUser;

    $provider = Mockery::mock();

    $provider->shouldReceive('user')
        ->once()
        ->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);
}

function fakeVerifiedGoogleLogin(
    string $id,
    string $email,
    string $name
): void {
    fakeGoogleLogin(
        $id,
        $email,
        $name,
        ['email_verified' => true]
    );
}

it(
    'accepts explicit Google email verification attributes',
    function (array $rawUser, string $googleId): void {
        config([
            'services.google.bootstrap_admin_id' => '',
        ]);

        $email = $googleId.'@example.test';

        fakeGoogleLogin(
            $googleId,
            $email,
            'Verified Google User',
            $rawUser
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('account.locked'));

        $this->assertAuthenticated();

        $user = User::query()
            ->where('google_id', $googleId)
            ->firstOrFail();

        expect($user->email)->toBe($email);
        expect($user->email_verified_at)->not->toBeNull();
    }
)->with([
    'email_verified true' => [
        ['email_verified' => true],
        'verified-primary',
    ],
    'verified_email true' => [
        ['verified_email' => true],
        'verified-fallback',
    ],
]);

it(
    'rejects Google email verification unless explicitly true',
    function (array $rawUser, string $googleId): void {
        $email = $googleId.'@example.test';

        fakeGoogleLogin(
            $googleId,
            $email,
            'Unverified Google User',
            $rawUser
        );

        $this->get(route('google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'google_id' => $googleId,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }
)->with([
    'attribute missing' => [
        [],
        'verification-missing',
    ],
    'boolean false' => [
        ['email_verified' => false],
        'verification-false-bool',
    ],
    'string false' => [
        ['email_verified' => 'false'],
        'verification-false-string',
    ],
    'integer zero' => [
        ['email_verified' => 0],
        'verification-zero-int',
    ],
    'string zero' => [
        ['email_verified' => '0'],
        'verification-zero-string',
    ],
    'null value' => [
        ['email_verified' => null],
        'verification-null',
    ],
    'integer one' => [
        ['email_verified' => 1],
        'verification-one-int',
    ],
    'string true' => [
        ['email_verified' => 'true'],
        'verification-true-string',
    ],
    'unknown string' => [
        ['email_verified' => 'yes'],
        'verification-unknown-string',
    ],
]);

it('allows only the configured Google ID to claim the first admin role', function (): void {
    config([
        'services.google.bootstrap_admin_id' => 'google-admin-allowed',
    ]);

    fakeVerifiedGoogleLogin(
        'google-outsider',
        'outsider@example.test',
        'Outsider'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('account.locked'));

    $outsider = User::query()
        ->where('google_id', 'google-outsider')
        ->firstOrFail();

    $stateAfterOutsider = DB::table('auth_bootstrap_states')
        ->where('key', 'first_google_admin')
        ->first();

    expect($outsider->role)->toBe(User::ROLE_USER);
    expect($stateAfterOutsider)->not->toBeNull();
    expect($stateAfterOutsider->claimed_user_id)->toBeNull();

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    fakeVerifiedGoogleLogin(
        'google-admin-allowed',
        'allowed-admin@example.test',
        'Allowed Admin'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $admin = User::query()
        ->where('google_id', 'google-admin-allowed')
        ->firstOrFail();

    $claimedState = DB::table('auth_bootstrap_states')
        ->where('key', 'first_google_admin')
        ->first();

    expect($admin->role)->toBe(User::ROLE_ADMIN);
    expect($claimedState)->not->toBeNull();
    expect($claimedState->claimed_user_id)->toBe($admin->id);

    $this->post(route('logout'))
        ->assertRedirect(route('login'));

    config([
        'services.google.bootstrap_admin_id' => 'google-second-candidate',
    ]);

    fakeVerifiedGoogleLogin(
        'google-second-candidate',
        'second-candidate@example.test',
        'Second Candidate'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('account.locked'));

    $secondCandidate = User::query()
        ->where('google_id', 'google-second-candidate')
        ->firstOrFail();

    $finalState = DB::table('auth_bootstrap_states')
        ->where('key', 'first_google_admin')
        ->first();

    expect($secondCandidate->role)->toBe(User::ROLE_USER);
    expect(
        User::query()
            ->where('role', User::ROLE_ADMIN)
            ->count()
    )->toBe(1);
    expect($finalState->claimed_user_id)->toBe($admin->id);
});

it('fails closed when bootstrap admin Google ID is empty', function (): void {
    config([
        'services.google.bootstrap_admin_id' => '',
    ]);

    fakeVerifiedGoogleLogin(
        'google-unconfigured',
        'unconfigured@example.test',
        'Unconfigured User'
    );

    $this->get(route('google.callback'))
        ->assertRedirect(route('account.locked'));

    $user = User::query()
        ->where('google_id', 'google-unconfigured')
        ->firstOrFail();

    $state = DB::table('auth_bootstrap_states')
        ->where('key', 'first_google_admin')
        ->first();

    expect($user->role)->toBe(User::ROLE_USER);
    expect($state)->not->toBeNull();
    expect($state->claimed_user_id)->toBeNull();
    expect(
        User::query()
            ->where('role', User::ROLE_ADMIN)
            ->count()
    )->toBe(0);
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

it('applies the named rate limiter to both Google OAuth endpoints', function (): void {
    foreach (['google.redirect', 'google.callback'] as $routeName) {
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull();
        expect($route->gatherMiddleware())
            ->toContain('throttle:google-oauth');
    }
});

it('rate limits repeated Google OAuth redirects without leaking credentials', function (): void {
    config([
        'services.google.client_id' => 'oauth-client-id-do-not-leak',
        'services.google.client_secret' => 'oauth-client-secret-do-not-leak',
    ]);

    $provider = Mockery::mock();

    $provider->shouldReceive('redirect')
        ->times(20)
        ->andReturnUsing(
            fn () => redirect('https://accounts.google.com')
        );

    Socialite::shouldReceive('driver')
        ->with('google')
        ->times(20)
        ->andReturn($provider);

    // The Socialite mock does not persist OAuth state, so this
    // loop proves the independent per-IP limit.
    foreach (range(1, 20) as $attempt) {
        $this->get(route('google.redirect'))
            ->assertRedirect('https://accounts.google.com');
    }

    $this->get(route('google.redirect'))
        ->assertStatus(429)
        ->assertDontSee('oauth-client-id-do-not-leak')
        ->assertDontSee('oauth-client-secret-do-not-leak');
});

it('protects every admin URI and mutation with the full middleware stack', function (): void {
    $adminRoutes = collect(Route::getRoutes())
        ->filter(function ($route): bool {
            $uri = $route->uri();

            return $uri === 'admin'
                || str_starts_with($uri, 'admin/');
        });

    $mutationMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    $adminMutationRoutes = $adminRoutes
        ->filter(
            fn ($route): bool => collect($route->methods())
                ->intersect($mutationMethods)
                ->isNotEmpty()
        );

    expect($adminRoutes->count())->toBeGreaterThan(0);
    expect($adminMutationRoutes->count())->toBeGreaterThan(0);

    foreach ($adminRoutes as $route) {
        expect($route->gatherMiddleware())
            ->toContain('web')
            ->toContain('auth')
            ->toContain('admin')
            ->toContain('admin.locale');
    }
});

it('blocks guests from admin pages and mutations without changing data', function (): void {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));

    $this->post(route('admin.stats.store'), [
        'value' => '999',
        'value_en' => '999',
        'label' => 'Tamu tidak boleh membuat',
        'label_en' => 'Guest must not create',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseMissing('site_statistics', [
        'label' => 'Tamu tidak boleh membuat',
    ]);
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
        ->assertSee('Continue with Google')
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
