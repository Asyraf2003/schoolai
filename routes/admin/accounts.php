<?php

use App\Http\Controllers\Admin\AccountAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/akun', [AccountAdminController::class, 'index'])
    ->name('admin.accounts.index');
Route::get('/admin/akun/data', [AccountAdminController::class, 'data'])
    ->name('admin.accounts.data');
Route::post('/admin/akun', [AccountAdminController::class, 'store'])
    ->name('admin.accounts.store');
Route::put('/admin/akun/{account}', [AccountAdminController::class, 'update'])
    ->name('admin.accounts.update');
Route::patch('/admin/akun/{account}/status', [AccountAdminController::class, 'status'])
    ->name('admin.accounts.status');
Route::put('/admin/akun/{account}/password', [AccountAdminController::class, 'resetPassword'])
    ->name('admin.accounts.password.reset');
