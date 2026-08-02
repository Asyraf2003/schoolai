<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ActiveSessionManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAccountIsActive
{
    public function __construct(
        private readonly ActiveSessionManager $sessionManager,
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user || $user->isActive()) {
            return $next($request);
        }

        $route = match (true) {
            $user instanceof User && $user->isGuru() => 'guru.login',
            $user instanceof User && $user->isMurid() => 'murid.login',
            default => 'login',
        };
        $errorKey = $user instanceof User && $user->isMurid()
            ? 'credentials'
            : 'email';

        $this->sessionManager->logout($request);

        return redirect()
            ->route($route)
            ->withErrors([
                $errorKey => __(
                    'app.auth.errors.access_unavailable'
                ),
            ]);
    }
}
