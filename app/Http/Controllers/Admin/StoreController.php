<?php
    
namespace App\Http\Controllers\Admin;
    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\BusinessType;
use DB;
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
    protected $link     = 'users';

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
        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $user_id = Auth::user()->id;
        $detail =  User::with('shop')->select(['name', 'mobile', 'email', 'id']);
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
                    ->addColumn('action', function($detail){
                        $action = ' <a  href="' . url('admin/stores/' . $detail->id . '/edit') . '" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                         $action .= '<form id="delete' . $detail->id . '" action="' . route('users.destroy', $detail->id) . '" method="POST">' . method_field('DELETE') . csrf_field() .
                        '<button type="button" onclick="deleteConfirm(' . $detail->id . ')" class="btn btn-danger btn-sm btn-icon mr-2"><i class="fa fa-trash" aria-hidden="true"></i></button></form>';
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
        $page               = collect();
        $variants           = collect();
        $user               = Auth::user();       
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link);
        $page->form_method  = 'POST';
        $variants->business_types     = BusinessType::pluck('name','id')->all();
        $variants->roles              = Role::where('name', '!=' , 'Super Admin')->pluck('name','name')->all();
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        if ($validator->passes()) {

            $input      = $request->all();
            $user_id    = Auth::user()->id;
            $input['password'] = Hash::make($input['password']);
            $input['parent_id'] = $user_id;

            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            if(Auth::user()->parent_id == null){
                $shop = new Shop();
                $shop->name             = $input['shop_name'];
                $shop->business_type_id = $input['business_type'];
                $shop->user_id          = $user->id;
                $shop->save();
    
                $user->shop_id = $shop->id;
            
            }else{
                $user->shop_id = Auth::user()->shop_id;
            }
            
            $user->save();

            return ['flagError' => false, 'message' => "Account Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];
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
        $user               = User::find($id);
        $page               = collect();
        $variants           = collect();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link . '/' . $user->id);
        $page->form_method  = 'PUT';        
        $variants->roles    = Role::pluck('name','name')->all();
        $variants->business_types     = BusinessType::pluck('name','id')->all();
        $userRole           = $user->roles->pluck('name','name')->all();
    
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

            $input = $request->all();
            if(!empty($input['password'])){ 
                $input['password'] = Hash::make($input['password']);
            }else{
                $input = Arr::except($input,array('password'));    
            }
        
            $user = User::find($id);
            $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
        
            $user->assignRole($request->input('roles'));

            $shop = Shop::find($user->shop_id);
            $shop->name             = $input['shop_name'];
            $shop->business_type_id = $input['business_type'];
            $shop->save();


            return ['flagError' => false, 'message' => "Account Updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check!",  'error'=>$validator->errors()->all()];

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


        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}