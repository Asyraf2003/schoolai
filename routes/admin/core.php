<?php

use App\Http\Controllers\PortalPlaceholderController;
use Illuminate\Support\Facades\Route;

/* ADMIN_DESKTOP_DUMMY_ROUTES_FINAL */
Route::redirect('/admin', '/admin/dashboard')->name('admin.index');

Route::get('/admin/dashboard', PortalPlaceholderController::class)
    ->name('admin.dashboard');
