<?php

use App\Enums\AccountRole;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function roleAccount(AccountRole $role): User
{
    return User::query()->forceCreate([
        'name' => $role->label(),
        'email' => $role === AccountRole::Murid ? null : $role->value.'@example.test',
        'student_id' => $role === AccountRole::Murid ? 'M001' : null,
        'password' => Hash::make('password-aman'),
        'role' => $role->value,
    ]);
}

it('enforces strict role isolation', function (AccountRole $role, string $allowed, array $denied): void {
    $user = roleAccount($role);
    $this->actingAs($user)->withSession([ActiveSessionManager::SESSION_KEY => 0]);

    $this->get(route($allowed))->assertOk();
    foreach ($denied as $route) {
        $this->get(route($route))->assertForbidden();
    }
})->with([
    'admin' => [AccountRole::Admin, 'admin.dashboard', ['guru.dashboard', 'murid.dashboard']],
    'guru' => [AccountRole::Guru, 'guru.dashboard', ['admin.dashboard', 'murid.dashboard']],
    'murid' => [AccountRole::Murid, 'murid.dashboard', ['admin.dashboard', 'guru.dashboard']],
]);

it('redirects guests to the login belonging to each internal area', function (): void {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('guru.dashboard'))->assertRedirect(route('guru.login'));
    $this->get(route('murid.dashboard'))->assertRedirect(route('murid.login'));
});

it('rejects an older session version for every role', function (AccountRole $role, string $route, string $login): void {
    $user = roleAccount($role);
    $user->forceFill(['session_version' => 2])->saveQuietly();

    $this->actingAs($user)
        ->withSession([ActiveSessionManager::SESSION_KEY => 1])
        ->get(route($route))
        ->assertRedirect(route($login));

    $this->assertGuest();
})->with([
    [AccountRole::Admin, 'admin.dashboard', 'login'],
    [AccountRole::Guru, 'guru.dashboard', 'guru.login'],
    [AccountRole::Murid, 'murid.dashboard', 'murid.login'],
]);

it('advances the active marker on a second login for every role', function (AccountRole $role): void {
    $user = roleAccount($role);
    $manager = app(ActiveSessionManager::class);
    $firstStore = new Store('first-device', new ArraySessionHandler(120));
    $secondStore = new Store('second-device', new ArraySessionHandler(120));
    $firstStore->start();
    $secondStore->start();
    $firstRequest = Request::create('/login');
    $secondRequest = Request::create('/login');
    $firstRequest->setLaravelSession($firstStore);
    $secondRequest->setLaravelSession($secondStore);

    $manager->login($firstRequest, $user);
    $manager->login($secondRequest, $user->fresh());

    expect($firstStore->get(ActiveSessionManager::SESSION_KEY))->toBe(1)
        ->and($secondStore->get(ActiveSessionManager::SESSION_KEY))->toBe(2)
        ->and($user->fresh()->session_version)->toBe(2);
})->with(AccountRole::cases());

it('invalidates the session during logout', function (): void {
    $guru = roleAccount(AccountRole::Guru);

    $this->actingAs($guru)
        ->withSession([
            ActiveSessionManager::SESSION_KEY => 0,
            'private_marker' => 'remove-me',
        ])
        ->post(route('logout'))
        ->assertRedirect(route('guru.login'))
        ->assertSessionMissing('private_marker');

    $this->assertGuest();
    expect($guru->fresh()->session_version)->toBe(1);
});

it('revokes a disabled account before any internal area is served', function (AccountRole $role, string $route, string $login, string $errorKey): void {
    $user = roleAccount($role);
    $user->forceFill(['disabled_at' => now()])->saveQuietly();

    $this->actingAs($user)
        ->withSession([ActiveSessionManager::SESSION_KEY => 0])
        ->get(route($route))
        ->assertRedirect(route($login))
        ->assertSessionHasErrors($errorKey);

    $this->assertGuest();
    expect($user->fresh()->session_version)->toBe(1);
})->with([
    [AccountRole::Admin, 'admin.dashboard', 'login', 'email'],
    [AccountRole::Guru, 'guru.dashboard', 'guru.login', 'email'],
    [AccountRole::Murid, 'murid.dashboard', 'murid.login', 'credentials'],
]);
