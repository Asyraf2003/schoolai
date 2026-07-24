<?php

use App\Http\Controllers\ArticlePageController;
use App\Http\Controllers\GalleryPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NativeArticleController;
use App\Http\Controllers\PpdbPageController;
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

Route::get('/ppdb', PpdbPageController::class)->name('ppdb');
Route::get('/artikel', ArticlePageController::class)->name('artikel');
Route::redirect('/artikel/adab-sebelum-prestasi', '/artikel', 301)->name('artikel.detail');
Route::get('/artikel/{article:slug}', [NativeArticleController::class, 'show'])
    ->name('artikel.native');
Route::get('/galeri', GalleryPageController::class)->name('galeri');
