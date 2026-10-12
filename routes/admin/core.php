<?php

use App\Http\Controllers\Admin\AccountAdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/akun')->name('admin.index');
Route::get('/admin/dashboard', [AccountAdminController::class, 'index'])->name('admin.dashboard');
