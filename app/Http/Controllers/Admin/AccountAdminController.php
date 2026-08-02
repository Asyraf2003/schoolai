<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AccountManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeAccountStatusRequest;
use App\Http\Requests\Admin\ResetStudentPasswordRequest;
use App\Http\Requests\Admin\StoreAccountRequest;
use App\Http\Requests\Admin\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\User;
use App\Services\AccountDirectory;
use App\Services\AccountRevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class AccountAdminController extends Controller
{
    public function index(AccountDirectory $directory): View
    {
        return view('admin.accounts.index', [
            'accounts' => $directory->list(null, null, null),
        ]);
    }

    public function data(
        Request $request,
        AccountDirectory $directory,
        AccountRevision $revision,
    ): JsonResponse|Response {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:20'],
        ]);
        $version = $revision->value();
        $etag = '"'.hash('sha256', $version.'|'.$request->getQueryString()).'"';

        if ($request->header('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        $accounts = $directory->list(
            $request->string('search')->toString(),
            $request->string('role')->toString(),
            $request->string('status')->toString(),
        )->map(fn (User $user): array => (new AccountResource($user))->resolve());

        return response()->json([
            'data' => $accounts,
            'revision' => $version,
        ])->header('ETag', $etag);
    }

    public function store(
        StoreAccountRequest $request,
        AccountManager $manager,
    ): JsonResponse|RedirectResponse {
        $account = $manager->create($request->user(), $request->validated());

        return $this->saved($request, $account, 'Akun berhasil dibuat.', 201);
    }

    public function update(
        UpdateAccountRequest $request,
        User $account,
        AccountManager $manager,
    ): JsonResponse|RedirectResponse {
        $account = $manager->update($request->user(), $account, $request->validated());

        return $this->saved($request, $account, 'Akun berhasil diperbarui.');
    }

    public function status(
        ChangeAccountStatusRequest $request,
        User $account,
        AccountManager $manager,
    ): JsonResponse|RedirectResponse {
        $account = $manager->setActive(
            $request->user(),
            $account,
            $request->boolean('active'),
        );

        return $this->saved($request, $account, 'Status akun berhasil diperbarui.');
    }

    public function resetPassword(
        ResetStudentPasswordRequest $request,
        User $account,
        AccountManager $manager,
    ): JsonResponse|RedirectResponse {
        $account = $manager->resetPassword(
            $request->user(),
            $account,
            $request->string('password')->toString(),
        );

        return $this->saved($request, $account, 'Password murid berhasil direset.');
    }

    private function saved(
        Request $request,
        User $account,
        string $message,
        int $status = 200,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => (new AccountResource($account))->resolve(),
            ], $status);
        }

        return back()->with('success', $message);
    }
}
