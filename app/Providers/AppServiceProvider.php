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
            $key = app('encrypter')->getKey();
            $sourceHash = hash_hmac('sha256', (string) $request->ip(), $key);
            $sessionHash = hash_hmac('sha256', $sessionId, $key);

            return [
                Limit::perMinute(20)
                    ->by('google-oauth:source:'.$sourceHash),
                Limit::perMinute(10)
                    ->by('google-oauth:session:'.$sessionHash),
            ];
        });

        Route::middleware('web')->group(base_path('routes/testimonials.php'));

        View::composer('admin.gallery.index', AdminGalleryIndexComposer::class);
        View::composer('admin.ppdb.edit', AdminPpdbEditComposer::class);
    }
}
