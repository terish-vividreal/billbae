<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Helpers\TaxHelper;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Country;
use App\Models\BillingAddres;
use App\Models\State;
use App\Models\District;
use App\Models\Service;
use App\Models\ServiceCategory;
use DataTables;
use Validator;
use Auth;
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

        // echo print_r($tax); 
        // exit;
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
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;
        
        if($request->billing_address_checkbox == 1)
        {
            $address                    =  new BillingAddres();
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

        if($request->service_type == 1)
        {     
            $variants->services = Service::select("*")->whereIn('id', $request->bill_item)->get();
            echo "<pre>"; print_r($variants->services); exit;
        }

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
