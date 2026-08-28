<?php

use App\Enums\AccountRole;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeBootstrapAdminGoogle(string $id, string $email): void
{
    $googleUser = (new GoogleUser)->map([
        'id' => $id,
        'email' => $email,
        'name' => 'Google Profile Name',
        'nickname' => null,
    ]);
    $googleUser->user = ['email_verified' => true];

    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn($provider);
}

it('provisions exactly one normalized admin without pre-binding Google', function (): void {
    $this->artisan('auth:bootstrap-admin', [
        '--name' => 'Owner Admin',
        '--email' => 'Owner@Example.Test',
    ])->assertExitCode(0);

    $admin = User::query()->sole();

    expect($admin->name)->toBe('Owner Admin')
        ->and($admin->email)->toBe('owner@example.test')
        ->and($admin->email_normalized)->toBe('owner@example.test')
        ->and($admin->role)->toBe(AccountRole::Admin)
        ->and($admin->google_id)->toBeNull()
        ->and($admin->disabled_at)->toBeNull()
        ->and($admin->email_verified_at)->toBeNull()
        ->and($admin->password)->not->toBe('');

    expect(SecurityAuditLog::query()
        ->where('event', 'account.admin.bootstrap_created')
        ->where('auditable_id', (string) $admin->id)
        ->exists())->toBeTrue();
});

it('refuses bootstrap after any admin already exists', function (): void {
    $this->artisan('auth:bootstrap-admin', [
        '--name' => 'First Admin',
        '--email' => 'first@example.test',
    ])->assertExitCode(0);

    $this->artisan('auth:bootstrap-admin', [
        '--name' => 'Second Admin',
        '--email' => 'second@example.test',
    ])->assertExitCode(1);

    expect(User::query()->where('role', AccountRole::Admin->value)->count())->toBe(1)
        ->and(User::query()->where('email_normalized', 'second@example.test')->exists())->toBeFalse();
});

it('refuses to escalate an existing account that owns the requested email', function (): void {
    $existing = User::query()->forceCreate([
        'name' => 'Existing Guru',
        'email' => 'shared@example.test',
        'password' => 'unused-password',
        'role' => AccountRole::Guru->value,
    ]);

    $this->artisan('auth:bootstrap-admin', [
        '--name' => 'Bootstrap Attempt',
        '--email' => 'SHARED@example.test',
    ])->assertExitCode(1);

    expect($existing->fresh()->role)->toBe(AccountRole::Guru)
        ->and(User::query()->where('role', AccountRole::Admin->value)->exists())->toBeFalse();
});

it('lets the bootstrapped admin bind its verified Google identity on first login', function (): void {
    $this->artisan('auth:bootstrap-admin', [
        '--name' => 'Official Owner Name',
        '--email' => 'owner@example.test',
    ])->assertExitCode(0);

    $admin = User::query()->sole();
    fakeBootstrapAdminGoogle('google-owner-123', 'OWNER@example.test');

    $this->withSession(['google_login_role' => AccountRole::Admin->value])
        ->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $admin->refresh();

    $this->assertAuthenticatedAs($admin);
    expect($admin->google_id)->toBe('google-owner-123')
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and($admin->name)->toBe('Official Owner Name');
});
