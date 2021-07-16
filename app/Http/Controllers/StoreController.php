<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Helpers\FunctionHelper;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ShopBilling;
use Image;
use DB;
use Validator;
use Auth;
use Hash;
use DataTables;
use Illuminate\Support\Arr;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    protected $title        = 'Profile';
    protected $viewPath     = '/store';
    protected $link         = 'store';
    protected $uploadPath   = 'store/';

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
        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$validator->errors()->all()];

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
        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$validator->errors()->all()];

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {

        

        // echo "<pre>"; print_r($request->all()); exit;
        // $validator = Validator::make($request->all(), [
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        // if ($validator->passes()) {

            $shop               = Shop::find($request->store_id);

            $old_store_logo = $shop->image;

            if ($old_store_logo != '') {
                \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/logo/' . $old_store_logo);
            }
            
            

            // Create storage folder
            $store_path = 'public/' . $this->uploadPath. '/logo/';
            Storage::makeDirectory($store_path);

            $image_64 = $request->image; //your base64 encoded data
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
            $replace = substr($image_64, 0, strpos($image_64, ',')+1); 

            $image = str_replace($replace, '', $image_64); 
            $image = str_replace(' ', '+', $image); 
            $imageName = Str::random(20).'.'.$extension;
            Storage::put($store_path.'/'.$imageName, base64_decode($image));
   

            $shop->image        = $imageName;
            $shop->save();
    
            return ['flagError' => false, 'logo' => $shop->show_image,  'message' => "Logo updated successfully"];



        // }

        // return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    
        
  
        
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
