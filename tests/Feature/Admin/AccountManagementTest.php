<?php

use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function provisionAccountAdmin(): User
{
    return User::query()->forceCreate([
        'name' => 'Admin Akun',
        'email' => 'admin-akun@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);
}

function signInAccountAdmin($test, User $admin): void
{
    $test->actingAs($admin)->withSession([
        ActiveSessionManager::SESSION_KEY => $admin->session_version,
    ]);
}

it('creates teachers and students without exposing credentials', function (): void {
    $admin = provisionAccountAdmin();
    signInAccountAdmin($this, $admin);

    $teacherResponse = $this->postJson(route('admin.accounts.store'), [
        'role' => User::ROLE_GURU,
        'name' => 'Guru Resmi',
        'email' => 'Guru.Resmi@Example.TEST',
    ])->assertCreated()
        ->assertJsonMissingPath('data.google_id')
        ->assertJsonMissingPath('data.session_version');

    $studentResponse = $this->postJson(route('admin.accounts.store'), [
        'role' => User::ROLE_MURID,
        'name' => 'Murid Resmi',
        'student_id' => 'SiswaA01',
        'password' => 'password-awal',
        'password_confirmation' => 'password-awal',
    ])->assertCreated();

    $teacher = User::query()->where('email_normalized', 'guru.resmi@example.test')->firstOrFail();
    $student = User::query()->where('student_id_normalized', 'siswaa01')->firstOrFail();

    expect($teacher->email)->toBe('guru.resmi@example.test')
        ->and($teacher->name)->toBe('Guru Resmi')
        ->and($student->student_id)->toBe('SiswaA01')
        ->and(Hash::check('password-awal', $student->password))->toBeTrue()
        ->and($teacherResponse->getContent())->not->toContain('password')
        ->and($studentResponse->getContent())->not->toContain('password-awal')
        ->not->toContain('password');
    $this->assertDatabaseHas('security_audit_logs', [
        'event' => 'account.guru.created',
        'auditable_id' => (string) $teacher->id,
    ]);
    $this->assertDatabaseHas('security_audit_logs', [
        'event' => 'account.murid.created',
        'auditable_id' => (string) $student->id,
    ]);
});

it('rejects duplicate email and student ID regardless of case', function (): void {
    $admin = provisionAccountAdmin();
    signInAccountAdmin($this, $admin);
    User::query()->forceCreate([
        'name' => 'Guru Lama',
        'email' => 'guru@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]);
    User::query()->forceCreate([
        'name' => 'Murid Lama',
        'student_id' => 'SISWA01',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_MURID,
    ]);

    $this->postJson(route('admin.accounts.store'), [
        'role' => User::ROLE_GURU,
        'name' => 'Guru Duplikat',
        'email' => 'GURU@example.test',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    $this->postJson(route('admin.accounts.store'), [
        'role' => User::ROLE_MURID,
        'name' => 'Murid Duplikat',
        'student_id' => 'siswa01',
        'password' => 'password-baru',
        'password_confirmation' => 'password-baru',
    ])->assertUnprocessable()->assertJsonValidationErrors('student_id_normalized');
});

it('updates status and resets a student password with session invalidation', function (): void {
    $admin = provisionAccountAdmin();
    $student = User::query()->forceCreate([
        'name' => 'Murid Dikelola',
        'student_id' => 'M001',
        'password' => Hash::make('password-lama'),
        'role' => User::ROLE_MURID,
        'session_version' => 4,
    ]);
    signInAccountAdmin($this, $admin);

    $this->patchJson(route('admin.accounts.status', $student), [
        'active' => false,
    ])->assertOk()->assertJsonPath('data.active', false);

    expect($student->fresh()->session_version)->toBe(5)
        ->and($student->fresh()->disabled_at)->not->toBeNull();

    $response = $this->putJson(route('admin.accounts.password.reset', $student), [
        'password' => 'password-reset',
        'password_confirmation' => 'password-reset',
    ])->assertOk();

    $student->refresh();
    expect(Hash::check('password-reset', $student->password))->toBeTrue()
        ->and(Hash::check('password-lama', $student->password))->toBeFalse()
        ->and($student->session_version)->toBe(6)
        ->and($response->getContent())->not->toContain('password-reset');

    $audit = SecurityAuditLog::query()->where('event', 'account.murid.password_reset')->firstOrFail();
    expect(json_encode($audit->metadata))->not->toContain('password');

    $this->patchJson(route('admin.accounts.status', $student), [
        'active' => true,
    ])->assertOk()->assertJsonPath('data.active', true);
    expect($student->fresh()->disabled_at)->toBeNull();
    expect(SecurityAuditLog::query()->pluck('event'))
        ->toContain('account.disabled')
        ->toContain('account.enabled');
});

it('filters account data and returns an ETag-aware unchanged response', function (): void {
    $admin = provisionAccountAdmin();
    User::query()->forceCreate([
        'name' => 'Guru Dicari',
        'email' => 'cari@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]);
    signInAccountAdmin($this, $admin);

    $first = $this->getJson(route('admin.accounts.data', [
        'search' => 'dicari',
        'role' => 'guru',
        'status' => 'active',
    ]))->assertOk()->assertJsonCount(1, 'data');
    $etag = $first->headers->get('ETag');

    expect($etag)->not->toBeNull();
    $this->withHeader('If-None-Match', $etag)
        ->getJson(route('admin.accounts.data', [
            'search' => 'dicari',
            'role' => 'guru',
            'status' => 'active',
        ]))->assertStatus(304)->assertHeader('ETag', $etag);
});

it('keeps account mutations behind admin authorization and CSRF', function (): void {
    $teacher = User::query()->forceCreate([
        'name' => 'Guru Tanpa Akses',
        'email' => 'tanpa-akses@example.test',
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_GURU,
    ]);
    $this->actingAs($teacher)->withSession([ActiveSessionManager::SESSION_KEY => 0]);
    $this->postJson(route('admin.accounts.store'), [])->assertForbidden();

    $admin = provisionAccountAdmin();
    signInAccountAdmin($this, $admin);
    app()->detectEnvironment(fn (): string => 'production');

    try {
        $this->post(route('admin.accounts.store'), [
            'role' => User::ROLE_GURU,
            'name' => 'Tanpa Token',
            'email' => 'tanpa-token@example.test',
        ])->assertStatus(419);
    } finally {
        app()->detectEnvironment(fn (): string => 'testing');
    }
});
