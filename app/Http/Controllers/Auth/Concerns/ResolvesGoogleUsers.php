<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Enums\AccountRole;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

trait ResolvesGoogleUsers
{
    /** @return array{user:?User,bound:bool,reason:string} */
    private function resolveUser(
        string $email,
        string $googleId,
        AccountRole $intendedRole,
    ): array {
        try {
            return DB::transaction(function () use ($email, $googleId, $intendedRole): array {
                $googleOwner = User::query()
                    ->where('google_id', $googleId)
                    ->lockForUpdate()
                    ->first();
                $emailOwner = User::query()
                    ->where('email_normalized', $email)
                    ->lockForUpdate()
                    ->first();

                if ($googleOwner instanceof User) {
                    $sameOwner = ! $emailOwner
                        || $emailOwner->is($googleOwner);
                    $sameEmail = hash_equals(
                        (string) $googleOwner->email_normalized,
                        $email,
                    );

                    if (! $sameOwner || ! $sameEmail) {
                        return $this->googleResolution(null, false, 'identity_conflict');
                    }

                    return $this->googleResolution($googleOwner, false, 'linked');
                }

                if (! $emailOwner instanceof User) {
                    return $this->googleResolution(null, false, 'unavailable');
                }

                if ($emailOwner->google_id !== null) {
                    return $this->googleResolution(null, false, 'identity_conflict');
                }

                if (
                    $emailOwner->isDisabled()
                    || $emailOwner->role !== $intendedRole
                ) {
                    return $this->googleResolution(null, false, 'unavailable');
                }

                $emailOwner->forceFill([
                    'google_id' => $googleId,
                    'email_verified_at' => $emailOwner->email_verified_at ?? now(),
                ])->saveQuietly();

                return $this->googleResolution($emailOwner->fresh(), true, 'bound');
            }, attempts: 3);
        } catch (UniqueConstraintViolationException) {
            return $this->googleResolution(null, false, 'identity_conflict');
        }
    }

    /** @return array{user:?User,bound:bool,reason:string} */
    private function googleResolution(?User $user, bool $bound, string $reason): array
    {
        return compact('user', 'bound', 'reason');
    }
}
