<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpdbPageController;
use App\Http\Controllers\ArticlePageController;
use App\Http\Controllers\GalleryPageController;
use App\Http\Controllers\Admin\SiteStatisticController;
use App\Http\Controllers\Admin\PpdbSettingController;
use App\Http\Controllers\Admin\GalleryAdminController;
use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\GalleryPageSectionAdminController;
use App\Http\Controllers\Admin\GalleryPageMediaAdminController;
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

Route::get('/ppdb', PpdbPageController::class)->name('ppdb');
Route::get('/artikel', ArticlePageController::class)->name('artikel');
Route::redirect('/artikel/adab-sebelum-prestasi', '/artikel', 301)->name('artikel.detail');
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

    Route::get('/admin/ppdb', [PpdbSettingController::class, 'edit'])
        ->name('admin.ppdb');

    Route::put('/admin/ppdb', [PpdbSettingController::class, 'update'])
        ->name('admin.ppdb.update');

    Route::patch('/admin/ppdb/toggle', [PpdbSettingController::class, 'toggle'])
        ->name('admin.ppdb.toggle');

    /* PPDB_SHOWCASE_ADMIN_ROUTES */
    Route::post('/admin/ppdb/showcase', [PpdbSettingController::class, 'storeShowcaseItem'])
        ->name('admin.ppdb.showcase.store');

    Route::get('/admin/ppdb/showcase/{ppdbShowcaseItem}/edit', [PpdbSettingController::class, 'editShowcaseItem'])
        ->name('admin.ppdb.showcase.edit');

    Route::put('/admin/ppdb/showcase/{ppdbShowcaseItem}', [PpdbSettingController::class, 'updateShowcaseItem'])
        ->name('admin.ppdb.showcase.update');

    Route::delete('/admin/ppdb/showcase/{ppdbShowcaseItem}', [PpdbSettingController::class, 'destroyShowcaseItem'])
        ->name('admin.ppdb.showcase.destroy');

    Route::patch('/admin/ppdb/showcase/{ppdbShowcaseItem}/move-up', [PpdbSettingController::class, 'moveShowcaseItemUp'])
        ->name('admin.ppdb.showcase.move-up');

    Route::patch('/admin/ppdb/showcase/{ppdbShowcaseItem}/move-down', [PpdbSettingController::class, 'moveShowcaseItemDown'])
        ->name('admin.ppdb.showcase.move-down');
    /* /PPDB_SHOWCASE_ADMIN_ROUTES */

    Route::get('/admin/artikel', [ArticleAdminController::class, 'index'])
        ->name('admin.artikel');

    Route::get('/admin/artikel/create', [ArticleAdminController::class, 'create'])
        ->name('admin.artikel.create');

    Route::post('/admin/artikel', [ArticleAdminController::class, 'store'])
        ->name('admin.artikel.store');

    Route::get('/admin/artikel/{article}', [ArticleAdminController::class, 'show'])
        ->name('admin.artikel.show');

    Route::get('/admin/artikel/{article}/edit', [ArticleAdminController::class, 'edit'])
        ->name('admin.artikel.edit');

    Route::put('/admin/artikel/{article}', [ArticleAdminController::class, 'update'])
        ->name('admin.artikel.update');

    Route::delete('/admin/artikel/{article}', [ArticleAdminController::class, 'destroy'])
        ->name('admin.artikel.destroy');

    /* REAL_GALLERY_CRUD_ROUTES_FINAL */
    Route::get('/admin/galeri', [GalleryAdminController::class, 'index'])
        ->name('admin.galeri');

    Route::get('/admin/galeri/create', [GalleryAdminController::class, 'create'])
        ->name('admin.galeri.create');

    Route::post('/admin/galeri', [GalleryAdminController::class, 'store'])
        ->name('admin.galeri.store');


    /* GALLERY_PAGE_SECTION_ADMIN_ROUTES_FINAL */
    Route::get('/admin/galeri/bagian/create', [GalleryPageSectionAdminController::class, 'create'])
        ->name('admin.galeri.sections.create');

    Route::post('/admin/galeri/bagian', [GalleryPageSectionAdminController::class, 'store'])
        ->name('admin.galeri.sections.store');

    Route::get('/admin/galeri/bagian/{galleryPageSection}', [GalleryPageSectionAdminController::class, 'show'])
        ->name('admin.galeri.sections.show');

    Route::get('/admin/galeri/bagian/{galleryPageSection}/edit', [GalleryPageSectionAdminController::class, 'edit'])
        ->name('admin.galeri.sections.edit');

    Route::put('/admin/galeri/bagian/{galleryPageSection}', [GalleryPageSectionAdminController::class, 'update'])
        ->name('admin.galeri.sections.update');

    Route::patch('/admin/galeri/bagian/{galleryPageSection}/toggle', [GalleryPageSectionAdminController::class, 'toggle'])
        ->name('admin.galeri.sections.toggle');

    Route::delete('/admin/galeri/bagian/{galleryPageSection}', [GalleryPageSectionAdminController::class, 'destroy'])
        ->name('admin.galeri.sections.destroy');

    Route::get('/admin/galeri/bagian/{galleryPageSection}/media/create', [GalleryPageMediaAdminController::class, 'create'])
        ->name('admin.galeri.section-media.create');

    Route::post('/admin/galeri/bagian/{galleryPageSection}/media', [GalleryPageMediaAdminController::class, 'store'])
        ->name('admin.galeri.section-media.store');

    Route::get('/admin/galeri/media/{galleryPageMediaItem}', [GalleryPageMediaAdminController::class, 'show'])
        ->name('admin.galeri.section-media.show');

    Route::get('/admin/galeri/media/{galleryPageMediaItem}/edit', [GalleryPageMediaAdminController::class, 'edit'])
        ->name('admin.galeri.section-media.edit');

    Route::put('/admin/galeri/media/{galleryPageMediaItem}', [GalleryPageMediaAdminController::class, 'update'])
        ->name('admin.galeri.section-media.update');

    Route::patch('/admin/galeri/media/{galleryPageMediaItem}/toggle', [GalleryPageMediaAdminController::class, 'toggle'])
        ->name('admin.galeri.section-media.toggle');

    Route::delete('/admin/galeri/media/{galleryPageMediaItem}', [GalleryPageMediaAdminController::class, 'destroy'])
        ->name('admin.galeri.section-media.destroy');
    /* /GALLERY_PAGE_SECTION_ADMIN_ROUTES_FINAL */

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
