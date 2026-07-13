<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('app.auth.errors.invalid_credentials'),
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        return redirect()
            ->route(
                $user && $user->isAdmin()
                    ? 'admin.dashboard'
                    : 'account.locked'
            )
            ->with(
                'success',
                __('app.auth.success.logged_in')
            );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', __('app.auth.success.logged_out'));
    }
}
