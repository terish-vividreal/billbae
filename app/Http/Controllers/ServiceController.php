<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Additionaltax;
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
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;        
        $variants->service_category      = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');         
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Service::where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        // if($request['name'] != '') {
        //     $names = strtolower($request['name']);
        //     $detail->Where(function ($query) use ($names) {
        //         $query->where('name', 'like', "'$names%'");
        //     });
        // }

        if($request['service_category'] != '') {
            $service_category = $request['service_category'];
            $detail->Where(function ($query) use ($service_category) {
                $query->where('service_category_id', $service_category);
            });
        }
            
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){
                $action = ' <a  href="' . url(ROUTE_PREFIX.'/services/' . $detail->id . '/edit') . '"" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"> <i class="icon-1x fas fa-trash-alt"></i></a>';
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
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity; 
        $variants->hours        = Hours::pluck('name', 'id'); 
        $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');  
        $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'percentage');  
        $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
        $variants->additional_tax_ids   = [];
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
            'name' => [
                'required',Rule::unique('services')->where(function($query) {
                  $query->where('shop_id', '=', SHOP_ID);
              })
            ],
            'service_category_id' => 'required',
            'hours_id' => 'required',
            'price' => 'required',
        ]);

        if ($validator->passes()) {

            $data                       = new Service();
            $data->shop_id              = SHOP_ID;
            $data->name                 = $request->name;
            $data->slug                 = $request->name;
            $data->service_category_id  = $request->service_category_id;
            $data->price                = $request->price;
            $data->tax_included         = ($request->tax_included == 1) ? 1 : 0 ;            
            $data->lead_before          = $request->lead_before;
            $data->lead_after           = $request->lead_after;  
            $data->hours_id             = $request->hours_id;
            $data->gst_tax              = $request->gst_tax;

            $data->save();

            if($request->additional_tax){
                $data->additionaltax()->sync($request->additional_tax);
            }

            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

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
        $service = Service::find($id);    
        if($service){
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link              = url($this->link);
            $page->route            = $this->route;
            $page->entity           = $this->entity; 
            $variants->hours        = Hours::pluck('name', 'id'); 
            $variants->service_category     = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
            $variants->tax_percentage       = DB::table('gst_tax_percentages')->pluck('percentage', 'percentage');  
            $variants->additional_tax       = Additionaltax::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
            
            if($service->additionaltax){
                $variants->additional_tax_ids = [];
                foreach($service->additionaltax as $row){
                    $variants->additional_tax_ids[] = $row->id;
                }
            }
            return view($this->viewPath . '.create', compact('page', 'variants', 'service'));
        }else{
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
            'service_category_id' => 'required',
            'hours_id' => 'required',
            'price' => 'required',
        ]);


        if ($validator->passes()) {
            $data = Service::findOrFail($id);
            if($data){
                $data->name                 = $request->name;
                $data->service_category_id  = $request->service_category_id;
                $data->price                = $request->price;
                $data->lead_before          = $request->lead_before;
                $data->lead_after           = $request->lead_after;  
                $data->hours_id             = $request->hours_id;
                $data->tax_included         = ($request->tax_included == 1) ? 1 : 0 ;
                $data->gst_tax              = $request->gst_tax;

                $data->save();

                // if($request->additional_tax){
                    $data->additionaltax()->sync($request->additional_tax);
                // }

                return ['flagError' => false, 'message' => $this->title. " updated successfully"];
            }else{
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
    public function destroy(State $state)
    {
        //
    }
}
