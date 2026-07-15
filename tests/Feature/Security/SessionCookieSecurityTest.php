<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('marks application cookies secure and HTTP only when HTTPS is configured', function (): void {
    config([
        'session.secure' => true,
        'session.http_only' => true,
        'session.same_site' => 'lax',
    ]);

    $response = $this->withSession(['_token' => 'session-cookie-token'])
        ->post('https://localhost/bahasa/en', ['_token' => 'session-cookie-token'])
        ->assertRedirect();

    $localeCookie = collect($response->headers->getCookies())
        ->first(fn ($cookie): bool => $cookie->getName() === 'site_locale');

    expect($localeCookie)
        ->not->toBeNull()
        ->and($localeCookie->isSecure())->toBeTrue()
        ->and($localeCookie->isHttpOnly())->toBeTrue()
        ->and($localeCookie->getSameSite())->toBe('lax');
});

it('invalidates the authenticated session during logout', function (): void {
    $user = User::query()->forceCreate([
        'name' => 'Session Security User',
        'email' => 'session-security@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_USER,
    ]);

    $this->actingAs($user)
        ->withSession(['sensitive_marker' => 'must-be-removed'])
        ->post(route('logout'))
        ->assertRedirect(route('login'))
        ->assertSessionMissing('sensitive_marker');

    $this->assertGuest();
});

it('uses secure production fallbacks without hardcoding a deployment domain', function (): void {
    $sessionConfig = file_get_contents(config_path('session.php'));
    $environmentExample = file_get_contents(base_path('.env.example'));

    expect($sessionConfig)
        ->toContain("env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production')")
        ->toContain("env('SESSION_ENCRYPT', env('APP_ENV') === 'production')")
        ->and($environmentExample)
        ->toContain('SESSION_HTTP_ONLY=true')
        ->toContain('SESSION_SAME_SITE=lax')
        ->toContain('TRUSTED_PROXIES=');
});
