<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\ChangePasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ChangePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class StudentPasswordController extends Controller
{
    public function update(
        ChangePasswordRequest $request,
        ChangePasswordAction $action,
    ): JsonResponse|RedirectResponse {
        $student = $request->user();

        if (! Hash::check(
            $request->string('current_password')->toString(),
            $student->password,
        )) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak sesuai.'],
            ]);
        }

        $action->execute(
            $request,
            $student,
            $request->string('password')->toString(),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Password berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
