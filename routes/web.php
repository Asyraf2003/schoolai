<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GalleryPageController;
use App\Http\Controllers\Admin\SiteStatisticController;
use App\Http\Controllers\Admin\GalleryAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/bahasa/{locale}', function (string $locale, Request $request) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);

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
})->whereIn('locale', ['id', 'en'])->name('language.switch');

Route::view('/ppdb', 'pages.ppdb')->name('ppdb');
Route::view('/artikel', 'pages.artikel')->name('artikel');
Route::view('/artikel/adab-sebelum-prestasi', 'pages.artikel-detail')->name('artikel.detail');
Route::get('/galeri', GalleryPageController::class)->name('galeri');


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

Route::middleware(['auth', 'admin.locale'])->group(function () {
    /* ADMIN_DESKTOP_DUMMY_ROUTES_FINAL */
    Route::redirect('/admin', '/admin/dashboard')->name('admin.index');

    Route::view('/admin/dashboard', 'admin.placeholder', ['adminPageKey' => 'dashboard'])
        ->name('admin.dashboard');

    Route::view('/admin/ppdb', 'admin.placeholder', ['adminPageKey' => 'ppdb'])
        ->name('admin.ppdb');

    Route::view('/admin/artikel', 'admin.placeholder', ['adminPageKey' => 'artikel'])
        ->name('admin.artikel');

    /* REAL_GALLERY_CRUD_ROUTES_FINAL */
    Route::get('/admin/galeri', [GalleryAdminController::class, 'index'])
        ->name('admin.galeri');

    Route::get('/admin/galeri/create', [GalleryAdminController::class, 'create'])
        ->name('admin.galeri.create');

    Route::post('/admin/galeri', [GalleryAdminController::class, 'store'])
        ->name('admin.galeri.store');

    Route::get('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'show'])
        ->name('admin.galeri.show');

    Route::get('/admin/galeri/{galleryItem}/edit', [GalleryAdminController::class, 'edit'])
        ->name('admin.galeri.edit');

    Route::put('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'update'])
        ->name('admin.galeri.update');

    Route::delete('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'destroy'])
        ->name('admin.galeri.destroy');

    Route::patch('/admin/galeri/{galleryItem}/toggle', [GalleryAdminController::class, 'toggle'])
        ->name('admin.galeri.toggle');

    Route::patch('/admin/galeri/{galleryItem}/move-up', [GalleryAdminController::class, 'moveUp'])
        ->name('admin.galeri.move-up');

    Route::patch('/admin/galeri/{galleryItem}/move-down', [GalleryAdminController::class, 'moveDown'])
        ->name('admin.galeri.move-down');
    /* /REAL_GALLERY_CRUD_ROUTES_FINAL */
    /* /ADMIN_DESKTOP_DUMMY_ROUTES_FINAL */

    Route::get('/admin/stats', [SiteStatisticController::class, 'edit'])
        ->name('admin.stats.edit');

    Route::put('/admin/stats', [SiteStatisticController::class, 'update'])
        ->name('admin.stats.update');

    Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
