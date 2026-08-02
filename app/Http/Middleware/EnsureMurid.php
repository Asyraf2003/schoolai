<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureMurid
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isMurid(), 403);

        return $next($request);
    }
}
