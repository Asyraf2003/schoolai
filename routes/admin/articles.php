<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/artikel', [ArticleAdminController::class, 'index'])->name('admin.artikel');
Route::get('/admin/artikel/create', [ArticleAdminController::class, 'create'])->name('admin.artikel.create');
Route::post('/admin/artikel', [ArticleAdminController::class, 'store'])->name('admin.artikel.store');
Route::get('/admin/artikel/{article}/edit', [ArticleAdminController::class, 'edit'])->name('admin.artikel.edit');
Route::put('/admin/artikel/{article}', [ArticleAdminController::class, 'update'])->name('admin.artikel.update');
Route::delete('/admin/artikel/{article}', [ArticleAdminController::class, 'destroy'])->name('admin.artikel.destroy');
Route::patch('/admin/artikel/{article}/restore', [ArticleAdminController::class, 'restore'])->withTrashed()->name('admin.artikel.restore');
