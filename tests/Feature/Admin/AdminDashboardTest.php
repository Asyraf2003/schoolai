<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the real-data dashboard for an active admin', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'disabled_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Status Konten')
        ->assertSee('Aktivitas Terbaru')
        ->assertSee('Artikel publik')
        ->assertSee('PPDB');
});
