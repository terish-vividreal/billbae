<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\ServiceCategory;
use DataTables;
use Validator;
use DB;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected $title    = 'Customer';
    protected $viewPath = 'customer';
    protected $link     = 'customers';
    protected $route    = 'customers';
    protected $entity   = 'Customers';

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
        $variants->country      = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');         
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
        // print_r($request->all()); exit;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->passes()) {
            $data                   = new Customer();
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->name;
            $data->gender             = $request->gender;
            $data->dob              = date("Y-m-d", strtotime($request->dob));
            $data->mobile           = $request->mobile;
            
            // $data->billing_name     = $request->billing_name;
            // $data->district_id      = $request->district_id;
            // $data->pincode          = $request->pincode;
            // $data->gst              = $request->gst;
            
            // $data->email            = $request->email;
            // $data->address          = $request->address;

            $data->save();


            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Customer::where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        // if($request['service_category'] != '') {
        //     $service_category = $request['service_category'];
        //     $detail->Where(function ($query) use ($service_category) {
        //         $query->where('service_category_id', $service_category);
        //     });
        // }
            
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){

                
                $action = ' <a  href="' . url(ROUTE_PREFIX.'/customers/' . $detail->id . '/edit') . '"" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="softDelete(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
                return $action;
            })
            // ->addColumn('price', function($detail){
            //     $price = '₹ '. $detail->price;
            //     return $price;
            // })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);  
        if($customer){ 
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link             = url($this->link);
            $page->route            = $this->route;

            $page->entity           = $this->entity; 
            $variants->countries    = DB::table('shop_countries')->where('status', 1)->pluck('name', 'id');


            if($customer->country_id != null){
                $variants->states       = DB::table('shop_states')->where('country_id', $customer->country_id)->pluck('name', 'id');
            }else{
                $variants->country_id   = '';
            }

            if($customer->state_id != null){
                $variants->districts   = DB::table('shop_districts')->where('state_id', $customer->state_id)->pluck('name', 'id');
            }
            
            return view($this->viewPath . '.edit', compact('page', 'customer' ,'variants'));
        }else{
            return redirect('customers')->with('error', $this->title.' not found');
        }   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->passes()) {
            $data = Customer::findOrFail($id);
            $data->name             = $request->name;
            $data->gender           = $request->gender;
            $data->dob              = date("Y-m-d", strtotime($request->dob));
            $data->mobile           = $request->mobile;
            $data->country_id       = $request->country_id;
            $data->state_id         = $request->state_id;
            $data->district_id      = $request->district_id;
            $data->pincode          = $request->pincode;
            $data->gst              = $request->gst;            
            $data->email            = $request->email;
            $data->address          = $request->address;

            $data->save();


            return ['flagError' => false, 'message' => $this->title. " updated successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occured. Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function autocomplete(Request $request)
    {
        $data = Customer::select("customers.id", DB::raw("CONCAT(customers.name,' - ',customers.mobile) as name"))
                ->where('shop_id', SHOP_ID)
                ->where("name","LIKE","%{$request->search}%")
                ->orWhere("mobile","LIKE","%{$request->search}%")                
                ->get();
        return response()->json($data);
    }
}
