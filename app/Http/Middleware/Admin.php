<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.  Accept: application/json
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */

        public function handle(Request $request, Closure $next): Response
        {
            if (auth('admin')->check() && auth('admin')->user()->is_admin === true) {
                return $next($request);
            }
            return response()->json(['message' => 'ليس لديك صلاحية الوصول'], 403);
        }
    
}


