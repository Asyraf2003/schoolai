<?php

namespace App\Providers;

use App\View\Composers\AdminGalleryIndexComposer;
use App\View\Composers\AdminPpdbEditComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
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
        RateLimiter::for('google-oauth', function (Request $request): array {
            $sessionId = $request->session()->getId();

            return [
                Limit::perMinute(20)
                    ->by('google-oauth:ip:'.$request->ip()),
                Limit::perMinute(10)
                    ->by('google-oauth:session:'.$sessionId),
            ];
        });

        Route::middleware('web')->group(base_path('routes/testimonials.php'));

        View::composer('admin.gallery.index', AdminGalleryIndexComposer::class);
        View::composer('admin.ppdb.edit', AdminPpdbEditComposer::class);
    }
}
