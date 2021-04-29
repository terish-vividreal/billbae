<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
        $page               = collect();
        $user               = Auth::user();
        $store              = Shop::with('users')->find($user->shop_id);
        $page->title        = $this->title;
        $page->link         = url($this->link);
        // echo count($store->users);
        // echo "<pre>"; print_r($store); exit;  
        return view($this->viewPath . '.profile', compact('page', 'user', 'store'));
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
       
            $shop   = Shop::find($id);
            $shop->name = $request->name;
            $shop->email = $request->email;
            $shop->contact = $request->contact;
            $shop->location = $request->location;
            $shop->about = $request->about;
            $shop->save();
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
