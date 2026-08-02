<?php

use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function accountUiAdmin(): User
{
    return User::query()->forceCreate([
        'name' => 'Admin UI Akun',
        'email' => 'admin-ui-akun@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);
}

it('renders the Indonesian account manager without credential values', function (): void {
    $admin = accountUiAdmin();
    User::query()->forceCreate([
        'name' => 'Murid Tampilan',
        'student_id' => 'Tampil01',
        'password' => Hash::make('rahasia-tidak-tampil'),
        'role' => User::ROLE_MURID,
    ]);

    $this->actingAs($admin)
        ->withSession([
            ActiveSessionManager::SESSION_KEY => 0,
            'site_locale' => 'ar',
        ])
        ->get(route('admin.accounts.index'))
        ->assertOk()
        ->assertSee('<html lang="id">', escape: false)
        ->assertSee('Kelola akses admin, guru, dan murid')
        ->assertSee('Murid Tampilan')
        ->assertDontSee('rahasia-tidak-tampil');
});

it('changes the account ETag when visible account data changes', function (): void {
    $admin = accountUiAdmin();
    $teacher = User::query()->forceCreate([
        'name' => 'Nama Sebelum',
        'email' => 'etag@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]);
    $this->actingAs($admin)->withSession([ActiveSessionManager::SESSION_KEY => 0]);
    $oldEtag = $this->getJson(route('admin.accounts.data'))->headers->get('ETag');

    $this->putJson(route('admin.accounts.update', $teacher), [
        'name' => 'Nama Sesudah',
        'email' => $teacher->email,
    ])->assertOk();

    $this->withHeader('If-None-Match', $oldEtag)
        ->getJson(route('admin.accounts.data'))
        ->assertOk()
        ->assertJsonPath('data.1.name', 'Nama Sesudah');
});

it('uses safe DOM updates and visibility-aware lightweight polling', function (): void {
    $source = file_get_contents(resource_path('js/pages/admin-accounts.js'));

    expect($source)
        ->not->toContain('innerHTML')
        ->not->toContain('localStorage')
        ->toContain('textContent')
        ->toContain('visibilitychange')
        ->toContain('30000');
});
