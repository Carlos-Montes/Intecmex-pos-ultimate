<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth, Route;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(getPermissions(Auth::user()->permissions, Route::currentRouteName()) == true):
            return $next($request);
        endif;

            return redirect('/not_allowed');
    }
}
