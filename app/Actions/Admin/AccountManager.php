<?php

namespace App\Actions\Admin;

use App\Enums\AccountRole;
use App\Models\User;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AccountManager
{
    public function __construct(
        private readonly ActiveSessionManager $sessionManager,
        private readonly AuditLogger $auditLogger,
    ) {}

    /** @param array<string, mixed> $data */
    public function create(User $actor, array $data): User
    {
        return DB::transaction(function () use ($actor, $data): User {
            $role = AccountRole::from($data['role']);
            $account = new User;
            $account->forceFill([
                'name' => trim($data['name']),
                'email' => $role === AccountRole::Guru ? $data['email'] : null,
                'student_id' => $role === AccountRole::Murid ? $data['student_id'] : null,
                'password' => $role === AccountRole::Murid
                    ? $data['password']
                    : Str::random(64),
                'role' => $role->value,
            ])->save();

            $this->auditLogger->record(
                'account.'.$role->value.'.created',
                actor: $actor,
                subject: $account,
            );

            return $account;
        }, attempts: 3);
    }

    /** @param array<string, mixed> $data */
    public function update(User $actor, User $account, array $data): User
    {
        return DB::transaction(function () use ($actor, $account, $data): User {
            $account = User::query()->lockForUpdate()->findOrFail($account->id);
            $oldEmail = $account->email_normalized;
            $attributes = ['name' => trim($data['name'])];

            if ($account->isAdmin() || $account->isGuru()) {
                $attributes['email'] = $data['email'];
            } elseif ($account->isMurid()) {
                $attributes['student_id'] = $data['student_id'];
            }

            $account->forceFill($attributes)->save();

            if (
                ($account->isAdmin() || $account->isGuru())
                && $oldEmail !== $account->email_normalized
                && $account->google_id !== null
            ) {
                $account->forceFill(['google_id' => null])->saveQuietly();
                $this->sessionManager->invalidateAll(
                    $account,
                    'google_email_changed',
                    $actor,
                );
            }

            $this->auditLogger->record(
                'account.'.($account->role?->value ?? 'inert').'.updated',
                actor: $actor,
                subject: $account,
            );

            return $account->fresh();
        }, attempts: 3);
    }

    public function setActive(User $actor, User $account, bool $active): User
    {
        if (! $active && $actor->is($account)) {
            throw ValidationException::withMessages([
                'active' => ['Akun yang sedang digunakan tidak dapat dinonaktifkan.'],
            ]);
        }

        return DB::transaction(function () use ($actor, $account, $active): User {
            $account = User::query()->lockForUpdate()->findOrFail($account->id);

            if (! $active && $account->isAdmin() && $this->activeAdminCount() <= 1) {
                throw ValidationException::withMessages([
                    'active' => ['Admin aktif terakhir tidak dapat dinonaktifkan.'],
                ]);
            }

            $account->forceFill([
                'disabled_at' => $active ? null : now(),
            ])->save();

            if (! $active) {
                return $this->sessionManager->invalidateAll(
                    $account,
                    'account_disabled',
                    $actor,
                );
            }

            return $account->fresh();
        }, attempts: 3);
    }

    public function resetPassword(User $actor, User $student, string $password): User
    {
        abort_unless($student->isMurid(), 404);

        return DB::transaction(function () use ($actor, $student, $password): User {
            $student = User::query()->lockForUpdate()->findOrFail($student->id);
            abort_unless($student->isMurid(), 404);
            $student->forceFill([
                'password' => $password,
                'password_changed_at' => now(),
            ])->saveQuietly();
            $student = $this->sessionManager->invalidateAll(
                $student,
                'admin_password_reset',
                $actor,
            );
            $this->auditLogger->record(
                'account.murid.password_reset',
                actor: $actor,
                subject: $student,
            );

            return $student;
        }, attempts: 3);
    }

    private function activeAdminCount(): int
    {
        return User::query()
            ->where('role', AccountRole::Admin->value)
            ->whereNull('disabled_at')
            ->lockForUpdate()
            ->get(['id'])
            ->count();
    }
}
