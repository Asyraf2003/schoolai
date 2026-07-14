<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ForceAdminLocale;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureRegularUser;
use App\Http\Middleware\AddSecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            AddSecurityHeaders::class,
        ]);
        $middleware->alias([
            'admin.locale' => ForceAdminLocale::class,
            'admin' => EnsureAdmin::class,
            'regular.user' => EnsureRegularUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if ($request->is('admin/galeri*')) {
                return back()->withErrors([
                    'media_file' => 'Ukuran file terlalu besar. Maksimal foto galeri 10MB. Pastikan upload_max_filesize dan post_max_size PHP lebih besar dari 10MB.',
                ]);
            }

            return null;
        });
    })->create();

$publicPathConfig = dirname(__DIR__).'/.public-path';

if (is_file($publicPathConfig)) {
    $configuredPublicPath = trim((string) file_get_contents($publicPathConfig));

    if ($configuredPublicPath !== '') {
        if (! str_starts_with($configuredPublicPath, DIRECTORY_SEPARATOR)) {
            $configuredPublicPath = dirname(__DIR__).DIRECTORY_SEPARATOR.$configuredPublicPath;
        }

        $app->usePublicPath(realpath($configuredPublicPath) ?: $configuredPublicPath);
    }
}

return $app;
