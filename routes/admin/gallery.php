<?php

use App\Http\Controllers\Admin\GalleryAdminController;
use App\Http\Controllers\Admin\GalleryPageSectionAdminController;
use Illuminate\Support\Facades\Route;

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

Route::patch('/admin/galeri/bagian/{galleryPageSection}/restore', [GalleryPageSectionAdminController::class, 'restore'])
    ->name('admin.galeri.sections.restore');

Route::put('/admin/galeri/bagian/{galleryPageSection}/media', [GalleryPageSectionAdminController::class, 'updateMediaPlacements'])
    ->name('admin.galeri.sections.media.update');
/* /GALLERY_PAGE_SECTION_ADMIN_ROUTES_FINAL */

Route::get('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'show'])
    ->name('admin.galeri.show');

Route::get('/admin/galeri/{galleryItem}/edit', [GalleryAdminController::class, 'edit'])
    ->name('admin.galeri.edit');

Route::put('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'update'])
    ->name('admin.galeri.update');

Route::delete('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'destroy'])
    ->name('admin.galeri.destroy');

Route::patch('/admin/galeri/{galleryItem}/restore', [GalleryAdminController::class, 'restore'])
    ->name('admin.galeri.restore');

Route::patch('/admin/galeri/{galleryItem}/toggle', [GalleryAdminController::class, 'toggle'])
    ->name('admin.galeri.toggle');

Route::patch('/admin/galeri/{galleryItem}/move-up', [GalleryAdminController::class, 'moveUp'])
    ->name('admin.galeri.move-up');

Route::patch('/admin/galeri/{galleryItem}/move-down', [GalleryAdminController::class, 'moveDown'])
    ->name('admin.galeri.move-down');
/* /REAL_GALLERY_CRUD_ROUTES_FINAL */
/* /ADMIN_DESKTOP_DUMMY_ROUTES_FINAL */
