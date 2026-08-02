<?php

use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeGoogleAuditIdentity(string $googleId, string $email): void
{
    $googleUser = (new GoogleUser)->map([
        'id' => $googleId,
        'email' => $email,
        'name' => 'Audit Google User',
        'nickname' => null,
    ]);
    $googleUser->user = ['email_verified' => true];
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
}

it('records successful and denied Google authentication events', function (): void {
    $active = User::query()->forceCreate([
        'name' => 'Audit Active Admin',
        'email' => 'audit-active-admin@example.test',
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-active-admin-id',
        'role' => User::ROLE_ADMIN,
    ]);
    fakeGoogleAuditIdentity($active->google_id, $active->email);
    $this->withSession(['google_login_role' => User::ROLE_ADMIN])
        ->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));
    $this->assertDatabaseHas('security_audit_logs', [
        'actor_user_id' => $active->id,
        'event' => 'auth.admin.login_succeeded',
    ]);
    $this->post(route('logout'))->assertRedirect(route('login'));

    $disabled = User::query()->forceCreate([
        'name' => 'Audit Disabled Admin',
        'email' => 'audit-disabled-admin@example.test',
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-disabled-admin-id',
        'role' => User::ROLE_ADMIN,
        'disabled_at' => now(),
    ]);
    fakeGoogleAuditIdentity($disabled->google_id, $disabled->email);
    $this->withSession(['google_login_role' => User::ROLE_ADMIN])
        ->get(route('google.callback'))
        ->assertRedirect(route('login'));

    $denied = SecurityAuditLog::query()
        ->where('event', 'auth.google.login_denied')
        ->where('actor_user_id', $disabled->id)
        ->firstOrFail();
    expect($denied->metadata)->toBe(['reason' => 'access_unavailable']);
});

it('records binding conflicts without storing the email or Google ID', function (): void {
    User::query()->forceCreate([
        'name' => 'Audit Existing Admin',
        'email' => 'audit-conflict@example.test',
        'password' => Hash::make('unused-password'),
        'google_id' => 'original-google-id',
        'role' => User::ROLE_ADMIN,
    ]);
    fakeGoogleAuditIdentity('different-google-id', 'audit-conflict@example.test');
    $this->withSession(['google_login_role' => User::ROLE_ADMIN])
        ->get(route('google.callback'))
        ->assertRedirect(route('login'));

    $log = SecurityAuditLog::query()
        ->where('event', 'auth.google.binding_conflict')->firstOrFail();
    expect(json_encode($log->metadata))
        ->not->toContain('audit-conflict@example.test')
        ->not->toContain('different-google-id');
});
