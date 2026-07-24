<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

trait AuthenticatesGoogleUsers
{
    public function redirect()
    {

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            $this->auditLogger->record(
                'auth.google.login_failed',
                metadata: ['reason' => 'provider_error'],
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('app.auth.errors.google_failed'),
                ]);
        }

        $email = strtolower(
            trim((string) $googleUser->getEmail())
        );

        $googleId = trim((string) $googleUser->getId());

        $bootstrapAdminId = trim(
            (string) config(
                'services.google.bootstrap_admin_id',
                ''
            )
        );

        if ($email === '') {
            $this->auditLogger->record(
                'auth.google.login_denied',
                metadata: ['reason' => 'missing_email'],
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.google_missing_email'
                    ),
                ]);
        }

        if ($googleId === '') {
            $this->auditLogger->record(
                'auth.google.login_denied',
                metadata: ['reason' => 'missing_google_id'],
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __('app.auth.errors.google_failed'),
                ]);
        }

        $rawUser = $googleUser->user ?? [];

        if (array_key_exists('email_verified', $rawUser)) {
            $emailVerified = $rawUser['email_verified'];
        } elseif (array_key_exists('verified_email', $rawUser)) {
            $emailVerified = $rawUser['verified_email'];
        } else {
            $emailVerified = null;
        }

        if ($emailVerified !== true) {
            $this->auditLogger->record(
                'auth.google.login_denied',
                metadata: ['reason' => 'unverified_email'],
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.google_unverified_email'
                    ),
                ]);
        }

        $name = trim(
            (string) (
                $googleUser->getName()
                ?: $googleUser->getNickname()
                ?: Str::before($email, '@')
            )
        );

        $user = $this->resolveUser($email, $googleId, $name, $bootstrapAdminId);

        if (! $user) {
            $this->auditLogger->record(
                'auth.google.identity_conflict',
                metadata: ['reason' => 'stored_identity_mismatch'],
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.google_identity_conflict'
                    ),
                ]);
        }

        if ($user->isDisabled()) {
            $this->auditLogger->record(
                $user->isAdmin()
                    ? 'auth.admin.login_denied'
                    : 'auth.user.login_denied',
                actor: $user,
                subject: $user,
                metadata: ['reason' => 'account_disabled'],
            );
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.account_disabled'
                    ),
                ]);
        }

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            $this->auditLogger->record(
                'auth.admin.login_succeeded',
                actor: $user,
                subject: $user,
            );
        }

        return redirect()
            ->route(
                $user->isAdmin()
                    ? 'admin.dashboard'
                    : 'account.locked'
            )
            ->with(
                'success',
                __('app.auth.success.logged_in')
            );
    }
}
