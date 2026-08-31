<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\StudentLoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StudentLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class StudentLoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login-perspective', ['activeRole' => 'murid']);
    }

    public function store(
        StudentLoginRequest $request,
        StudentLoginAction $action,
    ): JsonResponse|RedirectResponse {
        $result = $action->execute(
            $request,
            $request->string('student_id')->toString(),
            $request->string('password')->toString(),
        );

        if ($result['ok']) {
            $redirect = route('murid.dashboard');

            return $request->expectsJson()
                ? response()->json(['ok' => true, 'redirect' => $redirect])
                : redirect()->to($redirect)->with('success', __('app.auth.success.logged_in'));
        }

        $message = __(
            $result['locked']
                ? 'app.auth.errors.student_locked'
                : 'app.auth.errors.student_credentials',
        );
        $status = $result['locked'] ? 429 : 422;

        if ($request->expectsJson()) {
            $response = response()->json([
                'message' => $message,
                'errors' => ['credentials' => [$message]],
                'retry_after' => $result['retry_after'],
            ], $status);

            if ($result['locked']) {
                $response->headers->set(
                    'Retry-After',
                    (string) max(1, $result['retry_after']),
                );
            }

            return $response;
        }

        return back()
            ->withInput(['student_id' => $request->string('student_id')->toString()])
            ->withErrors(['credentials' => $message])
            ->with('retry_after', $result['retry_after']);
    }
}
