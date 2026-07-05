<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ForceAdminLocale;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [SetLocale::class]);
        $middleware->alias([
            'admin.locale' => ForceAdminLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if ($request->is('admin/galeri*')) {
                return back()->withErrors([
                    'media_file' => 'Ukuran file terlalu besar. Maksimal media galeri 100MB. Pastikan upload_max_filesize dan post_max_size PHP lebih besar dari 100MB.',
                ]);
            }

            return null;
        });
    })->create();
