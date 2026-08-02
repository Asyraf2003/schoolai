<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Student\StudentPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'choose'])
        ->name('portal.login');
    Route::get('/login/admin', [LoginController::class, 'show'])
        ->name('login');
    Route::get('/login/guru', [LoginController::class, 'showGuru'])
        ->name('guru.login');
    Route::get('/login/murid', [StudentLoginController::class, 'show'])
        ->name('murid.login');
    Route::post('/login/murid', [StudentLoginController::class, 'store'])
        ->name('murid.login.store');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:google-oauth')
        ->name('google.redirect');

    Route::get('/auth/google/guru/redirect', [GoogleAuthController::class, 'redirectGuru'])
        ->middleware('throttle:google-oauth')
        ->name('google.guru.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->middleware('throttle:google-oauth')
        ->name('google.callback');
});

Route::middleware(['auth', 'active.account', 'active.session'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $route = match (true) {
            $request->user()?->isAdmin() => 'admin.dashboard',
            $request->user()?->isGuru() => 'guru.dashboard',
            $request->user()?->isMurid() => 'murid.dashboard',
            default => null,
        };

        abort_if($route === null, 403);

        return redirect()->route($route);
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

Route::view('/guru/dashboard', 'guru.dashboard')
    ->middleware(['auth', 'active.account', 'active.session', 'guru', 'internal.locale'])
    ->name('guru.dashboard');

Route::middleware([
    'auth',
    'active.account',
    'active.session',
    'murid',
    'internal.locale',
])->group(function (): void {
    Route::view('/murid/dashboard', 'student.dashboard')
        ->name('murid.dashboard');
    Route::view('/murid/akun', 'student.account')
        ->name('murid.account');
    Route::put('/murid/akun/password', [StudentPasswordController::class, 'update'])
        ->name('murid.password.update');
});
