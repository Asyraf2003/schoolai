<?php

namespace App\Actions\Auth;

use App\Enums\AccountRole;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\AccountIdentity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;

final class BootstrapAdmin
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function create(string $name, string $email): User
    {
        $name = trim($name);
        $email = AccountIdentity::email($email);

        if ($name === '' || mb_strlen($name) > 120) {
            throw new InvalidArgumentException('Admin name must contain 1 to 120 characters.');
        }

        if (
            $email === null
            || mb_strlen($email) > 255
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            throw new InvalidArgumentException('A valid admin Google email is required.');
        }

        return DB::transaction(function () use ($name, $email): User {
            if (User::query()->where('role', AccountRole::Admin->value)->exists()) {
                throw new LogicException('An admin account already exists. Bootstrap is disabled.');
            }

            if (User::query()->where('email_normalized', $email)->exists()) {
                throw new LogicException('That email already belongs to an existing account.');
            }

            $admin = new User;
            $admin->forceFill([
                'name' => $name,
                'email' => $email,
                'password' => Str::random(64),
                'role' => AccountRole::Admin->value,
                'google_id' => null,
                'disabled_at' => null,
                'email_verified_at' => null,
                'session_version' => 0,
            ])->save();

            $this->auditLogger->record(
                'account.admin.bootstrap_created',
                subject: $admin,
                metadata: ['source' => 'cli'],
            );

            return $admin->fresh();
        }, attempts: 3);
    }
}
