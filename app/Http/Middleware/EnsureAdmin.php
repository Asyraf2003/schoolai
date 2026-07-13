<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAdmin
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! $request->user()?->isAdmin()) {
            return redirect()->route('account.locked');
        }

        return $next($request);
    }
}
