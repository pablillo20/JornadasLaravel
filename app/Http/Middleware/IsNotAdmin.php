<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsNotAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::user() && Auth::user()->rol === 'admin') {
            // Redirigir si no es admin
            return redirect('/ponentes'); 
        }

        return $next($request);
    }
}
