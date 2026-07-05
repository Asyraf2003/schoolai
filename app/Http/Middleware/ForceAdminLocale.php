<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ForceAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('id');

        return $next($request);
    }
}
