<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Adding support for jwt from cookie
     * @param Request $request 
     * @param Closure $next 
     * @param string[][] $guards 
     * @return mixed 
     * @throws AuthenticationException 
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Try to get jwt from the cookie
        $jwt = $request->cookie('jwt');
        if ($jwt) {
            // Mannualy add Authorization header key with the token
            $request->headers->set('Authorization', 'Bearer ' . $jwt);
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }
}
