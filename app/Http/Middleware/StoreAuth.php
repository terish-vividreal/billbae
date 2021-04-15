<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;

class StoreAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if($user->hasAnyRole(['Shop Admin', 'Manager', 'Staff'])){
            define('USER_ROLE', 'user');
            define('ROUTE_PREFIX', '');
            return $next($request);            
        }
   
        return redirect('home')->with('error',"You don't have access.");
    }
}
