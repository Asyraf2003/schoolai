<?php

use App\Enums\AccountRole;
use App\Models\PpdbSetting;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function portalAccount(AccountRole $role): User
{
    return User::query()->forceCreate([
        'name' => $role->label(),
        'email' => $role === AccountRole::Murid ? null : $role->value.'.portal@example.test',
        'student_id' => $role === AccountRole::Murid ? 'STU001' : null,
        'password' => Hash::make('password-aman'),
        'role' => $role->value,
    ]);
}

it('renders public pages in all three languages', function (string $locale, string $direction): void {
    foreach (['artikel' => 'article', 'galeri' => 'gallery'] as $route => $page) {
        $this->withSession(['locale' => $locale])
            ->get(route($route))
            ->assertOk()
            ->assertSee('dir="'.$direction.'"', false)
            ->assertSee($route === 'artikel' ? 'data-public-articles' : 'data-public-gallery', false);
    }
})->with([['id', 'ltr'], ['en', 'ltr'], ['ar', 'rtl']]);

it('links the header to gallery articles PPDB and login', function (): void {
    $response = $this->get(route('home'))->assertOk();
    $items = $response->viewData('navigation')['menuItems'];

    foreach ($items as $item) {
        if (($item['type'] ?? null) === 'language') {
            continue;
        }

        if (($item['type'] ?? null) === 'ppdb') {
            expect($item['href'])->toBe(PpdbSetting::sole()->registration_url);
        } elseif (($item['type'] ?? null) === 'login') {
            expect($item['href'])->toBe(route('portal.login'));
        } elseif (in_array('artikel', $item['route_patterns'] ?? [], true)) {
            expect($item['href'])->toBe(route('artikel'));
        } elseif (in_array('galeri', $item['route_patterns'] ?? [], true)) {
            expect($item['href'])->toBe(route('galeri'));
            foreach ($item['mega']['links'] ?? [] as $link) {
                expect($link['href'])->toBe(route('galeri'));
            }
        } else {
            expect($item['href'])->toBe('#');
            foreach ($item['mega']['links'] ?? [] as $link) {
                expect($link['href'])->toBe('#');
            }
        }
    }
});

it('redirects an authenticated visitor through the login entry to their dashboard', function (AccountRole $role, string $destination): void {
    $account = portalAccount($role);
    $this->actingAs($account)
        ->withSession([ActiveSessionManager::SESSION_KEY => 0])
        ->get(route('portal.login'))
        ->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))->assertRedirect(route($destination));

    $items = $this->get(route('home'))->viewData('navigation')['menuItems'];
    expect(collect($items)->firstWhere('type', 'login')['href'])->toBe(route('dashboard'));
})->with([
    [AccountRole::Admin, 'admin.dashboard'],
    [AccountRole::Guru, 'guru.dashboard'],
    [AccountRole::Murid, 'murid.dashboard'],
]);

it('enforces role isolation on placeholder dashboards', function (AccountRole $role, string $allowed, string $denied): void {
    $account = portalAccount($role);
    $this->actingAs($account)
        ->withSession([ActiveSessionManager::SESSION_KEY => 0])
        ->get(route($allowed))
        ->assertOk();

    $this->get(route($denied))->assertForbidden();
})->with([
    [AccountRole::Admin, 'admin.dashboard', 'murid.dashboard'],
    [AccountRole::Guru, 'guru.dashboard', 'admin.dashboard'],
    [AccountRole::Murid, 'murid.dashboard', 'guru.dashboard'],
]);

it('preserves the three existing login methods', function (): void {
    $this->get(route('portal.login'))->assertOk()
        ->assertSee(route('login'), false)
        ->assertSee(route('guru.login'), false)
        ->assertSee(route('murid.login'), false);
    $this->get(route('login'))->assertOk()->assertSee(route('google.redirect'), false);
    $this->get(route('guru.login'))->assertOk()->assertSee(route('google.guru.redirect'), false);
    $this->get(route('murid.login'))->assertOk()->assertSee(route('murid.login.store'), false);
});
