<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\ArticleCanvasAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/artikel', [ArticleAdminController::class, 'index'])
    ->name('admin.artikel');

Route::get('/admin/artikel/create', [ArticleAdminController::class, 'create'])
    ->name('admin.artikel.create');

Route::post('/admin/artikel', [ArticleAdminController::class, 'store'])
    ->name('admin.artikel.store');

Route::post('/admin/artikel/canvas/start', [ArticleCanvasAdminController::class, 'start'])
    ->name('admin.artikel.canvas.start');

Route::get('/admin/artikel/canvas/unsplash', [ArticleCanvasAdminController::class, 'searchUnsplash'])
    ->name('admin.artikel.canvas.unsplash');

Route::patch('/admin/artikel/homepage/order', [ArticleAdminController::class, 'reorderHomepage'])
    ->name('admin.artikel.homepage.order');

Route::post('/admin/artikel/{article}/homepage', [ArticleAdminController::class, 'pinHomepage'])
    ->name('admin.artikel.homepage.pin');

Route::delete('/admin/artikel/{article}/homepage', [ArticleAdminController::class, 'unpinHomepage'])
    ->name('admin.artikel.homepage.unpin');

Route::get('/admin/artikel/{article}/canvas', [ArticleCanvasAdminController::class, 'edit'])
    ->name('admin.artikel.canvas.edit');

Route::patch('/admin/artikel/{article}/canvas', [ArticleCanvasAdminController::class, 'autosave'])
    ->name('admin.artikel.canvas.autosave');

Route::post('/admin/artikel/{article}/canvas/image', [ArticleCanvasAdminController::class, 'uploadImage'])
    ->name('admin.artikel.canvas.image');

Route::post('/admin/artikel/{article}/canvas/publish', [ArticleCanvasAdminController::class, 'publish'])
    ->name('admin.artikel.canvas.publish');

Route::get('/admin/artikel/{article}', [ArticleAdminController::class, 'show'])
    ->name('admin.artikel.show');

Route::get('/admin/artikel/{article}/edit', [ArticleAdminController::class, 'edit'])
    ->name('admin.artikel.edit');

Route::put('/admin/artikel/{article}', [ArticleAdminController::class, 'update'])
    ->name('admin.artikel.update');

Route::delete('/admin/artikel/{article}', [ArticleAdminController::class, 'destroy'])
    ->name('admin.artikel.destroy');

Route::patch('/admin/artikel/{article}/restore', [ArticleAdminController::class, 'restore'])
    ->name('admin.artikel.restore');
