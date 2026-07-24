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

trait ResolvesGoogleUsers
{
    private function resolveUser(
        string $email,
        string $googleId,
        string $name,
        string $bootstrapAdminId,
    ): ?User {
        return DB::transaction(
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
    }
}
