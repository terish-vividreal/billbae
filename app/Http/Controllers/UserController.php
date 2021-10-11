<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Rules\MatchOldPassword;
use DB;
use Auth;
use Validator;
use Hash;
use DataTables;
use Illuminate\Support\Arr;
use App\Models\Shop;
use Mail; 

    
class UserController extends Controller
{
    protected $title    = 'Users';
    protected $viewPath = 'users';
    protected $link     = 'users';
    protected $route    = 'users';
    protected $entity   = 'users';

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
        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $user_id    = Auth::user()->id;
        $detail     =  User::select(['name', 'mobile', 'email', 'is_active', 'id']);

        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'search_name') {
                    $names = strtolower($search['value']);
                    $detail->where('name', 'like', "%{$names}%");
                }
            }
        }
        $detail->where('parent_id', $user_id)->where('is_active', '!=',  2)->orderBy('id', 'desc');
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
            ->addColumn('activate', function($detail){
                $checked = ($detail->is_active == 1) ? 'checked' : '';
                $html = '<div class="switch"><label> <input type="checkbox" '.$checked.' id="' . $detail->id . '" class="activate-user" data-id="'.$detail->id.'" onclick="manageUserStatus(this.id)"> <span class="lever"></span> </label> </div>';
                return $html;
            })
            ->addColumn('action', function($detail){
                $action = ' <a  href="' . url('users/' . $detail->id . '/edit') . '" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
                // $action .= '<form id="delete' . $detail->id . '" action="' . route('users.destroy', $detail->id) . '" method="POST">' . method_field('DELETE') . csrf_field() .
                // '<button type="button" onclick="deleteConfirm(' . $detail->id . ')" class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></button></form>';
                return $action;
            })
            ->removeColumn('id', 'is_active')
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
        $you                = Auth::user();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link);
        $page->entity       = $this->entity;
        $page->form_method  = 'POST';
        $page->route        = $this->route;
        $roles              = Role::where('name', '!=' , 'Super Admin')->orderBy('id', 'asc')->pluck('name','name')->all();
        return view($this->viewPath . '.create', compact('page', 'roles', 'you'));
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
            'roles' => 'required'
        ]);

        if ($validator->passes()) {
            $input                  = $request->all();
            $user_id                = Auth::user()->id;
            // $input['password'] = Hash::make($input['password']);
            $input['parent_id']     = $user_id;   

            $user       = User::create($input);
            $user->assignRole($request->input('roles'));
            if (Auth::user()->parent_id == null) {
                $shop           = new Shop();
                $shop->name     = $input['shop_name'];
                $shop->user_id  = $user->id;
                $shop->save();
                $user->shop_id  = $shop->id; 
            } else {
                $user->shop_id  = Auth::user()->shop_id;
            }
            $token                          = Str::random(64);
            $user->password_create_token    = $token;
            $user->save();

            $profile                = new StaffProfile();
            $profile->user_id       = $user->id;
            $profile->save();

            // Password create link
            // Mail::send('email.passwordCreate', ['token' => $token], function($message) use($request){
            //     $message->to($request->email);
            //     $message->subject('Create New Password Email');
            // });

            return ['flagError' => false, 'message' => "Account Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=>$validator->errors()->all()];
    
   
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
        $you                = Auth::user();
        $page->title        = $this->title;
        $page->route        = $this->route;
        $page->entity       = $this->entity;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link . '/' . $user->id);
        $page->form_method  = 'PUT';
        $roles              = Role::where('name', '!=' , 'Super Admin')->pluck('name','name')->all();
        $userRole           = $user->roles->pluck('name','name')->all();
        return view($this->viewPath . '.create',compact('user','roles', 'you' ,'userRole', 'page'));
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
            'roles' => 'required'
        ]);

        if ($validator->passes()) {
    
            $input      = $request->all();
            if (!empty($input['password'])) { 
                $input['password']  = Hash::make($input['password']);
            } else {
                $input              = Arr::except($input,array('password'));    
            }
        
            $user               = User::find($id);
            $input['gender']    = $request->gender;     
            $user->update($input);

            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $user->assignRole($request->input('roles'));
            return ['flagError' => false, 'message' => "Account Added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // DB::table('model_has_roles')->where('model_id',$id)->delete();
        // User::find($id)->delete();
        // return redirect()->route('users.index')
        //                 ->with('success','User deleted successfully');

        $user               = User::findOrFail($id);
        $user->is_active    = 2;
        $user->save();
        return ['flagError' => false, 'message' => $this->title. " deactivated successfully"];
    }

    public function isUnique(Request $request)
    { 
        if ($request->user_id == 0) {
            $count = User::where('email', $request->email)->count();
            echo ($count > 0 ? 'false' : 'true');
        } else {
            $count = User::where('email', $request->email)->where('id', '!=' , $request->user_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
    }

    public function manageStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        if ($user) {
            $status             = ($user->is_active == 0)?1:0;
            $user->is_active    = $status;
            $user->save();
            return ['flagError' => false, 'message' => $this->title. " status updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_password_confirmation' => ['same:new_password'],
        ]);

        if ($validator->passes()) {
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
            return ['flagError' => false, 'message' => $this->title. " password updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check!",  'error'=>$validator->errors()->all()];
    }
}