<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        $this->configureGoogleOAuth();

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        $this->configureGoogleOAuth();

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Login Google gagal. Coba lagi.']);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Google tidak memiliki email yang bisa digunakan.']);
        }

        $rawUser = $googleUser->user ?? [];
        $emailVerified = $rawUser['email_verified'] ?? $rawUser['verified_email'] ?? true;

        if ($emailVerified === false || $emailVerified === 'false' || $emailVerified === 0 || $emailVerified === '0') {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email Google belum terverifikasi.']);
        }

        $user = User::firstOrNew(['email' => $email]);

        if (! $user->exists) {
            $user->name = $googleUser->getName()
                ?: $googleUser->getNickname()
                ?: Str::before($email, '@');

            $user->password = Hash::make(Str::random(64));
        }

        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
        }

        $user->save();

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function configureGoogleOAuth(): void
    {
        config([
            'services.google.client_id' => env('GOOGLE_CLIENT_ID'),
            'services.google.client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'services.google.redirect' => env('GOOGLE_REDIRECT_URI'),
        ]);
    }
}
