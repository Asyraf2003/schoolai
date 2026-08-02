<?php

namespace App\Services;

use App\Models\User;

final class AccountRevision
{
    public function value(): string
    {
        $state = User::query()
            ->orderBy('id')
            ->get([
                'id', 'name', 'email_normalized', 'student_id_normalized',
                'role', 'disabled_at', 'last_login_at', 'session_version',
                'updated_at',
            ])
            ->toJson();

        return hash('sha256', $state);
    }
}
