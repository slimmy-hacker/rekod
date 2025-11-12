<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PortalMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next, ...$portals)
{
    if (Auth::check()) {
        if (empty($portals) || in_array(Auth::user()->role, $portals)) {
            return $next($request);
        }

        // optional: deny access to portal not allowed for this role
        abort(403, 'Unauthorized access to this portal.');
    }

    return redirect()->route('login');
}

}

