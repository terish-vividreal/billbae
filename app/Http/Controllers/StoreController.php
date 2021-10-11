<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Helpers\FunctionHelper;
use App\Models\BillingFormat;
use App\Models\PaymentType;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ShopBilling;
use App\Models\ThemeSetting;
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
        $page                       = collect();
        $variants                   = collect();
        $user                       = Auth::user();
        $store                      = Shop::with('users')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')->find($user->shop_id);        
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $variants->countries        = DB::table('shop_countries')->where('status',1)->pluck('name', 'id');  

        if ($store->country_id) {
            $variants->states           = DB::table('shop_states')->where('country_id',$store->country_id)->pluck('name', 'id'); 
            $country_code               = DB::table('shop_countries')->where('id',$store->country_id)->value('sortname');
            $variants->timezone         = DB::table('timezone')->where('country_code',$country_code)->pluck('zone_name', 'zone_name');
        }        
        if ($store->state_id) {
            $variants->districts        = DB::table('shop_districts')->where('state_id',$store->state_id)->pluck('name', 'id'); 
        }   
        return view($this->viewPath . '.profile', compact('page', 'user', 'store', 'variants'));
    }

    public function billings(Request $request)
    {
        $page                       = collect();
        $variants                   = collect();
        $user                       = Auth::user();
        $store                      = Shop::with('users')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')->find($user->shop_id);  
        $billing                    = ShopBilling::where('shop_id', SHOP_ID)->first();      
        $page->title                = 'Billing';
        $page->link                 = url($this->link);
        $variants->countries        = DB::table('shop_countries')->where('status',1)->pluck('name', 'id'); 
        $variants->tax_percentage   = DB::table('gst_tax_percentages')->pluck('percentage', 'id');  

        if ($billing->country_id) {
            $variants->states           = DB::table('shop_states')->where('country_id',$billing->country_id)->pluck('name', 'id'); 
            $country_code               = DB::table('shop_countries')->where('id',$billing->country_id)->value('sortname');
            $variants->timezone         = DB::table('timezone')->where('country_code',$country_code)->pluck('zone_name', 'zone_name');
            $variants->currencies       = DB::table('currencies')->where('country_id', $billing->country_id)->pluck('symbol', 'id');

        }        
        if ($billing->state_id) {
            $variants->districts        = DB::table('shop_districts')->where('state_id',$billing->state_id)->pluck('name', 'id'); 
        }

        return view($this->viewPath . '.billing', compact('page', 'user', 'store', 'variants', 'billing'));
    }

    public function billingSeries(Request $request)
    {
        $page                       = collect();
        $variants                   = collect();
        $user                       = Auth::user();
        $store                      = Shop::with('users')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')->find($user->shop_id);  
        $billing                    = ShopBilling::where('shop_id', SHOP_ID)->first();      
        $page->title                = 'Billing Series';
        $page->link                 = url($this->link);

        $variants->billing_formats          = BillingFormat::where('shop_id', SHOP_ID)->where('payment_type', 0)->first();
        $variants->billing_formats_all      = collect(BillingFormat::where('shop_id', SHOP_ID)->where('payment_type', '!=', 0)->get());
        $variants->payment_types            = PaymentType::select('name', 'id')->where('shop_id', SHOP_ID)->get();     
        return view($this->viewPath . '.billing-series', compact('page', 'user', 'store', 'variants', 'billing'));
    }

    public function userProfile(Request $request)
    {
        $page                   = collect();
        $variants               = collect();
        $user                   = Auth::user();
        $store                  = Shop::with('users')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')->find($user->shop_id);        
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->form_url         = url($this->link);
        $page->form_method      = 'POSt';

        return view($this->viewPath . '.user-profile', compact('page', 'user', 'store', 'variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function postUserProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'name' => 'required', ]);

        if ($validator->passes()) {
            $user               = Auth::user();
            $user->name         = $request->name;
            $user->email        = $request->email;
            $user->mobile       = $request->mobile;
            $user->save();
            return ['flagError' => false, 'message' => "User details updated successfully"];
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [ 'name' => 'required', ]);

        if ($validator->passes()) {
            $shop               = Shop::find($id);
            $shop->name         = $request->name;
            $shop->email        = $request->email;
            $shop->contact      = $request->contact;
            $shop->location     = $request->location;
            $shop->about        = $request->about;
            $shop->address      = $request->address;
            $shop->pincode      = $request->pincode;
            $shop->map_location = $request->map_location;
            $shop->pin          = $request->pin;
            $shop->timezone     = $request->timezone;
            $shop->time_format  = $request->time_format;
            $shop->country_id   = $request->country_id;
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
    public function updateGst(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'gst_percentage' => 'required', ]);

        if ($validator->passes()) {
            $billing                    = ShopBilling::find($request->gst_billing_id);
            $billing->gst_percentage    = $request->gst_percentage;
            $billing->save();
            return ['flagError' => false, 'message' => "GST Updated successfully"];
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
        $validator = Validator::make($request->all(), [ 'billing_id' => 'required', ]);

        if ($validator->passes()) {
            $billing                    = ShopBilling::find($id);
            $billing->shop_id           = SHOP_ID;
            $billing->company_name      = $request->company_name;
            $billing->address           = $request->address;
            $billing->pincode           = $request->pincode;
            $billing->pin               = $request->pin;
            $billing->gst               = $request->gst;
            $billing->country_id        = $request->billing_country_id;
            $billing->state_id          = $request->billing_state_id;
            $billing->district_id       = $request->billing_district_id;
            $billing->currency          = $request->currency;
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
        $validator = Validator::make($request->all(), [ 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', ]);

        if ($validator->passes()) {
            $shop               = Shop::find($request->store_id);
            $old_store_logo     = $shop->image;

            if ($old_store_logo != '') {
                \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/logo/' . $old_store_logo);
            }

            // Create storage folder if not exist
            $store_path         = 'public/' . $this->uploadPath. '/logo/';
            Storage::makeDirectory($store_path);

            $file               = $request->image;

            $extension          = $file->getClientOriginalExtension();
            $imageName          = Str::random(20).'.'.$extension;
            Storage::putFileAs($store_path, $file, $imageName);

            $shop->image        = $imageName;
            $shop->save();
            return ['flagError' => false, 'logo' => $shop->show_image,  'message' => "Logo updated successfully"];

        }

        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$validator->errors()->all()];


        // $image_64 = $request->image; //your base64 encoded data
        // $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        // $replace = substr($image_64, 0, strpos($image_64, ',')+1); 

        // $image = str_replace($replace, '', $image_64); 
        // $image = str_replace(' ', '+', $image); 
        // $imageName = Str::random(20).'.'.$extension;
        // Storage::put($store_path.'/'.$imageName, base64_decode($image));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserImage(Request $request)
    {
        $user               = Auth::user();
        $old_image          = $user->profile;

        if ($old_image != '') {
            \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/users/' . $old_image);
        }
        
        // Create storage folder
        $store_path         = 'public/' . $this->uploadPath. '/users/';
        Storage::makeDirectory($store_path);

        $image_64           = $request->image; //your base64 encoded data
        $extension          = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $replace            = substr($image_64, 0, strpos($image_64, ',')+1); 

        $image              = str_replace($replace, '', $image_64); 
        $image              = str_replace(' ', '+', $image); 
        $imageName          = Str::random(20).'.'.$extension;

        Storage::put($store_path.'/'.$imageName, base64_decode($image));
        $user->profile      = $imageName;
        $user->save();

        return ['flagError' => false, 'logo' => asset('storage/store/users/' . $user->profile),  'message' => "Profile image updated successfully"];
    }

    public function isUnique(Request $request)
    { 
        if ($request->store_id == 0) {
            $count      = Shop::where('email', $request->email)->count();
            echo ($count > 0 ? 'false' : 'true');
        } else {
            $count      = Shop::where('email', $request->email)->where('id', '!=' , $request->store_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
    }

    public function updateBillFormat(Request $request)
    {
        $billing_format             = BillingFormat::find($request->bill_format_id);
        $billing_format->shop_id    = SHOP_ID;
        $billing_format->prefix     = Str::upper($request->bill_prefix);
        $billing_format->suffix     = $request->bill_suffix;
        $billing_format->save();

        if (!$request->has('applied_to_all') ) {
            if (count($request->payment_types) > 0 ) {
                foreach($request->payment_types as $key => $type) {
                    $format = BillingFormat::updateOrCreate(
                        ['shop_id' => SHOP_ID, 'payment_type' => $type],
                        ['prefix' => Str::upper($request->bill_prefix_type[$type]), 'suffix' => ($request->bill_suffix_type[$type] != '') ? $request->bill_suffix_type[$type] : $request->bill_suffix, 'applied_to_all' => 1]
                    );
                }
                $billing_format->applied_to_all = 1;
                $billing_format->save();
            }
        }
        return ['flagError' => false, 'bill_format' => $billing_format->bill_format,  'message' => "Updated successfully"];    
    }

    public function themeSettings(Request $request)
    {
        $theme_settings                     = ThemeSetting::find($request->theme_settings_id);
        $theme_settings->activeMenuColor    = $request->activeMenuColor;
        $theme_settings->navbarBgColor      = $request->navbarBgColor;
        $theme_settings->isMenuDark         = ($request->has('isMenuDark'))?1:0;
        $theme_settings->menuCollapsed      = ($request->has('menuCollapsed'))?1:0;
        $theme_settings->footerFixed        = ($request->has('footerFixed'))?1:0;
        $theme_settings->menuStyle          = $request->menuSelection;
        $theme_settings->save();

        return ['flagError' => false, 'message' => "Theme settings updated successfully"];    
    }
}
