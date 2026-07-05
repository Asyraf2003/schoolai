<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\SiteStatisticController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/bahasa/{locale}', function (string $locale, Request $request) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);

    $request->session()->put('locale', $locale);

    $previous = url()->previous() ?: route('home');

    if (! str_starts_with($previous, $request->getSchemeAndHttpHost())) {
        $previous = route('home');
    }

    return redirect($previous)->withCookie(cookie('site_locale', $locale, 60 * 24 * 365));
})->whereIn('locale', ['id', 'en'])->name('language.switch');

Route::view('/ppdb', 'pages.ppdb')->name('ppdb');
Route::view('/artikel', 'pages.artikel')->name('artikel');
Route::view('/galeri', 'pages.galeri')->name('galeri');


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
        ->name('google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/admin', '/admin/stats')->name('admin.index');

    Route::get('/admin/stats', [SiteStatisticController::class, 'edit'])
        ->name('admin.stats.edit');

    Route::put('/admin/stats', [SiteStatisticController::class, 'update'])
        ->name('admin.stats.update');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
