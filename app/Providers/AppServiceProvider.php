<?php

namespace App\Providers;

use App\View\Composers\AdminGalleryIndexComposer;
use App\View\Composers\AdminPpdbEditComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.gallery.index', AdminGalleryIndexComposer::class);
        View::composer('admin.ppdb.edit', AdminPpdbEditComposer::class);
    }
}
