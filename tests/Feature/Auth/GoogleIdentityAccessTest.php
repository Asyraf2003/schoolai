<?php

use App\Enums\AccountRole;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakeSchoolGoogle(string $id, string $email, array $raw = ['email_verified' => true]): void
{
    $googleUser = (new GoogleUser)->map([
        'id' => $id,
        'email' => $email,
        'name' => 'Nama Profil Google',
        'nickname' => null,
    ]);
    $googleUser->user = $raw;
    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
}

function googleAccount(AccountRole $role, array $attributes = []): User
{
    return User::query()->forceCreate(array_replace([
        'name' => 'Nama Resmi Sekolah',
        'email' => $role->value.'@example.test',
        'password' => Hash::make('unused-password'),
        'role' => $role->value,
    ], $attributes));
}

it('binds and signs in a pre-provisioned admin by verified normalized email', function (): void {
    $admin = googleAccount(AccountRole::Admin, ['email' => 'Admin@Example.Test']);
    fakeSchoolGoogle('admin-google-id', 'admin@example.test');

    $this->withSession(['google_login_role' => 'admin'])
        ->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $admin->refresh();
    $this->assertAuthenticatedAs($admin);
    expect($admin->google_id)->toBe('admin-google-id')
        ->and($admin->name)->toBe('Nama Resmi Sekolah')
        ->and($admin->last_login_at)->not->toBeNull()
        ->and(session(ActiveSessionManager::SESSION_KEY))->toBe(1);
    $this->assertDatabaseHas('security_audit_logs', [
        'event' => 'auth.google.binding_succeeded',
        'actor_user_id' => $admin->id,
    ]);
});

it('binds and signs in a pre-provisioned guru without replacing the official name', function (): void {
    $guru = googleAccount(AccountRole::Guru);
    fakeSchoolGoogle('guru-google-id', $guru->email);

    $this->withSession(['google_login_role' => 'guru'])
        ->get(route('google.callback'))
        ->assertRedirect(route('guru.dashboard'));

    expect($guru->fresh()->google_id)->toBe('guru-google-id')
        ->and($guru->fresh()->name)->toBe('Nama Resmi Sekolah');
});

it('accepts an existing valid Google binding', function (): void {
    $admin = googleAccount(AccountRole::Admin, ['google_id' => 'linked-id']);
    fakeSchoolGoogle('linked-id', $admin->email);

    $this->withSession(['google_login_role' => 'admin'])
        ->get(route('google.callback'))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('does not create or privilege an unregistered Google account', function (): void {
    fakeSchoolGoogle('outsider-id', 'outsider@example.test');

    $this->withSession(['google_login_role' => 'guru'])
        ->get(route('google.callback'))
        ->assertRedirect(route('guru.login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'outsider@example.test']);
});

it('keeps a legacy inert account unbound and without a dashboard', function (): void {
    $inert = User::query()->forceCreate([
        'name' => 'Legacy Inert',
        'email' => 'legacy@example.test',
        'password' => Hash::make('unused-password'),
        'role' => null,
    ]);
    fakeSchoolGoogle('legacy-google-id', $inert->email);

    $this->withSession(['google_login_role' => 'guru'])
        ->get(route('google.callback'))
        ->assertRedirect(route('guru.login'));

    $this->assertGuest();
    expect($inert->fresh()->google_id)->toBeNull();
});

it('rejects disabled wrong-role and portal-mismatched accounts with the same message', function (array $state): void {
    $account = googleAccount($state['role'], $state['attributes']);
    fakeSchoolGoogle('denied-'.$state['name'], $account->email);

    $response = $this->withSession(['google_login_role' => $state['portal']])
        ->get(route('google.callback'))
        ->assertSessionHasErrors('email');

    expect($response->getSession()->get('errors')->first('email'))
        ->toBe(__('app.auth.errors.access_unavailable'))
        ->and($account->fresh()->google_id)->toBeNull();
    $this->assertGuest();
})->with([
    'disabled guru' => [[
        'name' => 'disabled', 'role' => AccountRole::Guru,
        'portal' => 'guru', 'attributes' => ['disabled_at' => now()],
    ]],
    'student on Google' => [[
        'name' => 'student', 'role' => AccountRole::Murid,
        'portal' => 'guru', 'attributes' => ['student_id' => 'S01'],
    ]],
    'admin on guru portal' => [[
        'name' => 'admin-portal', 'role' => AccountRole::Admin,
        'portal' => 'guru', 'attributes' => [],
    ]],
]);

it('rejects a conflicting Google identity without exposing identifiers in audit metadata', function (): void {
    $admin = googleAccount(AccountRole::Admin, ['google_id' => 'original-id']);
    fakeSchoolGoogle('different-id', $admin->email);

    $this->withSession(['google_login_role' => 'admin'])
        ->get(route('google.callback'))
        ->assertRedirect(route('login'));

    $log = SecurityAuditLog::query()
        ->where('event', 'auth.google.binding_conflict')->firstOrFail();
    expect($log->toJson())->not->toContain($admin->email)->not->toContain('different-id');
});

it('requires an explicitly verified Google email', function (array $raw): void {
    $guru = googleAccount(AccountRole::Guru);
    fakeSchoolGoogle('unverified-id', $guru->email, $raw);

    $this->withSession(['google_login_role' => 'guru'])
        ->get(route('google.callback'))
        ->assertRedirect(route('guru.login'));

    expect($guru->fresh()->google_id)->toBeNull();
})->with([
    'missing' => [[]],
    'false' => [['email_verified' => false]],
    'string true' => [['email_verified' => 'true']],
]);
