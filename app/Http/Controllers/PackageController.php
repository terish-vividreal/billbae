<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceCategory;
use DataTables;
use Validator;

class PackageController extends Controller
{
    protected $title    = 'Package';
    protected $viewPath = 'packages';
    protected $link     = 'packages';
    protected $route    = 'packages';
    protected $entity   = 'Package';

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
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link              = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity; 
        $variants->services     = Service::where('shop_id', SHOP_ID)->pluck('name', 'id'); 

        
        $variants->service_category      = ServiceCategory::where('shop_id', SHOP_ID)->pluck('name', 'id');         
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
            
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){
                $action = ' <a  href="' . url(ROUTE_PREFIX.'/packages/' . $detail->id . '/edit') . '"" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"> <i class="icon-1x fas fa-trash-alt"></i></a>';
                return $action;
            })
            ->addColumn('price', function($detail){
                $price = '₹ '. $detail->price;
                return $price;
            })
            ->addColumn('services', function($detail){
                $services ='';
                foreach($detail->service as $data){
                    $services.=$data->name.',' ;
                }
                return rtrim($services, ',');
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
            $data->save();

            $data->service()->sync($request->services);

            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];
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
        $package = Package::with('service')->find($id);    
        if($package){
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link              = url($this->link);
            $page->route            = $this->route;
            $page->entity           = $this->entity;   
            $service_ids            = array();

            foreach($package->service as $data){
                $service_ids[] = $data->id ;
            }
            return view($this->viewPath . '.edit', compact('page', 'variants', 'package', 'service_ids'));
        }else{
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
            $data = Package::findOrFail($id);
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->name;
            $data->slug             = $request->name;
            $data->price            = $request->price;
            $data->service_price    = $request->totalPrice;
            $data->discount         = $request->discount;
            $data->validity_mode    = $request->validity_mode;
            $data->validity         = $request->validity;
            $data->save();


            $data->service()->sync($request->services);

            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Package $package)
    {
        //
    }
}
