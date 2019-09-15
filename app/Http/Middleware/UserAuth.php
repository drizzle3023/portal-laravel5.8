<?php


namespace App\Http\Middleware;


use Closure;

class UserAuth
{
    public function handle($request, Closure $next, $guard = null) {

        $user = session()->get('user');

        if (isset($user)) {
            return $next($request);
        }

        return redirect('/login');
    }
}
