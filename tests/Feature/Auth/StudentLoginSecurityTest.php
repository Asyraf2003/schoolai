<?php

use App\Actions\Auth\StudentLoginAction;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

function schoolStudent(array $attributes = []): User
{
    return User::query()->forceCreate(array_replace([
        'name' => 'Murid Aman',
        'email' => null,
        'student_id' => 'Siswa01',
        'password' => Hash::make('password-awal'),
        'role' => User::ROLE_MURID,
    ], $attributes));
}

it('logs a student in case-insensitively and activates one session', function (): void {
    $student = schoolStudent();

    $this->post(route('murid.login.store'), [
        'student_id' => 'sISWA01',
        'password' => 'password-awal',
    ])->assertRedirect(route('murid.dashboard'));

    $student->refresh();
    $this->assertAuthenticatedAs($student);
    expect($student->student_id)->toBe('Siswa01')
        ->and($student->student_id_normalized)->toBe('siswa01')
        ->and($student->last_login_at)->not->toBeNull()
        ->and($student->session_version)->toBe(1)
        ->and(session(ActiveSessionManager::SESSION_KEY))->toBe(1);
});

it('returns the same neutral message for unavailable student credentials', function (array $state): void {
    if ($state['create']) {
        schoolStudent($state['attributes']);
    }

    $response = $this->postJson(route('murid.login.store'), [
        'student_id' => $state['student_id'],
        'password' => 'wrong-password',
    ])->assertStatus(422);

    expect($response->json('message'))->toBe(__('app.auth.errors.student_credentials'));
    $this->assertGuest();
    $audit = SecurityAuditLog::query()
        ->where('event', 'auth.murid.login_failed')->latest('id')->firstOrFail();
    expect($audit->toJson())
        ->not->toContain($state['student_id'])
        ->not->toContain('wrong-password');
})->with([
    'unknown ID' => [[
        'create' => false, 'attributes' => [], 'student_id' => 'UNKNOWN',
    ]],
    'wrong password' => [[
        'create' => true, 'attributes' => [], 'student_id' => 'Siswa01',
    ]],
    'disabled' => [[
        'create' => true, 'attributes' => ['disabled_at' => now()], 'student_id' => 'Siswa01',
    ]],
    'wrong role' => [[
        'create' => true,
        'attributes' => ['role' => User::ROLE_GURU, 'email' => 'guru@example.test'],
        'student_id' => 'Siswa01',
    ]],
]);

it('rejects spaces punctuation emoji and overlong student IDs', function (string $studentId): void {
    $response = $this->postJson(route('murid.login.store'), [
        'student_id' => $studentId,
        'password' => 'password-awal',
    ])->assertStatus(422);

    expect($response->json('errors.student_id'))->toBeArray();
})->with(['A B', 'A-1', '🙂', str_repeat('A', 33), '']);

it('locks on the fifth failure for sixty seconds and refresh cannot clear it', function (): void {
    schoolStudent();
    $payload = ['student_id' => 'SISWA01', 'password' => 'wrong'];

    foreach (range(1, 4) as $attempt) {
        $this->postJson(route('murid.login.store'), $payload)->assertStatus(422);
    }

    $locked = $this->postJson(route('murid.login.store'), $payload)
        ->assertStatus(429)
        ->assertJsonPath('retry_after', fn (int $seconds): bool => $seconds > 0);
    expect($locked->headers->has('Retry-After'))->toBeTrue();
    $this->get(route('murid.login'))->assertOk();
    $this->postJson(route('murid.login.store'), $payload)->assertStatus(429);

    $this->travel(61)->seconds();
    $this->postJson(route('murid.login.store'), $payload)->assertStatus(422);
});

it('clears the limiter after successful login', function (): void {
    schoolStudent();
    $request = Request::create('/login/murid', 'POST', server: ['REMOTE_ADDR' => '127.0.0.1']);
    $action = app(StudentLoginAction::class);
    $key = $action->limiterKey($request, 'Siswa01');

    RateLimiter::hit($key, 60);
    RateLimiter::hit($key, 60);

    $this->post(route('murid.login.store'), [
        'student_id' => 'siswa01',
        'password' => 'password-awal',
    ])->assertRedirect(route('murid.dashboard'));

    expect(RateLimiter::attempts($key))->toBe(0);
});

it('does not place the raw student ID or IP address in the limiter key', function (): void {
    $request = Request::create('/login/murid', 'POST', server: ['REMOTE_ADDR' => '203.0.113.19']);
    $key = app(StudentLoginAction::class)->limiterKey($request, 'Sensitive01');

    expect($key)->not->toContain('Sensitive01')
        ->not->toContain('sensitive01')
        ->not->toContain('203.0.113.19');
});
