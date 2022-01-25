<?php

namespace App\Http\Controllers;
use App\Models\ServiceCategory;
use Illuminate\Validation\Rule;
use App\Imports\CustomersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\FunctionHelper;
use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\PaymentType;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\District;
use App\Models\Country;
use App\Models\Package;
use App\Models\State;
use App\Models\Shop;
use DataTables;
use Validator;
use DB;
use Auth;

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
    function __construct()
    {
        // $this->middleware('permission:store-profile-update', ['only' => ['index','update']]);
        $this->middleware(function ($request, $next) {
            $this->timezone     = Shop::where('user_id', Auth::user()->id)->value('timezone');
            $this->time_format  = (Shop::where('user_id', Auth::user()->id)->value('time_format') == 1)?'h':'H';
            return $next($request);
        });
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
        $page->top_search       = 1;
        $page->entity           = $this->entity;       
        return view($this->viewPath . '.list', compact('page', 'variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Htt+p\Response
     */
    public function create()
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity; 
        $store                  = Shop::find(SHOP_ID);        
        // $variants->country      = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');
        $variants->phonecode    = DB::table('shop_countries')->select("id", DB::raw('CONCAT(" +", phonecode , " (", name, ")") AS phone_code'))->pluck('phone_code', 'id');                  
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
        $validator = Validator::make($request->all(), [ 'name' => 'required', ]);
        if ($validator->passes()) {
            $data                   = new Customer();
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->name;   
            $data->gender           = $request->gender;
            $data->dob              = date("Y-m-d", strtotime($request->dob));
            $data->mobile           = $request->mobile;
            $data->phone_code       = $request->phone_code;
            $data->email            = $request->email;
            $data->customer_code    = FunctionHelper::generateCustomerCode();
            // $data->district_id      = $request->district_id;
            // $data->pincode          = $request->pincode;
            // $data->gst              = $request->gst;
            // $data->address          = $request->address;
            $data->save();
            return ['flagError' => false, 'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=>$validator->errors()->all()];
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Customer::where('shop_id', SHOP_ID);
        if ($request['customer_type'] != '') {
            if ($request['customer_type'] == 'deleted') {  
                $detail         = $detail->onlyTrashed();
            }
        }

        if ($request['top_search'] != '') {
            $name       = $request['top_search'];
            $detail     = $detail->where(function($query)use($name){
                $query->where('name', 'LIKE', "{$name}%")
                        ->orWhere('email', 'LIKE', "%{$name}%") 
                        ->orWhere('customer_code', 'LIKE', "%{$name}%") 
                        ->orWhere('mobile', 'LIKE', "%{$name}%") ;

            }); 
        }

        $detail = $detail->orderBy('id', 'desc')->get();
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('status', function($detail) {
            if ($detail->deleted_at == null) {  
                $status = '<span class="chip lighten-5 green green-text">Active</span>';
            } else {
                $status = '<span class="chip lighten-5 red red-text">Banned</span>';
            }
            return $status;
            })
            ->editColumn('mobile', function($detail) {
                $phone_code     = (!empty($detail->phoneCode->phonecode) ? '+' .$detail->phoneCode->phonecode : '');
                $mobile         = (!empty($detail->mobile) ? $phone_code . ' ' . $detail->mobile:'');
                return $mobile;
            })
            ->addColumn('action', function($detail){
            if ($detail->deleted_at == null) {  
                $action      = '<a  href="' . url(ROUTE_PREFIX.'/customers/' . $detail->id . '/edit') . '"" class="btn mr-2 cyan" title="Edit details"><i class="material-icons">mode_edit</i></a>';
                $action     .= '<a href="' . url(ROUTE_PREFIX.'/customers/view-details/' . $detail->id ) . '" data-type="remove" data-type="remove" class="btn btn-sm gradient-45deg-amber-amber mr-2" title="View"><i class="material-icons">visibility</i></a>';
                $action     .= '<a href="javascript:void(0);" id="' . $detail->id . '" data-type="remove" onclick="softDelete(this.id)" data-type="remove" class="btn btn-danger btn-sm btn-icon mr-2" title="Remove"><i class="material-icons">block</i></a>';
            } else {
                $action = ' <a href="javascript:void(0);" id="' . $detail->id . '" onclick="restore(this.id)" class="btn mr-2 cyan" title="Restore"><i class="material-icons">restore</i></a>';
                $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="hardDelete(this.id)" data-type="delete" class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
            }
                return $action;
            })
            ->addColumn('create_bill', function($detail){
                $action = '<a href="' . url(ROUTE_PREFIX.'/customers/create-bill/' . $detail->id ) . '" class="btn btn-sm mr-2 green darken-1" title="View"><i class="material-icons">account_balance_wallet</i></a>';
                return $action;
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                    
    }

    public function billReport(Request $request, $id)
    {
        $detail     =  Billing::select( 'id', 'billed_date', 'amount', 'billing_code', 'payment_status')
                            ->where('customer_id', $id)->where('shop_id', SHOP_ID);
        // if( ($from != '') && ($to != '') ) {
        //     $detail->Where(function ($query) use ($from, $to) {
        //         $query->whereBetween('created_at', [$from, $to]);
        //     });
        // }
        if ($request['billing_code'] != '') {
            $billing_code    = $request['billing_code'];
            $detail         = $detail->where(function($query)use($billing_code){
                    $query->where('billing_code', 'like', '%'.$billing_code.'%');
            }); 
        }
        if ($request['payment_status'] != '') {
            $payment_status    = $request['payment_status'];
            $detail         = $detail->where(function($query)use($payment_status){
                    $query->where('payment_status', $payment_status);
            }); 
        }
        $detail = $detail->orderBy('created_at', 'DESC')->get();
        return Datatables::of($detail)
            ->addIndexColumn()
            ->editColumn('billed_date', function($detail) {
                return FunctionHelper::dateToTimeZone($detail->billed_date, 'd-M-Y '.$this->time_format.':i a');
            })
            ->editColumn('billing_code', function($detail) {
                $billing_code = '';
                $billing_code .=' <a href="' . url(ROUTE_PREFIX.'/billings/show/' . $detail->id) . '">'.$detail->billing_code.'</a>';
                return $billing_code;
            })
            // ->editColumn('customer_id', function($detail){
            //     $customer = $detail->customer->name;
            //     return $customer;
            // })
            ->editColumn('amount', function($detail) {
                $amount = $detail->amount;
                return $amount;
            })
            ->editColumn('payment_status', function($detail) {
                $status = '';
                if ($detail->payment_status == 0) {
                    $status = '<span class="chip lighten-5 red red-text">UNPAID</span>';
                } else {  
                    $status = '<span class="chip lighten-5 green green-text">PAID</span>';                                
                }
                return $status;
            })
            ->addColumn('in_out_time', function($detail) {
                $checkin_time   = FunctionHelper::dateToTimeZone($detail->checkin_time, $this->time_format.':i a');
                $checkout_time  = FunctionHelper::dateToTimeZone($detail->checkout_time, $this->time_format.':i a');
                $in_out_time    = $checkin_time . ' - ' . $checkout_time;
                return $in_out_time;
            })
            ->addColumn('payment_method', function($detail){
                $methods         = '';
                foreach($detail->paymentMethods as $row) {
                    $methods .= $row->paymentype->name. ', '; 
                }
                return rtrim($methods, ', ');
            })
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);
    }

    public function createBill(Request $request, $id)
    {
        $page                       = collect();
        $variants                   = collect();
        $user                       = Auth::user();
        $store                      = Shop::find($user->shop_id);
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $page->route                = $this->route;
        $page->entity               = $this->entity;       
        $customer                   = Customer::find($id);                                                                                                                      
        $variants->countries        = DB::table('shop_countries')->where('status',1)->pluck('name', 'id');          
        $variants->services         = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->packages         = Package::where('shop_id', SHOP_ID)->pluck('name', 'id');
        $variants->payment_types    = PaymentType::where('shop_id', SHOP_ID)->pluck('name', 'id');         
        $variants->time_picker      = ($this->time_format === 'h')?false:true;
        $variants->time_format      = $this->time_format;
        $variants->phonecode        = DB::table('shop_countries')->select("id", DB::raw('CONCAT(" +", phonecode , " (", name, ")") AS phone_code'))->pluck('phone_code', 'id');
        return view($this->viewPath . '.create-bill', compact('page', 'customer', 'variants', 'store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $customer                   = Customer::find($id);
        $last_activity              = Customer::lastActivity($id);
        $completed_bills            = Customer::completedBills($id);
        $pending_bills              = Customer::pendingBills($id);
        if ($customer) { 
            $page                   = collect();
            $variants               = collect();
            $page->title            = $this->title;
            $page->link             = url($this->link);
            $page->route            = $this->route;
            $page->entity           = $this->entity;         
            return view($this->viewPath . '.show', compact('page', 'variants', 'customer', 'last_activity', 'completed_bills', 'pending_bills'));
        } else {
            return redirect('customers')->with('error', $this->title.' not found');
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer                       = Customer::find($id); 
        if ($customer) { 
            $page                       = collect();
            $variants                   = collect();
            $page->title                = $this->title;
            $page->link                 = url($this->link);
            $page->route                = $this->route;
            $page->entity               = $this->entity; 
            $variants->countries        = DB::table('shop_countries')->pluck('name', 'id');
            $store                      = Shop::find(SHOP_ID);        
            $variants->phonecode        = DB::table('shop_countries')->select("id", DB::raw('CONCAT(" +", phonecode , " (", name, ")") AS phone_code'))->pluck('phone_code', 'id');
            if ($customer->country_id != null) {
                $variants->states       = DB::table('shop_states')->where('country_id', $customer->country_id)->pluck('name', 'id');
            } else {
                $variants->country_id   = '';
            }
            if ($customer->state_id != null) {
                $variants->districts   = DB::table('shop_districts')->where('state_id', $customer->state_id)->pluck('name', 'id');
            }
            return view($this->viewPath . '.edit', compact('page', 'customer' ,'variants', 'store'));
        } else {
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
        $validator      = Validator::make($request->all(), [ 'name' => 'required', ]);
        if ($validator->passes()) {
            $data                   = Customer::findOrFail($id);
            $data->name             = $request->name;
            $data->gender           = $request->gender;
            $data->dob              = date("Y-m-d", strtotime($request->dob));
            $data->mobile           = $request->mobile;
            $data->phone_code       = $request->phone_code;
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
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=> $validator->errors()->all()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer, Request $request)
    {
        $billing    = Billing::where('customer_id', $customer->id)->get();
        if (count($billing) > 0) {
            return ['flagError' => true, 'message' => "Cant deactivate! Customer has billing informations"];
        } 
        $customer->delete();
        return ['flagError' => false, 'message' => " Customer removed successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function hardDelete($id, Request $request)
    {
        $customer   = Customer::where('id', $id)->withTrashed()->first();
        $customer->forceDelete();
        return ['flagError' => false, 'message' => " Customer permanently deleted"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $customer   = Customer::where('id', $id)->withTrashed()->first();
        $customer->restore();
        return ['flagError' => false, 'message' => " Customer restored successfully"];
    }

    public function autocomplete(Request $request)
    {
        $data = array();
        $result   = Customer::select("customers.id", DB::raw("CONCAT(customers.name,' - ', COALESCE(customers.mobile, '')) as name"))
                                ->where('shop_id', SHOP_ID)->where("name","LIKE","%{$request->search}%")->orWhere("mobile","LIKE","%{$request->search}%")->get();
        if ($result) {
            foreach($result as $row) {
                $data[] = array([ 'id' => $row->id, 'name' => $row->name]);
            }
        } else {
            $data = [];
        }
        return response()->json($result);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function import() 
    {
        // Excel::import(new CustomersImport,request()->file('file'));  
        $import =  new CustomersImport;
        $import->import(request()->file('file'));
        return redirect('customers')->with('success', 'Customers Imported Successfully.');
    }
}