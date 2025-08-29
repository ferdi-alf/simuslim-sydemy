<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileApiSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appStatus = env('APP_STATUS', 'NOT_OPEN');

        if ($appStatus === 'OPEN_REGISTER') {
            if (!auth('sanctum')->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login first.',
                    'data' => null
                ], 401);
            }
        }

        return $next($request);
    }
}