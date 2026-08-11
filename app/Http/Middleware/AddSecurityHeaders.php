<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

final class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);
        $nonce = (string) Vite::cspNonce();

        $response->headers->set(
            'Content-Security-Policy',
            $this->contentSecurityPolicy(
                $nonce,
                $request->routeIs('home'),
                $this->allowsGoogleAnalytics($request)
            )
        );
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        );
        $response->headers->set('X-Frame-Options', 'DENY');

        if (
            config('app.env') === 'production'
            && $request->isSecure()
        ) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000'
            );
        }

        return $response;
    }

    private function allowsGoogleAnalytics(Request $request): bool
    {
        return config('app.env') === 'production'
            && $request->routeIs(
                'home',
                'ppdb',
                'artikel',
                'artikel.native',
                'galeri'
            );
    }

    private function contentSecurityPolicy(
        string $nonce,
        bool $allowDepthGalleryRuntime,
        bool $allowGoogleAnalytics
    ): string {
        $threeSource = $allowDepthGalleryRuntime
            ? ' https://cdn.jsdelivr.net'
            : '';
        $analyticsScriptSource = $allowGoogleAnalytics
            ? ' https://www.googletagmanager.com'
            : '';
        $analyticsImageSources = $allowGoogleAnalytics
            ? ' https://*.google-analytics.com https://www.googletagmanager.com'
            : '';
        $analyticsConnectSources = $allowGoogleAnalytics
            ? ' https://*.google-analytics.com https://*.analytics.google.com https://www.googletagmanager.com'
            : '';

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src 'self' 'nonce-{$nonce}'{$threeSource}{$analyticsScriptSource}",
            "script-src-attr 'none'",
            "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com",
            "style-src-attr 'unsafe-inline'",
            "img-src 'self' data: blob: https://i.ytimg.com https://images.unsplash.com https://resources.finalsite.net{$analyticsImageSources}",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self'{$threeSource}{$analyticsConnectSources}",
            "media-src 'self' blob:",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.tiktok.com https://www.instagram.com https://www.facebook.com https://player.vimeo.com https://open.spotify.com https://codepen.io",
            "manifest-src 'self'",
        ]);
    }
}
