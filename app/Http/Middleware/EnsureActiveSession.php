<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureActiveSession
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $sessionVersion = $request->session()->get(
            ActiveSessionManager::SESSION_KEY,
        );

        if (
            $user instanceof User
            && is_numeric($sessionVersion)
            && hash_equals((string) $user->session_version, (string) $sessionVersion)
        ) {
            return $next($request);
        }

        if ($user instanceof User) {
            $this->auditLogger->record(
                'auth.session.rejected',
                actor: $user,
                subject: $user,
                metadata: ['reason' => 'version_mismatch'],
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($this->loginRoute($user));
    }

    private function loginRoute(?User $user): string
    {
        if ($user?->isGuru()) {
            return 'guru.login';
        }

        if ($user?->isMurid()) {
            return 'murid.login';
        }

        return 'login';
    }
}
