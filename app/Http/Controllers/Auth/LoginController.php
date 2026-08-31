<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly ActiveSessionManager $sessionManager,
    ) {}

    public function choose(): View
    {
        return view('auth.login-perspective', ['activeRole' => null]);
    }

    public function show(): View
    {
        return view('auth.login-perspective', ['activeRole' => 'admin']);
    }

    public function showGuru(): View
    {
        return view('auth.login-perspective', ['activeRole' => 'guru']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->auditLogger->record(
                match (true) {
                    $user->isAdmin() => 'auth.admin.logout',
                    $user->isGuru() => 'auth.guru.logout',
                    $user->isMurid() => 'auth.murid.logout',
                    default => 'auth.logout',
                },
                actor: $user,
                subject: $user,
            );
        }

        $redirectRoute = match (true) {
            $user?->isGuru() => 'guru.login',
            $user?->isMurid() => 'murid.login',
            default => 'login',
        };

        $this->sessionManager->logout($request);

        return redirect()
            ->route($redirectRoute)
            ->with(
                'success',
                __('app.auth.success.logged_out')
            );
    }
}
