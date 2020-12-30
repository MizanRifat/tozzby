<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;

class MyAuth
{
 
    protected $auth;


    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, ...$guards)
    {

        if ($request->cookie('auth_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('auth_token'));
            $this->authenticate($request, $guards);
        }

        // $this->authenticate($request, $guards);

        return $next($request);
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

    }

}
