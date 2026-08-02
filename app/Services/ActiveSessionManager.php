<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ActiveSessionManager
{
    public const SESSION_KEY = 'auth_session_version';

    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function login(Request $request, User $user): User
    {
        [$authenticated, $previousVersion] = $this->advance($user);

        Auth::login($authenticated, remember: false);
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, $authenticated->session_version);

        if ($previousVersion > 0) {
            $this->recordInvalidation($authenticated, 'new_login');
        }

        return $authenticated;
    }

    public function rotateCurrent(Request $request, User $user, string $reason): User
    {
        [$current] = $this->advance($user);

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, $current->session_version);
        Auth::setUser($current);
        $this->recordInvalidation($current, $reason);

        return $current;
    }

    public function invalidateAll(
        User $user,
        string $reason,
        ?User $actor = null,
    ): User {
        [$current] = $this->advance($user);
        $this->recordInvalidation($current, $reason, $actor);

        return $current;
    }

    public function logout(Request $request): void
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->invalidateAll($user, 'logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /** @return array{User, int} */
    private function advance(User $user): array
    {
        return DB::transaction(function () use ($user): array {
            $locked = User::query()->lockForUpdate()->findOrFail($user->getKey());
            $previousVersion = (int) $locked->session_version;

            $locked->forceFill([
                'session_version' => $previousVersion + 1,
            ])->saveQuietly();

            return [$locked->fresh(), $previousVersion];
        }, attempts: 3);
    }

    private function recordInvalidation(
        User $user,
        string $reason,
        ?User $actor = null,
    ): void {
        $this->auditLogger->record(
            'auth.session.invalidated',
            actor: $actor ?? $user,
            subject: $user,
            metadata: ['reason' => $reason],
        );
    }
}
