<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortalPlaceholderController;
use App\Http\Controllers\PublicArticleController;
use App\Services\PpdbAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::post('/bahasa/{locale}', function (string $locale, Request $request) {
    abort_unless(in_array($locale, ['id', 'en', 'ar'], true), 404);

    $request->session()->put('locale', $locale);

    $previous = url()->previous() ?: route('home');
    $host = $request->getSchemeAndHttpHost();

    if (! str_starts_with($previous, $host)) {
        $previous = route('home');
    }

    $previousPath = parse_url($previous, PHP_URL_PATH) ?: '/';

    if (
        $previousPath === '/admin' ||
        str_starts_with($previousPath, '/admin/') ||
        $previousPath === '/login' ||
        str_starts_with($previousPath, '/auth/')
    ) {
        $previous = route('home');
    }

    return redirect($previous)->withCookie(cookie('site_locale', $locale, 60 * 24 * 365));
})->whereIn('locale', ['id', 'en', 'ar'])->name('language.switch');

Route::get('/ppdb', function (PpdbAccess $access) {
    $url = $access->current()->publicRegistrationUrl();
    abort_if($url === null, 404);

    return redirect()->away($url);
})->name('ppdb');
Route::get('/artikel', [PublicArticleController::class, 'index'])->name('artikel');
Route::get('/artikel/{article:slug}', [PublicArticleController::class, 'show'])
    ->name('artikel.native');
Route::get('/galeri', PortalPlaceholderController::class)->name('galeri');
