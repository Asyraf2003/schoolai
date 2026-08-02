<?php

use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('records account disablement and role changes without sensitive values', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Audit Account Admin',
        'email' => 'audit-account-admin@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);
    $target = User::query()->forceCreate([
        'name' => 'Audit Target User',
        'email' => 'audit-target@example.test',
        'password' => Hash::make('unused-password'),
        'google_id' => 'audit-target-google-id',
        'role' => User::ROLE_GURU,
    ]);
    $this->actingAs($admin);
    $target->forceFill([
        'disabled_at' => now(),
        'role' => User::ROLE_ADMIN,
    ])->save();

    $logs = SecurityAuditLog::query()
        ->where('auditable_type', $target->getMorphClass())
        ->where('auditable_id', (string) $target->id);
    expect($logs->clone()->pluck('event'))
        ->toContain('account.disabled')
        ->toContain('account.role_changed');
    expect($logs->get()->toJson())
        ->not->toContain('audit-target@example.test')
        ->not->toContain('audit-target-google-id')
        ->not->toContain('unused-password');
});
