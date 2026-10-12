<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('google-oauth', function (Request $request): array {
            $key = app('encrypter')->getKey();
            $sourceHash = hash_hmac('sha256', (string) $request->ip(), $key);
            $sessionHash = hash_hmac('sha256', $request->session()->getId(), $key);

            return [Limit::perMinute(20)->by('google-oauth:source:'.$sourceHash),
                Limit::perMinute(10)->by('google-oauth:session:'.$sessionHash)];
        });
    }
}
