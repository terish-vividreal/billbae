<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shop;
use Auth;

class IsStoreCompleted
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
        $user   = Auth::user();
        $store  = Shop::find($user->shop_id);  
        
        if ($store->country_id == NULL) {
            return redirect('store/profile')->with('error',"PLease update store country details.");
        } else if ($store->timezone == NULL) {
            return redirect('store/profile')->with('error',"PLease update store timezone details.");
        }
        return $next($request);
    }
}
