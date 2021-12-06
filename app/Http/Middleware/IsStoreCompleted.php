<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ShopBilling;
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
        $user       = Auth::user();
        $store      = Shop::find($user->shop_id);  
        $billing    = ShopBilling::where('shop_id', SHOP_ID)->first(); 
        if ($store->country_id == NULL) {
            return redirect('store/profile')->with('error',"Please update Store country details.");
        } else if ($store->timezone == NULL) {
            return redirect('store/profile')->with('error',"Please update Store timezone details.");
        } else if ($billing->company_name == NULL) {
            return redirect('store/billings')->with('error',"Please update Company name.");
        } else if ($billing->country_id == NULL) {
            return redirect('store/billings')->with('error',"Please update country details.");
        } else if ($billing->currency == NULL) {
            return redirect('store/billings')->with('error',"Please update currency details.");
        }
        return $next($request);
    }
}
