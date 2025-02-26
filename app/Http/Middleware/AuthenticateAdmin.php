<?php

namespace App\Http\Middleware;

use Closure, Auth;

class AuthenticateAdmin
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
        if (Auth::guard('admin')->check()) {
            if(_get_admin_board() == null):
               _set_admin_board();
            endif;
			return $next($request);
		}

		return redirect('admin/')->with([
				'warning' => 'You must have to be logged in.',
            ]);
    }
}
