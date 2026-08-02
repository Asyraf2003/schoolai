<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Enums\AccountRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

trait AuthenticatesGoogleUsers
{
    public function redirect(Request $request): RedirectResponse
    {
        $request->session()->put('google_login_role', AccountRole::Admin->value);

        return Socialite::driver('google')->redirect();
    }

    public function redirectGuru(Request $request): RedirectResponse
    {
        $request->session()->put('google_login_role', AccountRole::Guru->value);

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $intendedRole = AccountRole::tryFrom((string) $request->session()->pull(
            'google_login_role',
        ));
        $denialRoute = $intendedRole === AccountRole::Guru
            ? 'guru.login'
            : 'login';

        if (! in_array($intendedRole, [AccountRole::Admin, AccountRole::Guru], true)) {
            return $this->denyGoogle('invalid_login_intent', null, $denialRoute);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return $this->denyGoogle('provider_error', null, $denialRoute);
        }

        $email = mb_strtolower(trim((string) $googleUser->getEmail()));
        $googleId = trim((string) $googleUser->getId());
        $rawUser = is_array($googleUser->user ?? null) ? $googleUser->user : [];
        $verified = $rawUser['email_verified']
            ?? $rawUser['verified_email']
            ?? null;

        if ($email === '' || $googleId === '' || $verified !== true) {
            return $this->denyGoogle('invalid_provider_identity', null, $denialRoute);
        }

        $resolution = $this->resolveUser($email, $googleId, $intendedRole);
        $user = $resolution['user'];

        if ($resolution['reason'] === 'identity_conflict') {
            $this->auditLogger->record(
                'auth.google.binding_conflict',
                metadata: ['reason' => 'identity_conflict'],
            );
        }

        if (
            ! $user instanceof User
            || $user->isDisabled()
            || $user->role !== $intendedRole
        ) {
            return $this->denyGoogle('access_unavailable', $user, $denialRoute);
        }

        if ($resolution['bound']) {
            $this->auditLogger->record(
                'auth.google.binding_succeeded',
                actor: $user,
                subject: $user,
            );
        }

        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        $user = $this->sessionManager->login($request, $user);

        $this->auditLogger->record(
            $user->isAdmin()
                ? 'auth.admin.login_succeeded'
                : 'auth.guru.login_succeeded',
            actor: $user,
            subject: $user,
        );

        return redirect()
            ->route($user->isAdmin() ? 'admin.dashboard' : 'guru.dashboard')
            ->with('success', __('app.auth.success.logged_in'));
    }

    private function denyGoogle(
        string $reason,
        ?User $user = null,
        string $route = 'login',
    ): RedirectResponse {
        $this->auditLogger->record(
            'auth.google.login_denied',
            actor: $user,
            subject: $user,
            metadata: ['reason' => $reason],
        );

        return redirect()
            ->route($route)
            ->withErrors(['email' => __('app.auth.errors.access_unavailable')]);
    }
}
