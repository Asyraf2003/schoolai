<?php

namespace App\Actions\Student;

use App\Models\User;
use App\Services\ActiveSessionManager;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

final class ChangePasswordAction
{
    public function __construct(
        private readonly ActiveSessionManager $sessionManager,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function execute(Request $request, User $student, string $password): User
    {
        $student->forceFill([
            'password' => $password,
            'password_changed_at' => now(),
        ])->saveQuietly();

        $student = $this->sessionManager->rotateCurrent(
            $request,
            $student,
            'student_password_changed',
        );
        $this->auditLogger->record(
            'account.murid.password_changed',
            actor: $student,
            subject: $student,
        );

        return $student;
    }
}
