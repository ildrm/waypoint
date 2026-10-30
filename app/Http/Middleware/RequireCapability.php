<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class RequireCapability
{
    public function handle(Request $request, Closure $next, string $capability)
    {
        abort_unless($request->user()?->capable($capability), 403);

        return $next($request);
    }
}
