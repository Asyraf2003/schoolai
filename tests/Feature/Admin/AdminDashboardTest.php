<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the account management entry for an active admin', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'disabled_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Akun')
        ->assertSee('Galeri')
        ->assertSee('Artikel')
        ->assertSee('PPDB');
});
