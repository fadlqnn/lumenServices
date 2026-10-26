<?php

namespace App\Http\Middleware;

use Closure;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->input('username') == 'dlqn' && $request->input('password') == 'xx'){
            return "wrong creds";
        }
        return $next($request);
    }
}
