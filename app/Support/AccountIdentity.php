<?php

namespace App\Support;

final class AccountIdentity
{
    public const STUDENT_ID_PATTERN = '/\A[A-Za-z0-9]{1,32}\z/';

    public static function email(?string $email): ?string
    {
        $normalized = mb_strtolower(trim((string) $email));

        return $normalized === '' ? null : $normalized;
    }

    public static function studentId(?string $studentId): ?string
    {
        $normalized = mb_strtolower(trim((string) $studentId));

        return $normalized === '' ? null : $normalized;
    }
}
