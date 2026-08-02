<?php

use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('changes a student password and preserves only the current session', function (): void {
    $student = User::query()->forceCreate([
        'name' => 'Murid Password',
        'student_id' => 'PW01',
        'password' => Hash::make('password-lama'),
        'role' => User::ROLE_MURID,
    ]);

    $this->actingAs($student)
        ->withSession([ActiveSessionManager::SESSION_KEY => 0])
        ->putJson(route('murid.password.update'), [
            'current_password' => 'password-lama',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertOk()->assertJsonPath('ok', true);

    $student->refresh();
    expect(Hash::check('password-lama', $student->password))->toBeFalse()
        ->and(Hash::check('password-baru', $student->password))->toBeTrue()
        ->and($student->password_changed_at)->not->toBeNull()
        ->and($student->session_version)->toBe(1)
        ->and(session(ActiveSessionManager::SESSION_KEY))->toBe(1);
    $this->assertAuthenticatedAs($student);

    $audit = SecurityAuditLog::query()
        ->where('event', 'account.murid.password_changed')->firstOrFail();
    expect($audit->toJson())->not->toContain('password-lama')->not->toContain('password-baru');
});

it('requires the current password and confirmation without changing the hash', function (): void {
    $student = User::query()->forceCreate([
        'name' => 'Murid Password',
        'student_id' => 'PW02',
        'password' => Hash::make('password-lama'),
        'role' => User::ROLE_MURID,
    ]);
    $hash = $student->password;

    $this->actingAs($student)
        ->withSession([ActiveSessionManager::SESSION_KEY => 0])
        ->putJson(route('murid.password.update'), [
            'current_password' => 'salah',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertStatus(422)->assertJsonValidationErrors('current_password');

    expect($student->fresh()->password)->toBe($hash);
});
