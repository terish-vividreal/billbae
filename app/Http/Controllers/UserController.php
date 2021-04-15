<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
use Hash;
use DataTables;
use Illuminate\Support\Arr;
use App\Models\Shop;
    
class UserController extends Controller
{
    protected $title    = 'Users';
    protected $viewPath = 'users';
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
        // echo "<pre>"; print_r(Auth::user()->roles->name); exit;

        // dd(Auth::user()->roles->pluck('name')->toArray() );


        // echo Auth::user()->roles->name; exit;

        // $encrypted = Crypt::encryptString('user@gmail.com');
        // echo  $encrypted . '<br>'; 

        // $decrypted = Crypt::decryptString($encrypted);
        // echo  $decrypted; 

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
        $detail =  User::select(['name', 'mobile', 'email', 'id']);
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
                    ->addColumn('action', function($detail){
                        $action = ' <a  href="' . url('users/' . $detail->id . '/edit') . '" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                        // $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"> <i class="icon-1x fas fa-trash-alt"></i></a>';
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
        $user               = Auth::user();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link);
        $page->form_method  = 'POST';

        $roles              = Role::where('name', '!=' , 'Super Admin')->pluck('name','name')->all();
        return view($this->viewPath . '.create', compact('page', 'roles', 'user'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $user_id = Auth::user()->id;
        $input['password'] = Hash::make($input['password']);
        $input['parent_id'] = $user_id;

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        if(Auth::user()->parent_id == null){
            $shop = new Shop();
            $shop->name = $input['shop_name'];
        $shop->user_id = $user->id;
        $shop->save();

        $user->shop_id = $shop->id;
        
        }else{
            $user->shop_id = Auth::user()->shop_id;
        }
        
        $user->save();

        
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
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
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->form_url = url($this->link . '/' . $user->id);
        $page->form_method = 'PUT';
        
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view($this->viewPath . '.create',compact('user','roles','userRole', 'page'));
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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
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
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
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