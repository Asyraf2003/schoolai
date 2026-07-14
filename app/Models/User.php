<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\AuditLogger;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isRegularUser(): bool
    {
        return $this->role === self::ROLE_USER;
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
                        'role' => $user->role,
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
                        'current_role' => $user->role,
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
            'password' => 'hashed',
        ];
    }
}
