<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\ShopBilling;
use DB;
use Validator;
use Auth;
use Hash;
use DataTables;
use Illuminate\Support\Arr;
use App\Models\Shop;

class StoreController extends Controller
{
    protected $title    = 'Profile';
    protected $viewPath = '/store';
    protected $link     = 'store';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:store-profile-update', ['only' => ['index','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page                   = collect();
        $variants               = collect();
        $user                   = Auth::user();
        $store                  = Shop::with('users')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                    ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                    ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                    ->find($user->shop_id);        
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $variants->states           = DB::table('shop_states')->pluck('name', 'id');  
        
        if($store->state_id){
            $variants->districts    = DB::table('shop_districts')->where('state_id',$store->state_id)->pluck('name', 'id'); 
        }   
        
        $billing                = ShopBilling::where('shop_id', SHOP_ID)->first();

        if($billing->state_id){
            $variants->billing_districts    = DB::table('shop_districts')->where('state_id',$billing->state_id)->pluck('name', 'id'); 
        } 

        return view($this->viewPath . '.profile', compact('page', 'user', 'store', 'variants', 'billing'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->passes()) {
       
            $shop               = Shop::find($id);
            $shop->name         = $request->name;
            $shop->email        = $request->email;
            $shop->contact      = $request->contact;
            $shop->location     = $request->location;
            $shop->about        = $request->about;
            $shop->address      = $request->address;
            $shop->pincode      = $request->pincode;
            $shop->pin          = $request->pin;
            $shop->state_id     = $request->state_id;
            $shop->district_id  = $request->district_id;
            $shop->save();
            return ['flagError' => false, 'message' => "Account Updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check!",  'error'=>$validator->errors()->all()];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeBilling(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'billing_id' => 'required',
        ]);

        if ($validator->passes()) {
       
            $billing                    = ShopBilling::find($id);
            $billing->shop_id           = SHOP_ID;
            $billing->company_name      = $request->company_name;
            $billing->address           = $request->address;
            $billing->pincode           = $request->pincode;
            $billing->pin               = $request->pin;
            $billing->gst               = $request->gst;
            $billing->state_id             = $request->billing_state_id;
            $billing->district_id          = $request->billing_district_id;
            $billing->save();
            return ['flagError' => false, 'message' => "Account Updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check!",  'error'=>$validator->errors()->all()];

    }

    public function isUnique(Request $request){ 
        if($request->store_id == 0){
            $count = Shop::where('email', $request->email)->count();
            echo ($count > 0 ? 'false' : 'true');
        }else{
            $count = Shop::where('email', $request->email)->where('id', '!=' , $request->store_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
        
    }

}
