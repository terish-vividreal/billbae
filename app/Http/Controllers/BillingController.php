<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\ServiceCategory;
use App\Helpers\TaxHelper;
use Illuminate\Http\Request;
use App\Models\ShopBilling;
use App\Models\BillingItemAdditionalTax;
use App\Models\Package;
use App\Models\Country;
use App\Models\BillingAddres;
use App\Models\BillingItemTax;
use App\Models\BillingItem;
use App\Models\District;
use App\Models\PaymentType;
use App\Models\BillAmount;
use App\Models\State;
use App\Models\Service;
use App\Models\Shop;
use DataTables;
use Validator;
use Auth;
use Carbon;
use App\Helpers\FunctionHelper;
use App\Models\Customer;
use App\Models\Billing;
use PDF;
use Event;
use App\Events\SalesCompleted;

class BillingController extends Controller
{
    protected $title    = 'Billing';
    protected $viewPath = 'billing';
    protected $link     = 'billings';
    protected $route    = 'billings';
    protected $entity   = 'Billing';

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
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity; 
        
        $variants->country      = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->services     = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->packages     = Package::where('shop_id', SHOP_ID)->pluck('name', 'id');   
        
        return view($this->viewPath . '.create', compact('page', 'variants'));
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
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/invoice/' . $detail->id ) . '"" class="btn btn-info btn-sm btn-icon mr-2" title="Update Payment"> <i class="fa fa-inr" aria-hidden="true"></i> Update Payment</a>';
                }
                    
                
                if($detail->status == 0){
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/invoice/edit/' . $detail->id) . '"" class="btn btn-primary btn-sm btn-icon mr-2" title="Edit details"> <i class="icon-1x fas fa-pencil-alt"></i> Edit details</a>';
                    $action .= '<a href="javascript:void(0);" id="' . $detail->id . '" onclick="deleteBill(this.id)"  class="btn btn-danger btn-sm btn-icon mr-2" title="Delete"> <i class="icon-1x fas fa-trash-alt"></i></a>';
                }else{
                    $action .= ' <a href="' . url(ROUTE_PREFIX.'/'. $this->route . '/show/' . $detail->id) . '"" class="btn btn-secondary btn-sm btn-icon mr-2" title="View details"> <i class="icon-1x fas fa-eye"></i> View details</a>';
                    $action .= ' <a href="javascript:void(0);" id="' . $detail->id . '" onclick="cancelBill(this.id)" class="btn btn-warning btn-sm btn-icon mr-2" title="Cancel"> <i class="fa fa-ban"></i> Cancel </a>';
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
                    $status = '<span class="badge badge-warning">Pending</span>';
                }else{  
                    $status = '<span class="badge badge-success">Paid</span>';                                
                }
                return $status;
            })
            ->addColumn('updated_date', function($detail){
                $updated_at     = Carbon\Carbon::parse($detail->updated_at);
                $updated_date = $updated_at->format('d-M-Y h:i:s a');
                if($detail->payment_status == 1){
                    return $updated_date;
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

        // print_r($request->all()); exit; 

        $billing_item       = BillingItem::findOrFail($request->billing_item_id);
        // echo "<pre>"; print_r($billing_item); exit; 
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
        // echo "<pre>"; print_r($request->all());  exit ;

        


        $billing                    = new Billing();
        $billing->shop_id           = SHOP_ID;
        $billing->customer_id       = $request->customer_id;
        $billing->customer_type     = Customer::isExisting($request->customer_id);        
        $billing->amount            = $request->grand_total;
        $billing->billed_date       = date('Y-m-d', strtotime($request->billed_date));
        $billing->checkin_time      = $request->checkin_time;
        $billing->checkout_time     = $request->checkout_time;
        $billing->payment_status    = 0 ;
        $billing->billing_code      = FunctionHelper::generateCode(8, 'BB', SHOP_ID.Auth::user()->id);
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

        // echo "<pre>"; print_r($request->all()); exit;

        foreach($request->input('payment_amount') as $key => $value) {
            $data["payment_amount.{$key}"] = 'required';
        }

        $messages = [
            'required' => 'Please enter amount'
        ];

        $validator = Validator::make($request->all(), $data, $messages);

        if ($validator->passes()) {
            if($request->grand_total == array_sum($request->payment_amount))
            {
                
                $billing                    = Billing::findOrFail($request->billing_id);
                $billing->payment_status    = 1;
                $billing->amount            = $request->grand_total;
                $billing->status            = 1;
                $billing->save();
                
                // store payment type details
                foreach($request->input('payment_amount') as $key => $value) {
                    $bill_amount                = new BillAmount();
                    $bill_amount->bill_id       = $billing->id;
                    $bill_amount->payment_type  = $request->payment_type[$key];
                    $bill_amount->amount        = $request->payment_amount[$key];
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
                                            ->join('billing_items', 'billing_items.item_id', '=', 'packages.id')->where('packages.shop_id', SHOP_ID)->where('billing_items.billing_id', $request->bill_id)->whereIn('packages.id', $item_ids)->orderBy('packages.id', 'desc')->get();
                    }
        
                    $discount = array();
                    foreach($billing_items as $key => $row){

                        $tax_array                      = TaxHelper::simpleTaxCalculation($row);

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
                
                Event::dispatch(new SalesCompleted($request->billing_id));
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
        // echo "<pre>"; print_r($request->all());  
        // echo date('Y-m-d', strtotime($request->billed_date));
        // exit ;

        $billing                    = Billing::findOrFail($id);
        $billing->customer_id       = $request->customer_id;
        $billing->amount            = $request->grand_total;
        $billing->billed_date       = date('Y-m-d', strtotime($request->billed_date));
        $billing->checkin_time      = $request->checkin_time;
        $billing->checkout_time     = $request->checkout_time;

        $address                    = BillingAddres::where('bill_id', $id)->where('customer_id', $request->customer_id)->first();
        
        if($request->billing_address_checkbox == 0)
        {
            if($address){
                // echo "update";
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
                    $variants->billing_items = Service::where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
                }else{
                    $variants->billing_items = Package::where('shop_id', SHOP_ID)->whereIn('id', $ids)->orderBy('id', 'desc')->get();
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
        $variants->country      = Country::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->services     = Service::where('shop_id', SHOP_ID)->pluck('name', 'id');          
        $variants->packages     = Package::where('shop_id', SHOP_ID)->pluck('name', 'id'); 

        $billing                = Billing::findOrFail($id);
        if($billing->status === 0){

            $user                   = Auth::user();
            $billing                = Billing::findOrFail($id);
            $variants->store        = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                        ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                        ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                        ->find($user->shop_id); 

            if(isset($billing->customer->billingaddress->country_id )){
                $variants->states        = State::where('shop_id', SHOP_ID)->where('country_id', $billing->customer->billingaddress->country_id)->pluck('name', 'id');     
            }
            if(isset($billing->customer->billingaddress->state_id )){
                $variants->districts     = District::where('shop_id', SHOP_ID)->where('state_id', $billing->customer->billingaddress->state_id)->pluck('name', 'id');     
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
                    }
                    $grand_total                =  $billing_items->sum('grand_total');
                } 
                
            
                // echo "<pre>"; print_r($billing_items); exit;

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
