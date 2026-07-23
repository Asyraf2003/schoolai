<?php

use App\Http\Controllers\Admin\TestimonialMediaAdminController;
use App\Http\Controllers\TestimonialMediaController;
use Illuminate\Support\Facades\Route;

Route::get('/testimoni/media', TestimonialMediaController::class)
    ->name('testimoni.media');

Route::middleware([
    'auth',
    'active.account',
    'admin',
    'admin.locale',
])->prefix('admin/testimoni')->name('admin.testimoni.')->group(function (): void {
    Route::get('/', [TestimonialMediaAdminController::class, 'index'])->name('index');
    Route::get('/create', [TestimonialMediaAdminController::class, 'create'])->name('create');
    Route::post('/', [TestimonialMediaAdminController::class, 'store'])->name('store');
    Route::get('/{testimonialMedia}/edit', [TestimonialMediaAdminController::class, 'edit'])->name('edit');
    Route::put('/{testimonialMedia}', [TestimonialMediaAdminController::class, 'update'])->name('update');
    Route::delete('/{testimonialMedia}', [TestimonialMediaAdminController::class, 'destroy'])->name('destroy');
    Route::patch('/{testimonialMedia}/toggle', [TestimonialMediaAdminController::class, 'toggle'])->name('toggle');
    Route::patch('/{testimonialMedia}/move-up', [TestimonialMediaAdminController::class, 'moveUp'])->name('move-up');
    Route::patch('/{testimonialMedia}/move-down', [TestimonialMediaAdminController::class, 'moveDown'])->name('move-down');
    Route::patch('/archive/{testimonialMedia}/restore', [TestimonialMediaAdminController::class, 'restore'])->name('restore');
});
