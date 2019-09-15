<?php


namespace App\Http\Middleware;


use Closure;

class AdminAuth
{
    public function handle($request, Closure $next, $guard = null) {

        $admin = session()->get('user');
        $user_type = session()->get('user-type');

        if (isset($admin) && $user_type == 1) {
            return $next($request);
        }

        return redirect('/login');
    }
}
