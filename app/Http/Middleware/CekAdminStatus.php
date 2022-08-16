<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CekAdminStatus
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
        $is_admin = Auth::user()->is_admin;
        
        if($is_admin == User::IS_NOT_ADMIN){
            return response()->view('errors.403');
        }

        return $next($request);
    }
}
