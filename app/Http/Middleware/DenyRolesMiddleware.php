<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AlertHelper;
use Illuminate\Support\Facades\Log;

class DenyRolesMiddleware
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$deniedRoles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$deniedRoles)
    {
        if (!Auth::check()) {
            return redirect()
                ->route('login.index')
                ->with(AlertHelper::error(
                    'You must be logged in to access this page.',
                    'Authentication Required'
                ));
        }

        $userRole = Auth::user()->role;
log::info('Role user saat ini: ' . $userRole);

        

        if (in_array($userRole, $deniedRoles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            

            return redirect()
                ->route('login.index')
                ->with(AlertHelper::error(
                    'Access denied for role: ' . $userRole,
                    'Access Forbidden'
                ));
        }

        return $next($request);
    }

}