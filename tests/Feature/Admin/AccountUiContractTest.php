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
        ->assertSee('<html lang="id" dir="ltr">', escape: false)
        ->assertSee('Tambah akun Guru atau Murid')
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

it('uses server-rendered forms for accounts without exposing credentials to client storage', function (): void {
    $this->actingAs(accountUiAdmin())->get(route('admin.accounts.index'))->assertOk()
        ->assertSee('action="'.route('admin.accounts.store').'"', false)
        ->assertSee('autocomplete="new-password"', false);
});
