<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

// use App;

final class SetLocale
{
    /**
     * Set locale dari session/cookie agar pilihan bahasa tetap aktif
     * setelah hard refresh dan pindah halaman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? $request->cookie('site_locale')
            ?? config('app.locale');

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
