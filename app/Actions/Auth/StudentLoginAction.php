<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;
use App\Support\AccountIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

final class StudentLoginAction
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    private static ?string $dummyHash = null;

    public function __construct(
        private readonly ActiveSessionManager $sessionManager,
        private readonly AuditLogger $auditLogger,
    ) {}

    /** @return array{ok:bool,locked:bool,retry_after:int,user:?User} */
    public function execute(Request $request, string $studentId, string $password): array
    {
        $key = $this->limiterKey($request, $studentId);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            return $this->result(false, true, RateLimiter::availableIn($key));
        }

        $normalized = AccountIdentity::studentId($studentId);
        $user = User::query()->where('student_id_normalized', $normalized)->first();
        $hash = $user?->password ?? $this->dummyHash();
        $validPassword = Hash::check($password, $hash);

        if (! $validPassword || ! $user?->isMurid() || $user->isDisabled()) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            $locked = RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS);
            $this->auditLogger->record(
                'auth.murid.login_failed',
                metadata: ['reason' => 'credentials_unavailable'],
            );

            return $this->result(
                false,
                $locked,
                $locked ? RateLimiter::availableIn($key) : 0,
            );
        }

        RateLimiter::clear($key);
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        $user = $this->sessionManager->login($request, $user);
        $this->auditLogger->record(
            'auth.murid.login_succeeded',
            actor: $user,
            subject: $user,
        );

        return $this->result(true, false, 0, $user);
    }

    public function limiterKey(Request $request, string $studentId): string
    {
        $key = app('encrypter')->getKey();
        $identity = hash_hmac(
            'sha256',
            (string) AccountIdentity::studentId($studentId),
            $key,
        );
        $source = hash_hmac('sha256', (string) $request->ip(), $key);

        return 'student-login:'.$identity.':'.$source;
    }

    private function dummyHash(): string
    {
        return self::$dummyHash ??= Hash::make(bin2hex(random_bytes(24)));
    }

    /** @return array{ok:bool,locked:bool,retry_after:int,user:?User} */
    private function result(
        bool $ok,
        bool $locked,
        int $retryAfter,
        ?User $user = null,
    ): array {
        return [
            'ok' => $ok,
            'locked' => $locked,
            'retry_after' => $retryAfter,
            'user' => $user,
        ];
    }
}
