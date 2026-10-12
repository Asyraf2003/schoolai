<?php

use App\Http\Controllers\Admin\GalleryAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/galeri', [GalleryAdminController::class, 'index'])->name('admin.galeri');
Route::get('/admin/galeri/create', [GalleryAdminController::class, 'create'])->name('admin.galeri.create');
Route::post('/admin/galeri', [GalleryAdminController::class, 'store'])->name('admin.galeri.store');
Route::get('/admin/galeri/{galleryItem}/edit', [GalleryAdminController::class, 'edit'])->name('admin.galeri.edit');
Route::put('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'update'])->name('admin.galeri.update');
Route::delete('/admin/galeri/{galleryItem}', [GalleryAdminController::class, 'destroy'])->name('admin.galeri.destroy');
Route::patch('/admin/galeri/{galleryItem}/restore', [GalleryAdminController::class, 'restore'])->withTrashed()->name('admin.galeri.restore');
