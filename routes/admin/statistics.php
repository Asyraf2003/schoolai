<?php

use App\Http\Controllers\Admin\SiteStatisticController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/stats', [SiteStatisticController::class, 'edit'])
    ->name('admin.stats.edit');

Route::post('/admin/stats', [SiteStatisticController::class, 'store'])
    ->name('admin.stats.store');

Route::put('/admin/stats/{siteStatistic}', [SiteStatisticController::class, 'update'])
    ->name('admin.stats.update');

Route::delete('/admin/stats/{siteStatistic}', [SiteStatisticController::class, 'destroy'])
    ->name('admin.stats.destroy');

Route::patch('/admin/stats/{siteStatistic}/restore', [SiteStatisticController::class, 'restore'])
    ->name('admin.stats.restore');
