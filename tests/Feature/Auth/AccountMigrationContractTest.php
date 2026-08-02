<?php

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('provides the account and session columns without the bootstrap table', function (): void {
    foreach ([
        'email_normalized',
        'student_id',
        'student_id_normalized',
        'password_changed_at',
        'session_version',
    ] as $column) {
        expect(Schema::hasColumn('users', $column))->toBeTrue();
    }

    expect(Schema::hasTable('auth_bootstrap_states'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'must_change_password'))->toBeFalse();
});

it('enforces case-insensitive student uniqueness at the database boundary', function (): void {
    User::query()->forceCreate([
        'name' => 'Murid Satu',
        'student_id' => 'Siswa01',
        'password' => Hash::make('password-aman'),
        'role' => User::ROLE_MURID,
    ]);

    expect(fn () => User::query()->forceCreate([
        'name' => 'Murid Dua',
        'student_id' => 'sISWA01',
        'password' => Hash::make('password-aman'),
        'role' => User::ROLE_MURID,
    ]))->toThrow(QueryException::class);
});

it('enforces case-insensitive email uniqueness at the database boundary', function (): void {
    User::query()->forceCreate([
        'name' => 'Guru Satu',
        'email' => 'Guru@Example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]);

    expect(fn () => User::query()->forceCreate([
        'name' => 'Guru Dua',
        'email' => 'guru@example.TEST',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]))->toThrow(QueryException::class);
});

it('keeps legacy inert users representable without deleting them', function (): void {
    $inert = User::query()->forceCreate([
        'name' => 'Akun Legacy',
        'email' => 'legacy-inert@example.test',
        'password' => Hash::make('password-aman'),
        'role' => null,
    ]);

    expect($inert->fresh()->role)->toBeNull()
        ->and($inert->hasPrivilegedRole())->toBeFalse();
});
