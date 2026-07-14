<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    private const ADMIN_CLAIM_KEY = 'first_google_admin';

    public function redirect()
    {

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
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
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.google_missing_email'
                    ),
                ]);
        }

        if ($googleId === '') {
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

        $user = DB::transaction(
            function () use (
                $email,
                $googleId,
                $name,
                $bootstrapAdminId
            ): ?User {
                $state = DB::table('auth_bootstrap_states')
                    ->where('key', self::ADMIN_CLAIM_KEY)
                    ->lockForUpdate()
                    ->first();

                if (! $state) {
                    DB::table('auth_bootstrap_states')->insert([
                        'key' => self::ADMIN_CLAIM_KEY,
                        'claimed_user_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $state = DB::table('auth_bootstrap_states')
                        ->where('key', self::ADMIN_CLAIM_KEY)
                        ->lockForUpdate()
                        ->first();
                }

                $user = User::query()
                    ->where('google_id', $googleId)
                    ->lockForUpdate()
                    ->first();

                $emailOwner = User::query()
                    ->where('email', $email)
                    ->lockForUpdate()
                    ->first();

                /*
                 * Jangan pernah menautkan identitas Google baru
                 * ke akun lama hanya karena alamat email sama.
                 *
                 * Jika email sudah digunakan tetapi google_id
                 * tidak cocok, proses harus ditolak dan diperbaiki
                 * melalui prosedur administratif yang terpisah.
                 */
                if (! $user && $emailOwner) {
                    return null;
                }

                if (
                    $user
                    && $emailOwner
                    && $emailOwner->getKey() !== $user->getKey()
                ) {
                    return null;
                }

                if (! $user) {
                    $user = new User();
                    $user->password = Hash::make(
                        Str::random(64)
                    );
                    $user->role = User::ROLE_USER;
                }

                $user->name = $name !== ''
                    ? $name
                    : Str::before($email, '@');

                $user->email = $email;
                $user->google_id = $googleId;

                if (! $user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                $adminExists = User::query()
                    ->where('role', User::ROLE_ADMIN)
                    ->exists();

                $claimIsOpen = $state
                    && $state->claimed_user_id === null;

                if (
                    $claimIsOpen
                    && ! $adminExists
                    && $bootstrapAdminId !== ''
                    && $googleId === $bootstrapAdminId
                ) {
                    $user->role = User::ROLE_ADMIN;
                } elseif (
                    ! in_array(
                        $user->role,
                        [
                            User::ROLE_ADMIN,
                            User::ROLE_USER,
                        ],
                        true
                    )
                ) {
                    $user->role = User::ROLE_USER;
                }

                $user->save();

                if ($claimIsOpen && $user->isAdmin()) {
                    DB::table('auth_bootstrap_states')
                        ->where('key', self::ADMIN_CLAIM_KEY)
                        ->update([
                            'claimed_user_id' => $user->id,
                            'updated_at' => now(),
                        ]);
                }

                return $user;
            },
            attempts: 3,
        );

        if (! $user) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => __(
                        'app.auth.errors.google_identity_conflict'
                    ),
                ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

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
