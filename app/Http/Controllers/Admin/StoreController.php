<?php
    
namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\ThemeSetting;
use App\Events\StoreRegistered;
use App\Models\User;
use App\Models\ShopBilling;
use App\Models\BillingFormat;
use Spatie\Permission\Models\Role;
use App\Models\BusinessType;
use App\Models\ShopCountry;
use Mail;
use DB;
use Event;
use Validator;
use Auth;
use Hash;
use DataTables;
use Illuminate\Support\Arr;
use App\Models\Shop;
    
class StoreController extends Controller
{
    protected $title    = 'Stores';
    protected $viewPath = '/admin/users';
    protected $link     = 'admin/stores';
    protected $route    = 'stores';
    protected $entity   = 'stores';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user-list|user-create', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page           = collect();
        $page->title    = $this->title;
        $page->link     = url($this->link);
        $page->route    = $this->route;
        $page->entity   = $this->entity;
        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $user_id    = Auth::user()->id;
        $detail     = User::with(['shop'])->select(['name', 'mobile', 'phone_code', 'email', 'is_active', 'id']);
        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'search_name') {
                    $names = strtolower($search['value']);
                    $detail->where('name', 'like', "%{$names}%");
                }
            }
        }
        $detail->where('parent_id', $user_id)->orderBy('id', 'desc');
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('role', function($detail){
                $roles = User::find($detail->id)->roles;
                $html = '';
                if($roles){
                    foreach($roles as $role){
                        $html.= $role->name;
                    }
                }                        
                return $html;
            })
            ->addColumn('store', function($detail){                        
                $html = $detail->shop->name;                      
                return $html;
            })
            ->addColumn('businesstype', function($detail){                        
                $html =$detail->shop->business_types->name;                      
                return $html;
            })
            ->editColumn('mobile', function($detail) {
                $phone_code     = (!empty($detail->phoneCode->phonecode) ? '+' .$detail->phoneCode->phonecode : '');
                $mobile         = (!empty($detail->mobile) ? $phone_code . ' ' . $detail->mobile:'');
                return $mobile;
            })
            ->addColumn('is_active', function($detail){
                $html = '';
                if ($detail->is_active != 2) {
                    $checked    = ($detail->is_active == 1) ? 'checked' : '';
                    // $html       .= '<a href="javascript:" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                    $html       .= '<div class="switch"><label><input type="checkbox" '.$checked.' id="' . $detail->id . '" data-url="' . url($this->link.'/manage-status/') . '" class="activate-user" data-id="'.$detail->id.'" > <span class="lever"></span> </label> </div>';
                    return $html;
                }

                // onclick="manageUserStatus(this.id)"
            })
            ->addColumn('action', function($detail){
                $action = ' <a  href="' . url('admin/stores/' . $detail->id . '/edit') . '" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                // $action = ' <a  href="' . url('admin/stores/' . $detail->id . '/edit') . '" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                //     $action .= '<form id="delete' . $detail->id . '" action="' . route('users.destroy', $detail->id) . '" method="POST">' . method_field('DELETE') . csrf_field() .
                // '<button type="button" onclick="deleteConfirm(' . $detail->id . ')" class="btn btn-danger btn-sm btn-icon mr-2"><i class="fa fa-trash" aria-hidden="true"></i></button></form>';
                return $action;
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                   
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page                       = collect();
        $variants                   = collect();      
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $page->form_url             = url($this->link);
        $page->form_method          = 'POST';
        $page->route                = $this->route;
        $page->entity               = $this->entity;
        $variants->business_types   = BusinessType::pluck('name','id')->all();
        $variants->roles            = Role::where('id', '=' , 2)->pluck('name','name')->all();     
        $variants->phonecode        = ShopCountry::select("id", DB::raw('CONCAT(" +", phonecode , " (", name, ")") AS phone_code'))->where('status',1)->pluck('phone_code', 'id');         
        return view($this->viewPath . '.create', compact('page', 'variants'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required'
        ]);
        if ($validator->passes()) {
            $input                      = $request->all();
            $user_id                    = Auth::user()->id;
            $input['parent_id']         = $user_id;
            $input['mobile']            = $input['mobile'];
            $user                       = User::create($input);

            $user->assignRole($request->input('roles'));

            if (Auth::user()->parent_id == null) {
                $shop                   = new Shop();
                $shop->name             = $input['shop_name'];
                $shop->business_type_id = $input['business_type'];
                $shop->user_id          = $user->id;
                $shop->save();  
                $user->shop_id = $shop->id;
            } else {
                $user->shop_id          = Auth::user()->shop_id;
            }

            $token                      = Str::random(64);
            $user->verify_token         = $token;
            $user->save();

            // Store billing details created
            $billing                    = new ShopBilling();
            $billing->shop_id           = $shop->id;
            $billing->save();

            // Store billing format created with default details
            $billing_format             = new BillingFormat();
            $billing_format->shop_id    = $shop->id;
            $billing_format->prefix     = Str::upper(Str::substr(str_replace(' ', '', $shop->name), 0, 3)); 
            $billing_format->suffix     = 1000;
            $billing_format->save();

            // Store theme details created with default styles
            $theme_settings             = new ThemeSetting();
            $theme_settings->shop_id    = $shop->id;
            $theme_settings->save();

            //Store registration event  
            StoreRegistered::dispatch($user->id);

            return ['flagError' => false, 'message' => "Account Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=> $validator->errors()->all()];
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // dd('2');
        // $user = User::find($id);
        // return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user                       = User::find($id);
        $page                       = collect();
        $variants                   = collect();
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $page->form_url             = url($this->link . '/' . $user->id);
        $page->form_method          = 'PUT';   
        $page->route                = $this->route;
        $page->entity               = $this->entity;     
        $variants->roles            = Role::where('id', '=' , 2)->pluck('name','name')->all();
        $variants->business_types   = BusinessType::pluck('name','id')->all();
        $userRole                   = $user->roles->pluck('name','name')->all();
        $variants->phonecode        = ShopCountry::select("id", DB::raw('CONCAT(" +", phonecode , " (", name, ")") AS phone_code'))->where('status',1)->pluck('phone_code', 'id');         
        return view($this->viewPath . '.create',compact('user','variants','userRole', 'page'));
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
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        if ($validator->passes()) {

            $input                  = $request->all();
            if (!empty($input['password'])) { 
                $input['password']  = Hash::make($input['password']);
            } else {
                $input              = Arr::except($input,array('password'));    
            }
            
            $user                   = User::find($id);
            $input['updated_by']    = Auth::user()->id;
            $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $user->assignRole($request->input('roles'));

            $shop                   = Shop::find($user->shop_id);
            $shop->name             = $input['shop_name'];
            $shop->business_type_id = $input['business_type'];
            $shop->save();

            return ['flagError' => false, 'message' => "Account Updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$validator->errors()->all()];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        User::find($id)->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }

    public function manageStatus(Request $request)
    {
        $user                   = User::findOrFail($request->user_id);
        if ($user) {
            $status             = ($user->is_active == 0)?1:0;
            $user->is_active    = $status;
            $user->save();
            return ['flagError' => false, 'message' => $this->title. " status updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }
}