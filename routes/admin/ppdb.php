<?php

use App\Http\Controllers\Admin\PpdbSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/ppdb', [PpdbSettingController::class, 'edit'])->name('admin.ppdb');
Route::put('/admin/ppdb', [PpdbSettingController::class, 'update'])->name('admin.ppdb.update');
