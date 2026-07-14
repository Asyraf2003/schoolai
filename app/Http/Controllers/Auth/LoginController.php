<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger
    ) {
    }

    public function show()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            $this->auditLogger->record(
                'auth.admin.logout',
                actor: $user,
                subject: $user,
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                __('app.auth.success.logged_out')
            );
    }
}
