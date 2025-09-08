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
        // Cek status aplikasi dari environment
        $appStatus = env('APP_STATUS', 'NOT_OPEN');

        // Jika status OPEN_REGISTER, maka perlu autentikasi
        if ($appStatus === 'OPEN_REGISTER') {
            // Cek apakah user sudah login (menggunakan Sanctum atau auth:api)
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