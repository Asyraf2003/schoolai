<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:google-oauth')
        ->name('google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->middleware('throttle:google-oauth')
        ->name('google.callback');
});

Route::middleware(['auth', 'active.account'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return redirect()->route(
            $request->user()?->isAdmin()
                ? 'admin.dashboard'
                : 'account.locked'
        );
    })->name('dashboard');

    Route::view('/akun', 'account.locked')
        ->middleware('regular.user')
        ->name('account.locked');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});
