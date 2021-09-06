<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use App\Models\Designation;
use App\Models\StaffProfile;
use Illuminate\Support\Arr;
use App\Models\StaffDocument;
use App\Models\Shop;
use DataTables;
use Response;
use Validator;
use DB;
use Auth;
use Hash;
use Mail; 

    
class StaffController extends Controller
{
    protected $title        = 'Staffs';
    protected $viewPath     = 'staffs';
    protected $link         = 'staffs';
    protected $route        = 'staffs';
    protected $entity       = 'staffs';
    protected $uploadPath   = 'store/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:user-list|user-create', ['only' => ['index','store']]);
        //  $this->middleware('permission:user-create', ['only' => ['create','store']]);
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

        // $user_id = Auth::user()->id;
        // $user = User::role('Staffs')->where('parent_id', $user_id)->where('is_active', '!=',  2)->get();
        // echo "<pre>"; print_r($user); exit;

        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $user_id = Auth::user()->id;
        $detail =  User::role('Staffs')->where('parent_id', $user_id)->where('is_active', '!=',  2)->select(['name', 'mobile', 'email', 'is_active', 'id']);

        // $rs = User::where('parent_id', $user_id)->update(['is_Active' => 1]);

        if (isset($request->form)) {
            foreach ($request->form as $search) {
                if ($search['value'] != NULL && $search['name'] == 'search_name') {
                    $names = strtolower($search['value']);
                    $detail->where('name', 'like', "%{$names}%");
                }
            }
        }
        $detail->orderBy('id', 'desc');
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
                $action = ' <a  href="' . url('staffs/' . $detail->id . '/edit') . '" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                $action .= ' <a  href="' . url('staffs/' . $detail->id . '/manage-document') . '" class="btn mr-2 light-blue" title="Update documents"><i class="material-icons">attach_file</i></a>';
                // $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
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
        $page->designations = Designation::pluck('name', 'id');  ;
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
            // 'roles' => 'required'
        ]);

        if ($validator->passes()) {
            $input  = $request->all();
            $user_id = Auth::user()->id;
            // $input['password'] = Hash::make($input['password']);
            $input['parent_id'] = $user_id;
            $user = User::create($input);

            $staff_arr = array('0' => 'Staffs');
            $user->assignRole($staff_arr);

            if(Auth::user()->parent_id == null){
                $shop = new Shop();
                $shop->name = $input['shop_name'];
                $shop->user_id = $user->id;
                $shop->save();
                $user->shop_id = $shop->id; 
            }else{
                $user->shop_id = Auth::user()->shop_id;
            }
            $token = Str::random(64);
            $user->password_create_token = $token;
            $user->save();




            // Staff Profile
            $profile = new StaffProfile();
            $profile->user_id       = $user->id;
            $profile->designation   = $request->designation;
            // $profile->dob           = $request->designation
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
        $staff              = User::find($id);
        $page               = collect();
        $you                = Auth::user();
        $page->title        = $this->title;
        $page->route        = $this->route;
        $page->entity       = $this->entity;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link . '/' . $staff->id);
        $page->form_method  = 'PUT';
        $page->designations = Designation::pluck('name', 'id'); 
        $roles              = Role::where('name', '!=' , 'Super Admin')->pluck('name','name')->all();
        $userRole           = $staff->roles->pluck('name','name')->all();
        return view($this->viewPath . '.edit',compact('staff','roles', 'you' ,'userRole', 'page'));
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
            // 'roles' => 'required'
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
        $user = User::findOrFail($id);
        $user->is_active = 2;
        $user->save();

        return ['flagError' => false, 'message' => $this->title. " deactivated successfully"];
    }

    public function isUnique(Request $request)
    { 
        if($request->user_id == 0){
            $count = User::where('email', $request->email)->count();
            echo ($count > 0 ? 'false' : 'true');
        }else{
            $count = User::where('email', $request->email)->where('id', '!=' , $request->user_id)->count();
            echo ($count > 0 ? 'false' : 'true');
        }
    }

    public function manageStatus(Request $request)
    {

        $user = User::findOrFail($request->user_id);

        if($user){
            $status = ($user->is_active == 0)?1:0;
            $user->is_active = $status;
            $user->save();
            return ['flagError' => false, 'message' => $this->title. " status updated successfully"];
        }

        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserImage(Request $request)
    {

        $user               = User::findOrFail($request->user_id);

        if($user)
        {
            $old_image          = $user->profile;

            if ($old_image != '') {
                \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/users/' . $old_image);
            }
            
            
            // Create storage folder
            $store_path = 'public/' . $this->uploadPath. '/users/';
            Storage::makeDirectory($store_path);

            $image_64   = $request->image; //your base64 encoded data
            $extension  = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
            $replace    = substr($image_64, 0, strpos($image_64, ',')+1); 

            $image      = str_replace($replace, '', $image_64); 
            $image      = str_replace(' ', '+', $image); 
            $imageName  = Str::random(20).'.'.$extension;
            Storage::put($store_path.'/'.$imageName, base64_decode($image));


            $user->profile        = $imageName;
            $user->save();

            return ['flagError' => false, 'logo' => asset('storage/store/users/' . $user->profile),  'message' => "Profile image updated successfully"];
        }
        
        return ['flagError' => true, 'message' => "User not found !"];
    }

    public function manageDocument(Request $request, $id)
    {
        $staff              = User::find($id);
        $page               = collect();
        $you                = Auth::user();
        $page->title        = $this->title;
        $page->route        = $this->route;
        $page->entity       = $this->entity;
        $page->link         = url($this->link);
        $page->form_url     = url($this->link . '/' . $staff->id);
        $documents          = StaffDocument::where('user_id', $id)->get();
        $page->form_method  = 'PUT';

        return view($this->viewPath . '.manage-document',compact('staff','page', 'documents'));
    }

    public function getDocument(Request $request)
    {   
        $user       = User::findOrFail($request->staff_id);
        if($user){
            $documents  = StaffDocument::where('user_id', $user->id)->get();
            if($documents){
                $user_documents = view($this->viewPath . '.list-documents', compact('documents'))->render();  
                return ['flagError' => false, 'html' => $user_documents];
            }
        }

        return ['flagError' => true, 'message' => "Errors occurred Please check !", 'error'=>$validator->errors()->all()];
    }

    public function uploadIdProofs(Request $request)
    {
        $image      = $request->file('file');
        $imageName  = $image->getClientOriginalName();
        
        // Create storage folder
        $store_path = 'public/' . $this->uploadPath. '/users/documents/';
        Storage::makeDirectory($store_path);

        // Upload storage folder
        Storage::putFileAs($store_path, $image, $imageName);

        $document               = new StaffDocument();
        $document->user_id      = $request->staff_id;
        $document->name         = $imageName;
        $document->uploaded_by  = Auth::user()->id;
        $document->save();
        return response()->json(['success'=>$imageName]);
    }

    public function removeIdProofs(Request $request)
    {
        $old_image =  $request->get('filename');
        if ($old_image != '') {
            \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/users/documents/' . $old_image);
        }

        StaffDocument::where('name',$old_image)->where('user_id',$request->staff_id)->delete();

        if($request->data_return_type == 'html'){
            return ['flagError' => false, 'message' => "Document deleted successfully"];
        }

        return $old_image;  
    }

    function downloadFile(Request $request, $document)
    {
        // $file = Storage::disk('public')->get($document);
        $file = asset('storage/store/users/documents/' . $document);
  
        return (new Response($file, 200))
              ->header('Content-Type', 'image/jpeg');

        // Method 1
        // $download_path = asset('storage/store/users/documents/' . $document);
        // return Storage::download($download_path);


    }

    function updateDocumentDetails(Request $request)
    {
        $document = StaffDocument::find($request->document_id);

        if($document){
            $document->details =$request->details;
            $document->save(); 
            return ['flagError' => false, 'message' => "Details updated successfully"];
        }

        return ['flagError' => true, 'message' => "Errors occurred Please check !", 'error'=>$validator->errors()->all()];
    }

    public function deleteIdProofs(Request $request)
    {
        // $old_image =  $request->get('filename');
        // if ($old_image != '') {
        //     \Illuminate\Support\Facades\Storage::delete('public/' . $this->uploadPath . '/users/documents/' . $old_image);
        // }
        // StaffDocument::where('name',$old_image)->where('user_id',$request->staff_id)->delete();
        
        // return ['flagError' => false, 'message' => "Document deleted successfully"];  
    }
}