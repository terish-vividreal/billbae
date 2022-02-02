<?php
    
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
    
class RoleController extends Controller
{
    protected $title    = 'Roles';
    protected $viewPath = 'roles';
    protected $link     = 'roles';
    protected $route    = 'roles';
    protected $entity   = 'roles';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
        $this->middleware('permission:role-list', ['only' => ['index', 'show']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page               = collect();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->route        = $this->route;
        $page->entity       = $this->entity;

        if (auth()->user()->is_admin == 1) {
            $roles          = Role::where('name', '!=', 'Super Admin')->get(); 
        } else {
            $roles          = Role::where('name', '!=', 'Super Admin')->where('shop_id', SHOP_ID)->get(); 
        }
        $route_prefix       = (auth()->user()->is_admin == 1)?'admin':'';
        return view($route_prefix . '.' . $this->viewPath . '.index', compact('page', 'roles'))->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page           = collect();
        $page->title    = $this->title;
        $page->link     = url($this->link);
        $page->route    = $this->route;
        $page->entity   = $this->entity;
        $route_prefix   = (auth()->user()->is_admin == 1)?'admin':'';
        return view($route_prefix . '.' .$this->viewPath . '.create', compact('page'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required']);
        $role = Role::create(['name' => $request->input('name'), 'shop_id' => SHOP_ID]);
        // $role->syncPermissions($request->input('permission'));
        $route_prefix   = (auth()->user()->is_admin == 1)?'admin':'';
        return redirect($route_prefix.'/roles')->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page               = collect();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->route        = $this->route;
        $page->entity       = $this->entity;
        $role               = Role::find($id);
        $rolePermissions    = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")->where("role_has_permissions.role_id",$id)->get();
        $route_prefix       = (auth()->user()->is_admin == 1)?'admin':'';
        return view($route_prefix . '.' .$this->viewPath . '.show',compact('page', 'role', 'rolePermissions'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page               = collect();
        $page->title        = $this->title;
        $page->link         = url($this->link);
        $page->route        = $this->route;
        $page->entity       = $this->entity;
        $role               = Role::find($id);
        $permissions        = Permission::where('parent', '=', 0)->orderBy('sequence', 'ASC')->get();
        $rolePermissions    = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')->all();
        $route_prefix       = (auth()->user()->is_admin == 1)?'admin':'';
        return view($route_prefix . '.' .$this->viewPath . '.edit', compact('page','role','permissions','rolePermissions'));
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
            // 'permission' => 'required',
        ]);

        $role       = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        // return redirect()->route('roles.edit', [$id])->with('success','Role updated successfully');
        $route_prefix   = (auth()->user()->is_admin == 1)?'admin':'';
        return redirect($route_prefix.'/roles')->with('success','Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $role               = Role::find($id);
        $rolePermissions    = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")->where("role_has_permissions.role_id",$id)->get();
        
        if(count($rolePermissions) === 0 ) {
            DB::table("roles")->where('id',$id)->delete();
            return redirect()->route('roles.index')->with('success','Role deleted successfully');
        }
        return redirect()->route('roles.index')->with('error','Cant Delete! Role has assigned permissions');
    }
}