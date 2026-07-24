<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

/* ADMIN_DESKTOP_DUMMY_ROUTES_FINAL */
Route::redirect('/admin', '/admin/dashboard')->name('admin.index');

Route::get('/admin/dashboard', AdminDashboardController::class)
    ->name('admin.dashboard');
