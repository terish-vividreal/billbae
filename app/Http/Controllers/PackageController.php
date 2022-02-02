<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Additionaltax;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use DB;
use Auth;

class PackageController extends Controller
{
    protected $title    = 'Package';
    protected $viewPath = 'packages';
    protected $link     = 'packages';
    protected $route    = 'packages';
    protected $entity   = 'Package';


    function __construct()
    {
        $this->middleware('permission:package-create', ['only' => ['create','store']]);
        $this->middleware('permission:package-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:package-delete', ['only' => ['destroy']]);
        $this->middleware('permission:package-list', ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;       
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page                           = collect();
        $variants                       = collect();
        $page->title                    = $this->title;
        $page->link                     = url($this->link);
        $page->route                    = $this->route;
        $page->entity                   = $this->entity; 
        $variants->services             = Service::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
        $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'id');   
        $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id');        
        $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');   
        $variants->additional_tax_ids   = [];     
        return view($this->viewPath . '.create', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Package::where('shop_id', SHOP_ID)->orderBy('id', 'desc');
        // if($request['service_category'] != '') {
        //     $service_category = $request['service_category'];
        //     $detail->Where(function ($query) use ($service_category) {
        //         $query->where('service_category_id', $service_category);
        //     });
        // }
        if ($request['package_status'] != '') {
            if ($request['package_status'] == 'deleted') {  
                $detail         = $detail->onlyTrashed();
            }
        }
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail) {
                $action = '';
                if ($detail->deleted_at == null) { 
                    if( Auth::user()->hasPermissionTo('package-edit')) {
                        $action .= ' <a  href="' . url(ROUTE_PREFIX.'/'.$this->route.'/' . $detail->id . '/edit') . '"" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                    }
                    if( Auth::user()->hasPermissionTo('package-delete')) {
                        $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" data-type="remove" onclick="softDelete(this.id)" data-type="remove" class="btn btn-danger btn-sm btn-icon mr-2" title="Deactivate"><i class="material-icons">block</i></a>';    
                    }
                } else {
                    if( Auth::user()->hasPermissionTo('package-delete')) {
                        $action = ' <a href="javascript:void(0);" id="' . $detail->id . '" onclick="restore(this.id)" class="btn mr-2 cyan" title="Restore"><i class="material-icons">restore</i></a>';
                    }
                }
                return $action;
            })
            ->addColumn('price', function($detail){
                $price = '₹ '. $detail->price;
                return $price;
            })
            ->addColumn('services', function($detail){
                $services_list  ='';
                $services       = '';
                foreach ($detail->service as $data) {
                    $services_list.=$data->name.', ' ;
                }

                $services_list = rtrim($services_list, ',');

                $services = Str::limit(strip_tags($services_list), 30);
                if (strlen(strip_tags($services_list)) > 40) {
                    $services .= "<a href='javascript:void(0);' id=' . $detail->id . ' onclick='showMessage(\"".$services_list."\")'>View</a>";
                }
                return $services ;
            })
            ->addColumn('activate', function($detail){
                $checked = ($detail->status == 1) ? 'checked' : '';
                $html = '<div class="switch"><label> <input type="checkbox" '.$checked.' id="' . $detail->id . '" class="activate-user" data-id="'.$detail->id.'" onclick="updateStatus(this.id)"> <span class="lever"></span> </label> </div>';
                return $html;
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                    
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
            'name' => [
                'required',Rule::unique('packages')->where(function($query) {
                  $query->where('shop_id', '=', SHOP_ID);
              })
            ],
            'price' => 'required',
        ]);

        if ($validator->passes()) {
            $data                   = new Package();
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->name;
            $data->slug             = $request->name;
            $data->price            = $request->price;
            $data->service_price    = $request->totalPrice;
            $data->discount         = $request->discount;
            $data->validity_mode    = $request->validity_mode;
            $data->validity         = $request->validity;
            $data->tax_included     = ($request->tax_included == 1) ? 1 : 0 ;
            $data->gst_tax          = $request->gst_tax;
            $data->hsn_code         = $request->hsn_code;
            $data->save();

            $data->service()->sync($request->services);
            if ($request->additional_tax) {
                $data->additionaltax()->sync($request->additional_tax);
            }
            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $package                    = Package::with('service')->find($id);    
        if ($package) {
            $page                           = collect();
            $variants                       = collect();
            $page->title                    = $this->title;
            $page->link                     = url($this->link);
            $page->route                    = $this->route;
            $page->entity                   = $this->entity;   
            $service_ids                    = array();
            $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'id');  
            $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
            $variants->services             = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');
            $variants->additional_tax_ids   = [];
            foreach ($package->service as $data) {
                $service_ids[] = $data->id ;
            }
            if ($package->additionaltax) {
                $variants->additional_tax_ids = [];
                foreach($package->additionaltax as $row){
                    $variants->additional_tax_ids[] = $row->id;
                }
            }
            return view($this->viewPath . '.edit', compact('page', 'variants', 'package', 'service_ids'));
        } else {
            return redirect('services')->with('error', $this->title.' not found');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',Rule::unique('packages')->where(function($query) use($id){
                  $query->where('shop_id', '=', SHOP_ID)->where('id', '!=', $id);;
              })
            ],
            'price' => 'required',
        ]);

        if ($validator->passes()) {
            $data                   = Package::findOrFail($id);
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->name;
            $data->slug             = $request->name;
            $data->price            = $request->price;
            $data->service_price    = $request->totalPrice;
            $data->discount         = $request->discount;
            $data->validity_mode    = $request->validity_mode;
            $data->validity         = $request->validity;
            $data->tax_included     = ($request->tax_included == 1) ? 1 : 0 ;
            $data->gst_tax          = $request->gst_tax;
            $data->hsn_code         = $request->hsn_code;
            $data->save();

            $data->service()->sync($request->services);
            $data->additionaltax()->sync($request->additional_tax);
            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        if (count($package->billingItems) > 0) {
            return ['flagError' => true, 'message' => "Cant deactivate! Package has billing informations"];
        } 
        $package->updated_by = Auth::user()->id;
        $package->save();
        $package->delete();
        return ['flagError' => false, 'message' => " Package deactivated successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $service   = Package::where('id', $id)->withTrashed()->first();
        $service->restore();
        return ['flagError' => false, 'message' => " Package activated successfully"];
    }

    public function updateStatus(Request $request)
    {
        $data               = Package::findOrFail($request->id);
        if ($data) {
            $status         = ($data->status == 0)?1:0;
            $data->status   = $status;
            $data->save();
            return ['flagError' => false, 'message' => $this->title. " status updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
    }
}
