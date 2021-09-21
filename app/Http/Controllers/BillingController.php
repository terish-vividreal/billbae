<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\ServiceCategory;
use App\Helpers\TaxHelper;
use Illuminate\Http\Request;
use App\Models\ShopBilling;
use App\Models\BillingItemAdditionalTax;
use App\Helpers\FunctionHelper;
use App\Models\BillingFormat;
use App\Events\SalesCompleted;
use App\Models\BillingItemTax;
use App\Models\BillingAddres;
use App\Models\PaymentType;
use App\Models\BillingItem;
use App\Models\BillAmount;
use App\Models\Customer;
use App\Models\Billing;
use App\Models\Package;
use App\Models\Country;
use App\Models\District;
use App\Models\Service;
use App\Models\State;
use App\Models\Shop;
use DataTables;
use Validator;
use Carbon;
use Event;
use Auth;
use DB;
use PDF;

class BillingController extends Controller
{
    protected $title        = 'Billing';
    protected $viewPath     = 'billing';
    protected $link         = 'billings';
    protected $route        = 'billings';
    protected $entity       = 'Billing';
    protected $timezone     = '';
    protected $time_format  = '';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
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
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;      
        return view($this->viewPath . '.list', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page                       = collect();
        $variants                   = collect();
        $user                       = Auth::user();
        $store                      = Shop::find($user->shop_id);
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $page->route                = $this->route;
        $page->entity               = $this->entity;                                                                                                                             
        $variants->countries        = DB::table('shop_countries')->where('status',1)->pluck('name', 'id');          
        $variants->services         = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->packages         = Package::where('shop_id', SHOP_ID)->pluck('name', 'id');
        $variants->payment_types    = PaymentType::where('shop_id', SHOP_ID)->pluck('name', 'id');         
        $variants->time_picker      = ($this->time_format === 'h')?false:true;
        $variants->time_format      = $this->time_format;
        return view($this->viewPath . '.create', compact('page', 'variants', 'store'));
    }

    /**
     * Display a listing of the resource in datatable.
     * @throws \Exception
     */
    public function lists(Request $request)
    {
        $detail =  Billing::where('shop_id', SHOP_ID)->orderBy('id', 'desc');
        // if($request['service_category'] != '') {
        //     $service_category = $request['service_category'];
        //     $detail->Where(function ($query) use ($service_category) {
        //         $query->where('service_category_id', $service_category);
        //     });
        // }
        return Datatables::of($detail)
            ->addIndexColumn()
            ->addColumn('action', function($detail){
                $action = '';
                if($detail->payment_status == 0){
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/invoice/' . $detail->id ) . '"" class="btn mr-2 blue tooltipped" title="Update Payment"><i class="material-icons">payment</i></a>';
                } 
                if($detail->status == 0){
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/invoice/edit/' . $detail->id) . '"" class="btn mr-2 cyan tooltipped" data-tooltip="Edit details"><i class="material-icons">mode_edit</i></a>';
                    $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="deleteBill(this.id)"  class="btn red btn-sm btn-icon mr-2" title="Delete"><i class="material-icons">delete</i></a>';
                }else{
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/show/' . $detail->id) . '"" class="btn mr-2 cyan tooltipped" title="View details"><i class="material-icons">remove_red_eye</i></a>';
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'.$this->route.'/invoice-data/generate-pdf/'. $detail->id) . '"" class="btn mr-2 indigo tooltipped" title="Download Invoice"><i class="material-icons mr-4">file_download</i></a>';
                    $action .= ' <a href="javascript:void(0);" id="' . $detail->id . '" onclick="cancelBill(this.id)" class="btn orange btn-sm btn-icon mr-2" title="Cancel Bill"><i class="material-icons">cancel</i> </a>';
                }   
                return $action;
            })
            ->editColumn('customer_id', function($detail){
                $customer = $detail->customer->name;
                return $customer;
            })
            ->editColumn('amount', function($detail){
                $amount = '₹ '. $detail->amount;
                return $amount;
            })
            ->editColumn('payment_status', function($detail){
                $status = '';
                if($detail->payment_status == 0){
                    $status = '<span class="chip lighten-5 red red-text">UNPAID</span>';
                }else{  
                    $status = '<span class="chip lighten-5 green green-text">PAID</span>';                                
                }
                return $status;
            })
            ->addColumn('updated_date', function($detail){
                if($detail->payment_status == 1){
                    return FunctionHelper::dateToTimeZone($detail->billed_date, 'd-M-Y '.$this->time_format.':i a');
                }  
            })
            ->addColumn('bill_status', function($detail){
                $status = '';
                if($detail->status == 0){
                    $status = '<span class="badge badge-warning">Open</span>';
                }else if($detail->status == 1){  
                    $status = '<span class="badge badge-success">Completed</span>';                                
                }else{
                    $status = '<span class="badge badge-danger">Cancelled</span>';             
                }
                return $status;
            })
            
            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true);                    
    }

    /**
     * Return amount after discount
     *
     * @return \Illuminate\Http\Response
     */
    public function manageDiscount(Request $request) 
    {
        // echo $request->discount_value; exit;


        $billing_item       = BillingItem::findOrFail($request->billing_item_id);
        if($billing_item)
        {
            if($request->discount_action == 'add')
            {
                $billing_item->is_discount_used = 1 ;
                $billing_item->discount_type    = $request->discount_type;
                $billing_item->discount_value   = $request->discount_value;
            }else
            {
                $billing_item->is_discount_used = 0 ;
                $billing_item->discount_type    = null;
                $billing_item->discount_value   = null;
            }
            $billing_item->save();
            return response()->json(['flagError' => false]);
        }
        $errors = array('Errors Occurred. Please check !');
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=> $errors];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        // Formatting time 
        $billed_date    = FunctionHelper::dateToTimeFormat($request->billed_date);
        $checkin_time   = FunctionHelper::dateToTimeFormat($request->checkin_time);
        $checkout_time  = FunctionHelper::dateToTimeFormat($request->checkout_time);

        $billing                    = new Billing();
        $billing->shop_id           = SHOP_ID;
        $billing->customer_id       = $request->customer_id;
        $billing->customer_type     = Customer::isExisting($request->customer_id);        
        $billing->amount            = $request->grand_total;
        $billing->billed_date       = FunctionHelper::dateToUTC($billed_date, 'Y-m-d H:i:s A');
        $billing->checkin_time      = FunctionHelper::dateToUTC($checkin_time, 'Y-m-d H:i:s A'); 
        $billing->checkout_time     = FunctionHelper::dateToUTC($checkout_time, 'Y-m-d H:i:s A'); 

        $billing->payment_status    = 0 ;
        // $billing->billing_code      = FunctionHelper::generateCode(0, 8, 'BB', SHOP_ID.Auth::user()->id);
        $billing->address_type      = ($request->billing_address_checkbox == 0) ? 'company' : 'customer' ;
        $billing->save();
        
        if($request->billing_address_checkbox == 0)
        {
            $address                    = new BillingAddres();
            $address->shop_id           = SHOP_ID;
            $address->bill_id           = $billing->id;
            $address->customer_id       = $request->customer_id;
            $address->billing_name      = $request->customer_billing_name;
            $address->country_id        = $request->country_id;
            $address->state_id          = $request->state_id;
            $address->district_id       = $request->district_id;
            $address->pincode           = $request->pincode;
            $address->gst               = $request->customer_gst;
            $address->address           = $request->address;
            $address->updated_by        = Auth::user()->id;
            $address->save();
        }
        
        if($request->bill_item){
            foreach($request->bill_item as $row){
                $item                   = new BillingItem();
                $item->billing_id       = $billing->id ;
                $item->customer_id      = $request->customer_id ;
                $item->item_type        = ($request->service_type == 1) ? 'services' : 'packages' ;
                $item->item_id          = $row ;
                $item->save();
            }       
        }
        return redirect($this->route.'/invoice/'.$billing->id);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePayment(Request $request)
    {
        $data = [];
        foreach($request->input('payment_amount') as $key => $value) {
            $data["payment_amount.{$key}"] = 'required';
        }
        $messages = [
            'required' => 'Please enter amount'
        ];
        $validator = Validator::make($request->all(), $data, $messages);
        // echo array_sum($request->payment_amount);
        // echo "<pre>"; print_r($request->all()); 
        // exit;
        if ($validator->passes()) {
            if($request->grand_total == array_sum($request->payment_amount))
            {
                // Store default billing format
                $default_format     = Billing::getDefaultFormat();
                if(count($request->input('payment_type')) == 1 )
                {
                    // Step 1 - Checking payment type has billing format
                    $format         = BillingFormat::where('shop_id', SHOP_ID)->where('payment_type', $request->input('payment_type')[0])->first();
                    $format_id      = (isset($format))?$format->id:$default_format->id;
                    $billing_code   = FunctionHelper::getBillingCode($format_id);
                }else{
                    $format_id      = $default_format->id;
                    $billing_code   = FunctionHelper::getBillingCode($default_format->id);
                }
                $billing                    = Billing::findOrFail($request->billing_id);
                $billing->payment_status    = 1;
                $billing->amount            = $request->grand_total;
                $billing->status            = 1;
                $billing->billing_code      = $billing_code;
                $billing->save();
                foreach($request->input('payment_amount') as $key => $value) {
                    $bill_amount                    = new BillAmount();
                    $bill_amount->bill_id           = $billing->id;
                    $bill_amount->payment_type      = $request->payment_type[$key];
                    $bill_amount->amount            = $request->payment_amount[$key];
                    $bill_amount->billing_format_id = $format_id;
                    $bill_amount->save();
                }
                if($billing->items){
                    $item_ids                   = [];
                    $billing_items_array        = $billing->items->toArray();
                    $item_type                  = $billing_items_array[0]['item_type'];
                    foreach($billing_items_array as $row){
                        $item_ids[] = $row['item_id']; 
                    }
                    if($item_type == 'services')
                    {
                        $billing_items = Service::select('services.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                            ->join('billing_items', 'billing_items.item_id', '=', 'services.id')->where('services.shop_id', SHOP_ID)->where('billing_items.billing_id', $request->billing_id)->whereIn('services.id', $item_ids)->orderBy('services.id', 'desc')->get();
                    }
                    else            
                    {
                        $billing_items = Package::select('packages.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                            ->join('billing_items', 'billing_items.item_id', '=', 'packages.id')->where('packages.shop_id', SHOP_ID)->where('billing_items.billing_id', $request->billing_id)->whereIn('packages.id', $item_ids)->orderBy('packages.id', 'desc')->get();
                    }
                    $discount = array();
                    foreach($billing_items as $key => $row){
                        $tax_array                          = TaxHelper::simpleTaxCalculation($row);
                        $item_tax                           = new BillingItemTax();
                        $item_tax->bill_id                  = $billing->id;
                        $item_tax->bill_item_id             = $row->billingItemsId;
                        $item_tax->item_id                  = $row->id;
                        $item_tax->tax_method               = 'split_2';
                        $item_tax->total_tax_percentage     = $tax_array['total_tax_percentage'];
                        $item_tax->cgst_percentage          = $tax_array['cgst_percentage'];
                        $item_tax->sgst_percentage          = $tax_array['sgst_percentage'];
                        $item_tax->cgst_amount              = $tax_array['cgst'];
                        $item_tax->sgst_amount              = $tax_array['sgst'];
                        $item_tax->grand_total              = $tax_array['total_amount'];
                        $item_tax->tax_amount               = $tax_array['amount'];
                        $item_tax->save();
                        if( count($tax_array['additiona_array']) > 0){
                            foreach($tax_array['additiona_array'] as $additional){
                                $additional_obj                 = new BillingItemAdditionalTax();
                                $additional_obj->bill_id        = $billing->id;
                                $additional_obj->bill_item_id   = $row->billingItemsId;
                                $additional_obj->item_id        = $row->id;
                                $additional_obj->tax_name       = $additional['name'];
                                $additional_obj->percentage     = $additional['percentage'];
                                $additional_obj->percentage     = $additional['percentage'];
                                $additional_obj->amount         = $additional['amount'];
                                $additional_obj->save();
                            }
                        }
                    }
                }
                // $amount         = $billing->paymentMethods->where('payment_type', 1)->sum('amount');


                Event::dispatch(new SalesCompleted($billing->id));
                return ['flagError' => false, 'message' => "Payment submitted successfully !"];           
            }
            else
            {
                $errors = array('Sub total and the entered amounts are not equal');
                return ['flagError' => true, 'message' => "Total amount is not matching !",  'error'=> $errors];
            }
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=>$validator->errors()->all()];
    }
    public function updateInvoice(Request $request, $id)
    {
        // Formatting time 
        $billed_date    = FunctionHelper::dateToTimeFormat($request->billed_date);
        $checkin_time   = FunctionHelper::dateToTimeFormat($request->checkin_time);
        $checkout_time  = FunctionHelper::dateToTimeFormat($request->checkout_time);
        $billing                    = Billing::findOrFail($id);
        $billing->customer_id       = $request->customer_id;
        $billing->amount            = $request->grand_total;
        $billing->billed_date       = FunctionHelper::dateToUTC($billed_date, 'Y-m-d H:i:s A');
        $billing->checkin_time      = FunctionHelper::dateToUTC($checkin_time, 'Y-m-d H:i:s A'); 
        $billing->checkout_time     = FunctionHelper::dateToUTC($checkout_time, 'Y-m-d H:i:s A'); 
        $address                    = BillingAddres::where('bill_id', $id)->where('customer_id', $request->customer_id)->first();
        $billing_address_checkbox = $request->has('billing_address_checkbox') ? 1 : 0;
        // echo $checked; exit;
        if($billing_address_checkbox == 0)
        {
            if($address){
                // echo "update";
                // echo "<pre>" ; print_r($request->all()); exit;
                $address->customer_id       = $request->customer_id;
                $address->billing_name      = $request->customer_billing_name;
                $address->country_id        = $request->country_id;
                $address->state_id          = $request->state_id;
                $address->district_id       = $request->district_id;
                $address->pincode           = $request->pincode;
                $address->gst               = $request->customer_gst;
                $address->address           = $request->address;
                $address->updated_by        = Auth::user()->id;
                $address->save();
                $billing->address_type      = 'company' ;
            }else{
                // echo "new";
                $new_address                    = new BillingAddres();
                $new_address->shop_id           = SHOP_ID;
                $new_address->bill_id           = $billing->id;
                $new_address->customer_id       = $request->customer_id;
                $new_address->billing_name      = $request->customer_billing_name;
                $new_address->country_id        = $request->country_id;
                $new_address->state_id          = $request->state_id;
                $new_address->district_id       = $request->district_id;
                $new_address->pincode           = $request->pincode;
                $new_address->gst               = $request->customer_gst;
                $new_address->address           = $request->address;
                $new_address->updated_by        = Auth::user()->id;
                $new_address->save();
                $billing->address_type      = 'company' ;
            }
        }else{

            if($address){
                $address->delete();
                $billing->address_type      = 'customer' ;
            }
        }
        $billing->save();
        $old_bill_items = BillingItem::where('billing_id', $id)->where('customer_id', $request->customer_id)->delete();
        if($request->bill_item){
            foreach($request->bill_item as $row){
                $item                   = new BillingItem();
                $item->billing_id       = $billing->id ;
                $item->customer_id      = $request->customer_id ;
                $item->item_type        = ($request->service_type == 1) ? 'services' : 'packages' ;
                $item->item_id          = $row ;
                $item->save();
            }       
        }
        return redirect($this->route.'/invoice/'.$id);
    }

    /**
     * Create new customer
     *
     * @param  \App\Models\Customer  
     * @return \Illuminate\Http\Response
     * return id
     */
    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_customer_name' => 'required',
        ]);

        if ($validator->passes()) {
            $data                   = new Customer();
            $data->shop_id          = SHOP_ID;
            $data->name             = $request->new_customer_name;
            $data->gender           = $request->gender;
            $data->dob              = date("Y-m-d", strtotime($request->dob));
            $data->mobile           = $request->new_customer_mobile;
            $data->email            = $request->new_customer_email;
            $data->save();
            return ['flagError' => false, 'customer_id' => $data->id,  'message' => $this->title. " added successfully"];
        }
        return ['flagError' => true, 'message' => "Errors Occurred. Please check !",  'error'=> $validator->errors()->all()];
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request, $id)
    {
        $page                       = collect();
        $variants                   = collect();
        $page->title                = $this->title;
        $page->link                 = url($this->link);
        $page->route                = $this->route;
        $page->entity               = $this->entity; 
        $user                       = Auth::user();
        $billing                    = Billing::findOrFail($id);
        $variants->payment_types    = PaymentType::pluck('name', 'id'); 
        $variants->store            = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                        ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                        ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                        ->find($user->shop_id); 

        if($billing->status === 0){
            if($billing->items){

                $billing_items_array        = $billing->items->toArray();
                $item_type                  = $billing_items_array[0]['item_type'];

                // $variants->billing_items    = array();
                foreach($billing_items_array as $row)
                {
                    $ids[] = $row['item_id']; 
                }

                if($item_type == 'services'){
                    $variants->billing_items = Service::with('gsttax')->where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
                }else{
                    $variants->billing_items = Package::with('gsttax')->where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
                }

                foreach($variants->billing_items as $key => $row){
                    $tax_array  = TaxHelper::simpleTaxCalculation($row);
                    $variants->billing_items[$key]['tax_array'] = $tax_array;
                }

                $variants->item_ids = $ids ;
            }
            
            if($billing){
                // echo "<pre>"; print_r($variants->billing_items->toArray()); exit; 
                // echo "<pre>"; print_r($billing->items->toArray()); exit; 
                // echo "<pre>"; print_r($variants->store->billing); exit; 
                $variants->item_ids = $ids ;
                $variants->bill_id  = $id ;
                return view($this->viewPath . '.invoice', compact('page', 'billing' ,'variants'));
            }
        }
        abort(404);  
    }

    public function getInvoiceData(Request $request)
    {
        $grand_total            = 0 ;
        $billing                = Billing::findOrFail($request->bill_id);       
        $discount               = $request->discount;

        if($billing->items){
            
            $billing_items_array        = $billing->items->toArray();
            $item_type                  = $billing_items_array[0]['item_type'];

            if($item_type == 'services')
            {
                $billing_items = Service::select('services.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                    ->join('billing_items', 'billing_items.item_id', '=', 'services.id')
                                    ->where('services.shop_id', SHOP_ID)
                                    ->where('billing_items.billing_id', $request->bill_id)
                                    ->whereIn('services.id', $request->item_ids)
                                    ->orderBy('services.id', 'desc')->get();
            }
            else            
            {
                $billing_items = Package::select('packages.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                    ->join('billing_items', 'billing_items.item_id', '=', 'packages.id')
                                    ->where('packages.shop_id', SHOP_ID)
                                    ->where('billing_items.billing_id', $request->bill_id)
                                    ->whereIn('packages.id', $request->item_ids)
                                    ->orderBy('packages.id', 'desc')->get();
            }

            // foreach($billing_items as $row){
            //     echo $row->name . '<br>';
            // }
            // exit;


            foreach($billing_items as $key => $row){
                $tax_array          = TaxHelper::simpleTaxCalculation($row, $discount);
                $billing_items[$key]['tax_array'] = $tax_array;
                // echo $billing_items[$key]['tax_array']['discount_applied']; 
                // print_r($billing_items[$key]['tax_array']); 
                // exit;
                // $total_percentage = $row->gst_tax ;                
                // if(count($row->additionaltax) > 0){
                //     foreach($row->additionaltax as $additional){
                //         $total_percentage = $total_percentage+$additional->percentage;
                //     } 
                // }
                // $total_service_tax      = ($row->price/100) * $total_percentage ; 
                // $gross_charge           = ($row->tax_included == 1) ? $row->price : $row->price + $total_service_tax  ;
                // $grand_total            = ($grand_total + $gross_charge); 
                $grand_total            = ($grand_total + $billing_items[$key]['tax_array']['total_amount']); 
                if($billing_items[$key]['tax_array']['discount_applied'] == 1){
                    $grand_total            = ($grand_total - $billing_items[$key]['tax_array']['discount_amount']); 
                }
            }
        }
        // print_r($tax_array); exit;
        $invoice_details = view($this->viewPath . '.invoice-data', compact('billing_items'))->render();  
        return ['flagError' => false, 'grand_total' => $grand_total, 'html' => $invoice_details];
        // return response($questionHtml);       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF(Request $request, $id)
    {
        $grand_total            = 0 ;
        $user                   = Auth::user();
        $billing                = Billing::findOrFail($id);
        $store                  = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                    ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                    ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                    ->find($user->shop_id); 
        if($billing){
            $billing_items_array        = $billing->items->toArray();
            $item_type                  = $billing_items_array[0]['item_type'];
            foreach($billing_items_array as $row)
            {
                $item_ids[] = $row['item_id']; 
            }
            if($item_type == 'services')
            {
                $billing_items = Service::select('services.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                    ->join('billing_items', 'billing_items.item_id', '=', 'services.id')
                                    ->where('services.shop_id', SHOP_ID)
                                    ->where('billing_items.billing_id', $id)
                                    ->whereIn('services.id', $item_ids)
                                    ->orderBy('services.id', 'desc')->get();
            }
            else            
            {
                $billing_items = Package::select('packages.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 'billing_items.discount_type', 'billing_items.discount_value')
                                    ->join('billing_items', 'billing_items.item_id', '=', 'packages.id')
                                    ->where('packages.shop_id', SHOP_ID)
                                    ->where('billing_items.billing_id', $id)
                                    ->whereIn('packages.id', $item_ids)
                                    ->orderBy('packages.id', 'desc')->get();
            }      
            // echo "<pre>"; print_r($billing_items); exit;
            foreach($billing_items as $key => $row){
                $tax_array          = TaxHelper::simpleTaxCalculation($row);
                $billing_items[$key]['tax_array'] = $tax_array;
                $grand_total            = ($grand_total + $billing_items[$key]['tax_array']['total_amount']); 
                if($billing_items[$key]['tax_array']['discount_applied'] == 1){
                    $grand_total            = ($grand_total - $billing_items[$key]['tax_array']['discount_amount']); 
                }
            }
        }              
        $data = [
            'billing' => $billing,
            'store' => $store,
            'billing_items' => $billing_items,
            'grand_total' => $grand_total,
        ];
        // echo "<pre>"; print_r($data); exit;
        $pdf        = PDF::loadView($this->viewPath . '.invoice-pdf', $data);
        $bill_title = str_replace(' ', '-', strtolower($billing->customer->name));
        return $pdf->download($bill_title.'-invoice.pdf');
    }
    public function editInvoice(Request $request , $id)
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;
        $variants->states       = array();
        $variants->districts    = array();
        $variants->country      = DB::table('shop_countries')->where('status', 1)->pluck('name', 'id');         
        $variants->services     = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->packages     = Package::where('shop_id', SHOP_ID)->pluck('name', 'id'); 

        $billing                = Billing::findOrFail($id);

        // echo "<pre>"; print_r($billing);
        // echo "<pre>"; print_r($billing->billingaddress);exit;
        // echo $billing->customer->billingaddress->shopCountry->name; 
        if($billing->status === 0){
            $user                   = Auth::user();
            $billing                = Billing::findOrFail($id);
            $variants->store        = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                        ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                        ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                        ->find($user->shop_id); 
            if(isset($billing->billingaddress->country_id )){
                $variants->states        = DB::table('shop_states')->where('country_id',$billing->billingaddress->country_id)->pluck('name', 'id'); 
            }
            if(isset($billing->billingaddress->state_id )){
                $variants->districts     = DB::table('shop_districts')->where('state_id', $billing->billingaddress->state_id)->pluck('name', 'id');   
            }
            if($billing->items){
                $billing_items_array        = $billing->items->toArray();
                $item_type                  = $billing_items_array[0]['item_type'];
                foreach($billing_items_array as $row)
                {
                    $ids[] = $row['item_id']; 
                }
                if($item_type == 'services'){
                    $service_type   = 1;
                    $item_type      = 'services' ;
                    // $variants->billing_items = Service::where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
                }else{
                    $service_type = 2;
                    $item_type      = 'packages' ;
                    // $variants->billing_items = Package::where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
                }
            }
            
            if($billing){
                $variants->item_ids = $ids ;
                $variants->bill_id  = $id ;
                $variants->time_picker  = ($this->time_format === 'h')?false:true;
                $variants->time_format  = $this->time_format;

                return view($this->viewPath . '.edit-invoice', compact('page', 'billing', 'service_type', 'item_type' ,'variants'));
            }
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // $dt = Carbon\Carbon::parse('2021-07-20 10:00:00')->timezone('Asia/Dubai');
        // $toDay = $dt->format('d');
        // $toMonth = $dt->format('m');
        // $toYear = $dt->format('Y');
        // $dateUTC = Carbon\Carbon::createFromDate($toYear, $toMonth, $toDay, 'UTC');
        // $datePST = Carbon\Carbon::createFromDate($toYear, $toMonth, $toDay, 'Asia/Dubai');
        // $difference = $dateUTC->diffInHours($datePST);
        // $date = $dt->addHours($difference);
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;
        $billing                = Billing::findOrFail($id);
        if($billing){
            if($billing->status === 1){
                $user                   = Auth::user();
                $billing                = Billing::findOrFail($id);
                $variants->store        = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                            ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                            ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                            ->find($user->shop_id); 
                if($billing->items){
                    $billing_items_array        = $billing->items->toArray();
                    $item_type                  = $billing_items_array[0]['item_type'];
                    foreach($billing_items_array as $row)
                    {
                        $ids[] = $row['item_id']; 
                    }
                    if($item_type == 'services')
                    {
                        // $billing_items = Service::select('services.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 
                        //                     'billing_items.discount_type', 'billing_items.discount_value',
                        //                     'billing_item_taxes.cgst_percentage', 'billing_item_taxes.sgst_percentage',
                        //                     'billing_item_taxes.tax_amount', 'billing_item_taxes.sgst_amount','billing_item_taxes.grand_total', 'billing_item_taxes.cgst_amount',
                        //                     )
                        //                     ->join('billing_items', 'billing_items.item_id', '=', 'services.id')
                        //                     ->join('billing_item_taxes', 'billing_item_taxes.bill_item_id', '=', 'billing_items.id')                                        
                        //                     // ->join('billing_item_additional_taxes', 'billing_item_additional_taxes.bill_item_id', '=', 'billing_items.id')                                        
                        //                     ->where('services.shop_id', SHOP_ID)
                        //                     ->where('billing_items.billing_id', $id)
                        //                     ->whereIn('services.id', $ids)
                        //                     //->groupBy('services.id', 'desc')
                        //                     ->orderBy('services.id', 'desc')->get();

                        $billing_items  = BillingItem::select('services.name',  'services.hsn_code', 'billing_items.id as id', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 
                                            'billing_items.discount_type', 'billing_items.discount_value',
                                            'billing_item_taxes.cgst_percentage', 'billing_item_taxes.sgst_percentage',
                                            'billing_item_taxes.tax_amount', 'billing_item_taxes.sgst_amount','billing_item_taxes.grand_total', 'billing_item_taxes.cgst_amount',
                                            )
                                            ->join('services', 'services.id', '=', 'billing_items.item_id')
                                            ->join('billing_item_taxes', 'billing_item_taxes.bill_item_id', '=', 'billing_items.id')                                        
                                            ->where('services.shop_id', SHOP_ID)
                                            ->where('billing_items.billing_id', $id)
                                            ->whereIn('services.id', $ids)
                                            ->orderBy('services.id', 'desc')->get();
                    }
                    else            
                    {
                        // $billing_items = Package::select('packages.*', 'billing_items.id as billingItemsId', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 
                        //                     'billing_items.discount_type', 'billing_items.discount_value',
                        //                     'billing_item_taxes.cgst_percentage', 'billing_item_taxes.sgst_percentage',
                        //                     'billing_item_taxes.tax_amount', 'billing_item_taxes.sgst_amount','billing_item_taxes.grand_total', 'billing_item_taxes.cgst_amount',
                        //                     )
                        //                     ->join('billing_items', 'billing_items.item_id', '=', 'packages.id')
                        //                     ->join('billing_item_taxes', 'billing_item_taxes.bill_item_id', '=', 'billing_items.id')
                        //                     ->where('packages.shop_id', SHOP_ID)
                        //                     ->where('billing_items.billing_id', $id)
                        //                     ->whereIn('packages.id', $ids)
                        //                     ->orderBy('packages.id', 'desc')->get();

                        $billing_items  = BillingItem::select('packages.name',  'packages.hsn_code', 'billing_items.id as id', 'billing_items.billing_id as billingId', 'billing_items.is_discount_used', 
                                            'billing_items.discount_type', 'billing_items.discount_value',
                                            'billing_item_taxes.cgst_percentage', 'billing_item_taxes.sgst_percentage',
                                            'billing_item_taxes.tax_amount', 'billing_item_taxes.sgst_amount','billing_item_taxes.grand_total', 'billing_item_taxes.cgst_amount',
                                            )
                                            ->join('packages', 'packages.id', '=', 'billing_items.item_id')
                                            ->join('billing_item_taxes', 'billing_item_taxes.bill_item_id', '=', 'billing_items.id')                                        
                                            ->where('packages.shop_id', SHOP_ID)
                                            ->where('billing_items.billing_id', $id)
                                            ->whereIn('packages.id', $ids)
                                            ->orderBy('packages.id', 'desc')->get();

                    }
                    $grand_total                =  $billing_items->sum('grand_total');
                } 
                
            
                // echo "<pre>"; print_r($billing); exit;

                return view($this->viewPath . '.invoice-view', compact('page', 'billing', 'billing_items', 'grand_total', 'item_type' ,'variants'));
            }
            abort(404);
        }
        abort(404); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function edit(Billing $billing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Billing $billing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */


     
    public function destroy(Billing $billing)
    {
        if($billing->address_type== "company")
            $billing_addres = BillingAddres::where('bill_id', $billing->id)->delete() ;

        $billing_items      = BillingItem::where('billing_id', $billing->id)->delete() ;
        $billing            = $billing->delete();  

        return ['flagError' => false, 'message' => $this->title. " details deleted successfully"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function cancelBill($billing)
    {
        $billing                = Billing::findOrFail($billing);
        $billing->status = 2;
        $billing->save();
        return ['flagError' => false, 'message' => " Bill cancelled successfully"];
    }
}

