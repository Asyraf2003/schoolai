<?php

use App\Enums\AccountRole;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders public placeholders in all three languages', function (string $locale, string $direction): void {
    foreach (['artikel' => 'article', 'galeri' => 'gallery'] as $route => $page) {
        $this->withSession(['locale' => $locale])
            ->get(route($route))
            ->assertOk()
            ->assertSee('dir="'.$direction.'"', false)
            ->assertSee('data-portal-page="'.$page.'"', false);
    }
})->with([['id', 'ltr'], ['en', 'ltr'], ['ar', 'rtl']]);

it('uses inert header navigation except for PPDB and login', function (): void {
    $response = $this->get(route('home'))->assertOk();
    $items = $response->viewData('navigation')['menuItems'];

    foreach ($items as $item) {
        if (($item['type'] ?? null) === 'language') {
            continue;
        }

        if (($item['type'] ?? null) === 'ppdb') {
            expect($item['href'])->toBe(route('ppdb'));
        } elseif (($item['type'] ?? null) === 'login') {
            expect($item['href'])->toBe(route('portal.login'));
        } else {
            expect($item['href'])->toBe('#');
            foreach ($item['mega']['links'] ?? [] as $link) {
                expect($link['href'])->toBe('#');
            }
        }
    }
});

it('redirects an authenticated visitor through the login entry to their dashboard', function (AccountRole $role, string $destination): void {
    $account = User::factory()->create(['role' => $role->value, 'student_id' => $role === AccountRole::Murid ? 'STU001' : null]);
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
    $account = User::factory()->create(['role' => $role->value, 'student_id' => $role === AccountRole::Murid ? 'STU002' : null]);
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
