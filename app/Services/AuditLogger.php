<?php

namespace App\Services;

use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuditLogger
{
    private const SENSITIVE_KEY_FRAGMENTS = [
        'authorization',
        'cookie',
        'env',
        'password',
        'secret',
        'session',
        'token',
    ];

    public function record(
        string $event,
        ?User $actor = null,
        ?Model $subject = null,
        array $metadata = [],
    ): SecurityAuditLog {
        $authenticatedUser = Auth::user();

        if (
            $actor === null
            && $authenticatedUser instanceof User
        ) {
            $actor = $authenticatedUser;
        }

        $request = app()->bound('request')
            ? request()
            : null;

        return SecurityAuditLog::query()->create([
            'actor_user_id' => $actor?->getKey(),
            'event' => mb_substr($event, 0, 120),
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey() !== null
                ? (string) $subject->getKey()
                : null,
            'ip_address' => $request instanceof Request
                ? $request->ip()
                : null,
            'user_agent' => $request instanceof Request
                ? mb_substr(
                    (string) $request->userAgent(),
                    0,
                    500
                )
                : null,
            'metadata' => $metadata === []
                ? null
                : $this->sanitizeMetadata($metadata),
        ]);
    }

    private function sanitizeMetadata(array $metadata): array
    {
        $sanitized = [];

        foreach ($metadata as $key => $value) {
            if (
                is_string($key)
                && $this->isSensitiveKey($key)
            ) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeMetadata(
                    $value
                );

                continue;
            }

            if (is_string($value)) {
                $sanitized[$key] = mb_substr(
                    $value,
                    0,
                    500
                );

                continue;
            }

            if (
                is_int($value)
                || is_float($value)
                || is_bool($value)
                || $value === null
            ) {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = mb_strtolower($key);

        foreach (
            self::SENSITIVE_KEY_FRAGMENTS
            as $fragment
        ) {
            if (str_contains($key, $fragment)) {
                return true;
            }
        }

        return false;
    }
}
