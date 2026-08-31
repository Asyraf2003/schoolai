<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Enums\AccountRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

trait AuthenticatesGoogleUsers
{
    public function redirect(Request $request): RedirectResponse
    {
        $request->session()->put('google_login_role', AccountRole::Admin->value);
        $request->session()->put('google_login_popup', $request->boolean('popup'));
        $request->session()->put('google_login_popup_token', mb_substr((string) $request->query('popup_token', ''), 0, 128));

        return Socialite::driver('google')->redirect();
    }

    public function redirectGuru(Request $request): RedirectResponse
    {
        $request->session()->put('google_login_role', AccountRole::Guru->value);
        $request->session()->put('google_login_popup', $request->boolean('popup'));
        $request->session()->put('google_login_popup_token', mb_substr((string) $request->query('popup_token', ''), 0, 128));

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse|Response
    {
        $popup = (bool) $request->session()->pull('google_login_popup', false);
        $popupToken = (string) $request->session()->pull('google_login_popup_token', '');
        $intendedRole = AccountRole::tryFrom((string) $request->session()->pull('google_login_role'));
        $denialRoute = $intendedRole === AccountRole::Guru ? 'guru.login' : 'login';

        if (! in_array($intendedRole, [AccountRole::Admin, AccountRole::Guru], true)) {
            return $this->denyGoogle('invalid_login_intent', null, $denialRoute, $popup, $intendedRole?->value, $popupToken);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return $this->denyGoogle('provider_error', null, $denialRoute, $popup, $intendedRole->value, $popupToken);
        }

        $email = mb_strtolower(trim((string) $googleUser->getEmail()));
        $googleId = trim((string) $googleUser->getId());
        $rawUser = is_array($googleUser->user ?? null) ? $googleUser->user : [];
        $verified = $rawUser['email_verified'] ?? $rawUser['verified_email'] ?? null;

        if ($email === '' || $googleId === '' || $verified !== true) {
            return $this->denyGoogle('invalid_provider_identity', null, $denialRoute, $popup, $intendedRole->value, $popupToken);
        }

        $resolution = $this->resolveUser($email, $googleId, $intendedRole);
        $user = $resolution['user'];

        if ($resolution['reason'] === 'identity_conflict') {
            $this->auditLogger->record(
                'auth.google.binding_conflict',
                metadata: ['reason' => 'identity_conflict'],
            );
        }

        if (! $user instanceof User || $user->isDisabled() || $user->role !== $intendedRole) {
            return $this->denyGoogle('access_unavailable', $user, $denialRoute, $popup, $intendedRole->value, $popupToken);
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
            $user->isAdmin() ? 'auth.admin.login_succeeded' : 'auth.guru.login_succeeded',
            actor: $user,
            subject: $user,
        );

        $dashboardRoute = $user->isAdmin() ? 'admin.dashboard' : 'guru.dashboard';
        if ($popup) {
            return $this->googlePopupResult(
                true,
                $intendedRole->value,
                route($dashboardRoute),
                __('app.auth.success.logged_in'),
                route($denialRoute),
                $popupToken,
            );
        }

        return redirect()->route($dashboardRoute)->with('success', __('app.auth.success.logged_in'));
    }

    private function denyGoogle(
        string $reason,
        ?User $user = null,
        string $route = 'login',
        bool $popup = false,
        ?string $role = null,
        string $popupToken = '',
    ): RedirectResponse|Response {
        $this->auditLogger->record(
            'auth.google.login_denied',
            actor: $user,
            subject: $user,
            metadata: ['reason' => $reason],
        );

        $message = __('app.auth.errors.access_unavailable');
        if ($popup) {
            return $this->googlePopupResult(false, $role, null, $message, route($route), $popupToken);
        }

        return redirect()->route($route)->withErrors(['email' => $message]);
    }

    private function googlePopupResult(
        bool $ok,
        ?string $role,
        ?string $redirect,
        string $message,
        string $fallbackUrl,
        string $popupToken,
    ): Response {
        return response()->view('auth.google-popup-result', [
            'ok' => $ok,
            'role' => $role,
            'redirect' => $redirect,
            'message' => $message,
            'fallbackUrl' => $fallbackUrl,
            'popupToken' => $popupToken,
        ]);
    }
}
