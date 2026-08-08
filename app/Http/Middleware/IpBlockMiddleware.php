<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpBlockMiddleware
{
    /**
     * Handle an incoming request for IP Blocking.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (BlockedIp::isBlocked($request->ip())) {
            return response()->view('errors.blocked', [], 403);
        }

        return $next($request);
    }
}
