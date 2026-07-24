<?php

use App\Http\Controllers\Admin\PpdbSettingController;
use App\Http\Controllers\Admin\PpdbShowcaseAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/ppdb', [PpdbSettingController::class, 'edit'])
    ->name('admin.ppdb');

Route::put('/admin/ppdb', [PpdbSettingController::class, 'update'])
    ->name('admin.ppdb.update');

Route::patch('/admin/ppdb/toggle', [PpdbSettingController::class, 'toggle'])
    ->name('admin.ppdb.toggle');

/* PPDB_SHOWCASE_ADMIN_ROUTES */
Route::post('/admin/ppdb/showcase', [PpdbShowcaseAdminController::class, 'store'])
    ->name('admin.ppdb.showcase.store');

Route::get('/admin/ppdb/showcase/{ppdbShowcaseItem}/edit', [PpdbShowcaseAdminController::class, 'edit'])
    ->name('admin.ppdb.showcase.edit');

Route::put('/admin/ppdb/showcase/{ppdbShowcaseItem}', [PpdbShowcaseAdminController::class, 'update'])
    ->name('admin.ppdb.showcase.update');

Route::delete('/admin/ppdb/showcase/{ppdbShowcaseItem}', [PpdbShowcaseAdminController::class, 'destroy'])
    ->name('admin.ppdb.showcase.destroy');

Route::patch('/admin/ppdb/showcase/{ppdbShowcaseItem}/restore', [PpdbShowcaseAdminController::class, 'restore'])
    ->name('admin.ppdb.showcase.restore');

Route::patch('/admin/ppdb/showcase/{ppdbShowcaseItem}/move-up', [PpdbShowcaseAdminController::class, 'moveUp'])
    ->name('admin.ppdb.showcase.move-up');

Route::patch('/admin/ppdb/showcase/{ppdbShowcaseItem}/move-down', [PpdbShowcaseAdminController::class, 'moveDown'])
    ->name('admin.ppdb.showcase.move-down');
/* /PPDB_SHOWCASE_ADMIN_ROUTES */
