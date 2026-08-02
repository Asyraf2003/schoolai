<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AccountRole;
use App\Services\AuditLogger;
use App\Support\AccountIdentity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

#[Fillable(['name', 'email', 'password'])]
#[Hidden([
    'password',
    'remember_token',
    'google_id',
    'email_normalized',
    'student_id_normalized',
    'session_version',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = AccountRole::Admin->value;

    public const ROLE_GURU = AccountRole::Guru->value;

    public const ROLE_MURID = AccountRole::Murid->value;

    protected $attributes = [
        'session_version' => 0,
    ];

    public function isAdmin(): bool
    {
        return $this->role === AccountRole::Admin;
    }

    public function isGuru(): bool
    {
        return $this->role === AccountRole::Guru;
    }

    public function isMurid(): bool
    {
        return $this->role === AccountRole::Murid;
    }

    public function hasPrivilegedRole(): bool
    {
        return $this->role instanceof AccountRole;
    }

    public function isDisabled(): bool
    {
        return $this->disabled_at !== null;
    }

    public function isActive(): bool
    {
        return ! $this->isDisabled();
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $email = AccountIdentity::email($user->email);
            $studentId = trim((string) $user->student_id);

            $user->email = $email;
            $user->email_normalized = $email;
            $user->student_id = $studentId === '' ? null : $studentId;
            $user->student_id_normalized = AccountIdentity::studentId($studentId);
        });

        static::updated(function (User $user): void {
            $actor = Auth::user();

            if ($user->wasChanged('disabled_at')) {
                app(AuditLogger::class)->record(
                    $user->isDisabled()
                        ? 'account.disabled'
                        : 'account.enabled',
                    actor: $actor instanceof User ? $actor : null,
                    subject: $user,
                    metadata: [
                        'role' => $user->role?->value,
                    ],
                );
            }

            if ($user->wasChanged('role')) {
                app(AuditLogger::class)->record(
                    'account.role_changed',
                    actor: $actor instanceof User ? $actor : null,
                    subject: $user,
                    metadata: [
                        'previous_role' => $user->getOriginal('role'),
                        'current_role' => $user->role?->value,
                    ],
                );
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'disabled_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'session_version' => 'integer',
            'role' => AccountRole::class,
            'password' => 'hashed',
        ];
    }
}
