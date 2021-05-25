<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\ServiceCategory;
use App\Helpers\TaxHelper;
use Illuminate\Http\Request;
use App\Models\ShopBilling;
use App\Models\Package;
use App\Models\Country;
use App\Models\BillingAddres;
use App\Models\BillingItem;
use App\Models\District;
use App\Models\Service;
use App\Models\Shop;
use DataTables;
use Validator;
use Auth;
use App\Helpers\FunctionHelper;
use App\Models\Customer;
use App\Models\Billing;

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
        

        // $tax = TaxHelper::getGstincluded(1000, 18, 9, 9);
        $amount             = 5000;
        $gst_percent        = 18; 
        $flood_percent      = 2; 
        $flood_percent_2    = 1; 

        $total_percentage   = 21;
        $total_tax          = ($amount/100) * $total_percentage ; 

        $onepercentage      = $total_tax/$total_percentage;

        $gst_amount         = $onepercentage*$gst_percent ;
        
        $cgst = $gst_amount/2 ;

        return view($this->viewPath . '.create', compact('page', 'variants'));
    }

    /**
     * Return amount after discount
     *
     * @return \Illuminate\Http\Response
     */
    public function manageDiscount(Request $request) 
    {

        // print_r($request->all()); exit; 

        $billing_item       = BillingItem::findOrFail($request->billing_id);

            if($request->discount_action === 'add')
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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // echo "<pre>"; print_r($request->all()); exit ;
        $billing                    = new Billing();
        $billing->shop_id           = SHOP_ID;
        $billing->customer_id       = $request->customer_id;
        $billing->amount            = $request->grand_total;
        $billing->payment_status    = 0 ;
        $billing->billing_code      = FunctionHelper::generateCode(8, 'BB', SHOP_ID.Auth::user()->id);
        $billing->address_type      = ($request->billing_address_checkbox == 0) ? 'company' : 'customer' ;
        $billing->save();
        
        
        if($request->billing_address_checkbox == 0)
        {
            $address                    = new BillingAddres();
            $address->shop_id           = SHOP_ID;
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
                $item->item_type        = ($request->service_type == 1) ? 'services' : 'packages' ;
                $item->item_id          = $row ;
                $item->save();
            }       
        }
        return redirect($this->route.'/invoice/'.$billing->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function invoice(Request $request, $id)
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity; 
        $user                   = Auth::user();
        $billing                = Billing::findOrFail($id);
        $variants->store        = Shop::with('billing')->select('shops.*', 'shop_states.name as state', 'shop_districts.name as district')
                                    ->leftjoin('shop_states', 'shop_states.id', '=', 'shops.state_id')
                                    ->leftjoin('shop_districts', 'shop_districts.id', '=', 'shops.district_id')
                                    ->find($user->shop_id); 

                                    
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
                                    ->whereIn('services.id', $request->item_ids)->orderBy('services.id', 'desc')->get();
            }
            else            
            {
                $billing_items = Package::where('shop_id', SHOP_ID)->whereIn('id', $request->item_ids)->orderBy('id', 'desc')->get();
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
        return ['flagError' => false, 'grand_total' => number_format($grand_total,2), 'html' => $invoice_details];
        // return response($questionHtml);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function show(Billing $billing)
    {
        //
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
        //
    }
}
