<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\ServiceCategory;
use App\Models\Additionaltax;
use App\Imports\ServicesImport;
use App\Imports\CustomersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;
use App\Models\Service;
use App\Models\User;
use App\Models\Shop;
use App\Models\Hours;
use DataTables;
use Validator;
use Auth;
use Hash;
use DB;

class ServiceController extends Controller
{
    protected $title    = 'Service';
    protected $viewPath = 'services';
    protected $link     = 'services';
    protected $route    = 'services';
    protected $entity   = 'Service';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:service-list|service-create|service-edit|service-delete', ['only' => ['index','store']]);
        // $this->middleware('permission:service-create', ['only' => ['create','store']]);
        // $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:service-delete', ['only' => ['destroy']]);
        // $this->middleware('permission:service-list', ['only' => ['list']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $page                           = collect();
        $variants                       = collect();
        $page->title                    = $this->title;
        $page->link                     = url($this->link);
        $page->route                    = $this->route;
        $page->entity                   = $this->entity;        
        $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');         
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Service::where('shop_id', SHOP_ID)->orderBy('id', 'desc');
        if ($request['service_type'] != '') {
            if ($request['service_type'] == 'deleted') {  
                $detail         = $detail->onlyTrashed();
            }
        }

        if($request['service_category'] != '') {
            $service_category = $request['service_category'];
            $detail->Where(function ($query) use ($service_category) {
                $query->where('service_category_id', $service_category);
            });
        }
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){
                if ($detail->deleted_at == null) { 
                    $action = ' <a  href="' . url(ROUTE_PREFIX.'/services/' . $detail->id . '/edit') . '"" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                    $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" data-type="remove" onclick="softDelete(this.id)" data-type="remove" class="btn btn-danger btn-sm btn-icon mr-2" title="Deactivate"><i class="material-icons">block</i></a>';    
                } else {
                    $action = ' <a href="javascript:void(0);" id="' . $detail->id . '" onclick="restore(this.id)" class="btn mr-2 cyan" title="Restore"><i class="material-icons">restore</i></a>';
                }
                return $action;
            })
            ->addColumn('service_category', function($detail){
                $country = $detail->serviceCategory->name;
                return $country;
            })
            ->addColumn('price', function($detail){
                $price = '₹ '. $detail->price;
                return $price;
            })
            ->addColumn('hours', function($detail){
                $country = $detail->hours->name;
                return $country;
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
        $page                           = collect();
        $variants                       = collect();
        $page->title                    = $this->title;
        $page->link                     = url($this->link);
        $page->route                    = $this->route;
        $page->entity                   = $this->entity; 
        $variants->hours                = Hours::pluck('name', 'id'); 
        $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');   
        $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
        $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'id'); 
        $variants->additional_tax_ids   = [];
        $store                          = Shop::find(Auth::user()->shop_id);  
        return view($this->viewPath . '.create', compact('page', 'variants', 'store'));
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
                'required',Rule::unique('services')->where(function($query) {
                  $query->where('shop_id', '=', SHOP_ID);
              })
            ],
            // 'service_category_id' => 'required',
            'hours_id' => 'required',
            'price' => 'required',
        ]);

        if ($validator->passes()) {
            $data                       = new Service();
            $data->shop_id              = SHOP_ID;
            $data->name                 = $request->name;
            $data->slug                 = $request->name;
            $service_category           = ServiceCategory::firstOrCreate(['shop_id' => SHOP_ID, 'name' => $request->search_service_category]);
            if ($service_category) {
                $data->service_category_id  = $service_category->id;
            }
            $data->price                = $request->price;
            $data->tax_included         = ($request->tax_included == 1) ? 1 : 0 ;            
            $data->lead_before          = $request->lead_before;
            $data->lead_after           = $request->lead_after;  
            $data->hours_id             = $request->hours_id;
            // $data->gst_tax              = CustomHelper::serviceGST(SHOP_ID, $request->gst_tax);
            // $data->hsn_code             = CustomHelper::serviceHSN(SHOP_ID, $request->hsn_code);
            $data->gst_tax              = $request->gst_tax;
            $data->hsn_code             = $request->hsn_code;
            $data->save();

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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::with('gsttax')->find($id);   
        if ($service) {
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link             = url($this->link);
            $page->route            = $this->route;
            $page->entity           = $this->entity; 
            $variants->hours        = Hours::pluck('name', 'id'); 
            $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
            $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'id');  
            $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
            if ($service->additionaltax) {
                $variants->additional_tax_ids = [];
                foreach($service->additionaltax as $row) {
                    $variants->additional_tax_ids[] = $row->id;
                }
            }
            return view($this->viewPath . '.create', compact('page', 'variants', 'service'));
        } else {
            return redirect('services')->with('error', $this->title.' not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {       
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',Rule::unique('services')->where(function($query) use($id) {
                  $query->where('shop_id', '=', SHOP_ID)->where('id', '!=', $id);
              })
            ],
            // 'service_category_id' => 'required',
            'hours_id' => 'required',
            'price' => 'required',
        ]);
        if ($validator->passes()) {
            $data = Service::findOrFail($id);
            if ($data) {
                $data->name                 = $request->name;

                $service_category           = ServiceCategory::firstOrCreate(['shop_id' => SHOP_ID, 'name' => $request->search_service_category]);
                if ($service_category) {
                    $data->service_category_id  = $service_category->id;
                }
                $data->price                = $request->price;
                $data->lead_before          = $request->lead_before;
                $data->lead_after           = $request->lead_after;  
                $data->hours_id             = $request->hours_id;
                $data->tax_included         = ($request->tax_included == 1) ? 1 : 0 ;
                $data->gst_tax              = $request->gst_tax;
                $data->hsn_code             = $request->hsn_code;
                $data->save();

                // if($request->additional_tax){
                    $data->additionaltax()->sync($request->additional_tax);
                // }
                return ['flagError' => false, 'message' => $this->title. " updated successfully"];
            } else {
                return ['flagError' => true, 'message' => "Data not found, Try again!"];
            }
        }
        return ['flagError' => true, 'error'=>$validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        if (count($service->billingItems) > 0) {
            return ['flagError' => true, 'message' => "Cant deactivate! Service has billing informations"];
        } 
        $service->updated_by = Auth::user()->id;
        $service->save();
        $service->delete();
        return ['flagError' => false, 'message' => " Service deactivated successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $service   = Service::where('id', $id)->withTrashed()->first();
        $service->restore();
        return ['flagError' => false, 'message' => " Service activated successfully"];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function import(Request $request) 
    {
        Excel::import(new ServicesImport, $request->file('file')->store('temp'));

        // $import =  new ServicesImport;
        // $import->import(request()->file('file'));
        return redirect('services')->with('success', 'Services Imported Successfully.');
    }

    
}