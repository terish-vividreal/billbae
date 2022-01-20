<?php

namespace App\Http\Controllers;

use Form;
use Illuminate\Http\Request;
use App\Helpers\TaxHelper;
use App\Models\PaymentType;
use App\Models\ShopBilling;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Country;
use App\Models\District;
use App\Models\Billing;
use App\Models\Schedule;
use App\Models\Package;
use App\Models\User;
use App\Models\State;
use Response;
use Session;
use Carbon;
use DB;

use Auth;

class CommonController extends Controller
{

    public function getStates(Request $request)
    {
        $states   = State::where('country_id',$request->country_id)->pluck('name','id');
        $form     = Form::select('state_id', $states , '', ['class' => 'form-control', 'placeholder' => 'Select a state' , 'id'=>'state_id' ]);
        return response($form);
    }

    public function getDistricts(Request $request)
    {
        $districts  = District::where('state_id',$request->state_id)->pluck('name','id');
        $form       = Form::select('district_id', $districts , '', ['class' => 'form-control', 'placeholder' => 'Select a district' , 'id'=>'district_id' ]);
        return response($form);
    }

    public function getShopDistricts(Request $request)
    {
        $districts  = DB::table('shop_districts')->where('state_id',$request->state_id)->pluck('name','id');
        $form       = Form::select('district_id', $districts , '', ['class' => 'form-control', 'placeholder' => 'Select a district' , 'id'=>'district_id' ]);
        return response($form);
    }

    public function getShopStates(Request $request)
    {
        $states     = DB::table('shop_states')->where('country_id',$request->country_id)->pluck('name','id');
        $form       = Form::select('state_id', $states , '', ['class' => 'form-control', 'placeholder' => 'Select a State' , 'id'=>'state_id' ]);
        return response($form);
    }

    public function getTimezone(Request $request)
    {
        $country_code    = DB::table('shop_countries')->where('id',$request->country_id)->value('sortname');
        $timezone        = DB::table('timezone')->where('country_code',$country_code)->get();

        // For html
        // $form           = Form::select('timezone', $timezone , '', ['class' => 'form-control', 'placeholder' => 'Select a timezone' , 'id'=>'timezone' ]);
        // return response($form);

        // Select 2
        if ($timezone)
            return response()->json(['flagError' => false, 'data' => $timezone]);
    }
    
    public function getAllServices(Request $request)
    {
        // if($request->category_id)
        // whereIn('service_category_id', $request->category_id)->

        // $result = Service::with('additionaltax', 'gsttax', 'hours', 'leadBefore', 'leadAfter')
        //                 select('services.*', DB::raw('CONCAT(phonecode , " (", name, ")") AS phone_code'))->where('shop_id', SHOP_ID)

        $services   = Service::leftjoin('hours', 'hours.id', 'services.hours_id')
                    ->select( DB::raw('CONCAT(services.name, " (", hours.name , ")") AS name'), 'services.id')->where('services.shop_id', SHOP_ID)->get();
        if($services)
            return response()->json(['flagError' => false, 'data' => $services]);
    }
    
    public function getAllPackages(Request $request)
    {
        $packages   = Package::select('name','id')->where('shop_id', SHOP_ID)->get();
        if($packages)
            return response()->json(['flagError' => false, 'data' => $packages]);
    }

    public function getStatesOfCountry(Request $request)
    {
        // if($request->country_id == 101){
        //     $errors = array('I am sorry, this service is currently not supported in your selected country. In case you wish to use this service in any country other than India, please leave a message in the contact us page, and we shall respond to you at the earliest.');
        //     return ['flagError' => true, 'message' => "Currently not supported in your selected country!",  'error'=> $errors];
        // }
        $states   = DB::table('shop_states')->where('country_id', $request->country_id)->get();

        if($states)
            return response()->json(['flagError' => false, 'data' => $states]);
        else
            return response()->json(['flagError' => true, 'data' => null]);
    }
    
    public function getDistrictsOfState(Request $request)
    {
        $districts   = DB::table('shop_districts')->where('state_id', $request->state_id)->get();
        if($districts)
            return response()->json(['flagError' => false, 'data' => $districts]);
        else
            return response()->json(['flagError' => true, 'data' => null]);
    }

    public function getAllTherapists()
    {
        $user_id        = Auth::user()->id;
        $data           =  User::role('Staffs')
                            ->leftjoin('staff_profiles', 'staff_profiles.user_id', '=', 'users.id')
                            ->leftjoin('schedule_colors', 'staff_profiles.schedule_color', '=', 'schedule_colors.id')
                            ->where('users.parent_id', $user_id)
                            ->where('users.is_active', '=',  1)
                            ->whereIn('staff_profiles.designation', [1, 2])
                            ->where('users.is_active', '!=',  2)->get(['users.id', 'users.name as title', 'schedule_colors.name as eventColor']);


        if($data)
            return response()->json(['flagError' => false, 'data' => $data]);
        else
            return response()->json(['flagError' => true, 'data' => null]);
    }

    public function getTherapist($id)
    {
        $data           =  User::find($id);
        if($data)
            return response()->json(['flagError' => false, 'therapist' => $data]);
        else
            return response()->json(['flagError' => true, 'data' => null]);
    }

    public function getCurrencies(Request $request)
    {
        $currencies     = DB::table('currencies')->where('country_id', $request->country_id)->get();
        if($currencies)
            return response()->json(['flagError' => false, 'data' => $currencies]);
        else
            return response()->json(['flagError' => true, 'data' => null]);
    }
    
    public function getServices(Request $request)
    {
        $query   = Service::with('hours')->where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        if($request->data_ids)
            $query      = $query->whereIn('id', $request->data_ids);

        $services       = $query->get();   

        if($services)
            return response()->json(['flagError' => false, 'data' => $services, 'totalPrice' => $services->sum('price')]);
    }
    
    public function getPackages(Request $request)
    {
        $query   = Package::where('shop_id', SHOP_ID)->orderBy('id', 'desc');

        if($request->data_ids)
            $query   = $query->whereIn('id', $request->data_ids);

        $packages   = $query->get();   

        if($packages)
            return response()->json(['flagError' => false, 'data' => $packages, 'totalPrice' => $packages->sum('price')]);
    }

    public function getCustomerDetails(Request $request)
    {
        $customer   = Customer::where('id', $request->customer_id)->where('shop_id', SHOP_ID)->first();
        if($customer)
            return response()->json(['flagError' => false, 'data'=> $customer]);
        else
          return response()->json(['flagError' => true,'message'=>'Customer not fount']);
    }
    
    public function getPaymentTypes(Request $request)
    {
        $tableHtml      = '';
        $paymentTypes   = PaymentType::whereIn('shop_id', [0, SHOP_ID])->get();

        if ($paymentTypes) {
            foreach($paymentTypes as $row) {
                $tableHtml.=    '<tr id="row'.$row->id.'" data-name="'.$row->name.'" data-id="'.$row->id.'" data-shop-id="'.$row->shop_id.'"><td><p class="mb-1"><label><input type="checkbox" class="payment-types" name="payment_types[]" data-type="'.$row->id.'" id="payment_types_'.$row->id.'"  value="'.$row->id.'"><span></span></label></td>';
                $tableHtml.=    '<td>'.$row->name.'</td>';
                $tableHtml.=    '<td><a href="javascript:" class="payment-types-btn-edit"><i class="material-icons yellow-text">edit</i></a> <a href="javascript:" id="'.$row->id.'" data-shop_id="'.$row->shop_id.'" class="deletePaymentTypes"><i class="material-icons pink-text">clear</i></a></td></tr>';
            }
            return response()->json(['flagError' => false, 'data'=> $paymentTypes, 'html' => $tableHtml]);
        } else {
            return response()->json(['flagError' => true,'message'=>'Payment Types not fount']);
        }
          
    }

    public function calculateTax(Request $request)
    {
        
        $type = $request->type;
        if ($type == 'services') {
            $result = Service::with('additionaltax')->where('shop_id', SHOP_ID)->whereIn('id', $request->data_ids)->orderBy('id', 'desc')->get();
        } else {
            $result = Package::with('additionaltax')->where('shop_id', SHOP_ID)->whereIn('id', $request->data_ids)->orderBy('id', 'desc')->get();
        }

        if($result){

            $html                   = '';
            $index                  = 1 ;
            $total_percentage       = 0 ;
            $total_service_tax      = 0 ;
            $gross_charge           = 0 ;
            $gross_value            = 0 ;
            $grand_total            = 0 ;
            $additional_tax_array   = array();

            foreach($result as $row){

                // $tax_data       = TaxHelper::simpleTaxCalculation($row);
                $total_percentage = $row->gst_tax ;                
                if(count($row->additionaltax) > 0){
                    foreach($row->additionaltax as $additional){
                        $total_percentage = $total_percentage+$additional->percentage;
                    } 
                }

                $total_service_tax          = ($row->price/100) * $total_percentage ;        
                $tax_onepercentage          = $total_service_tax/$total_percentage;
                $total_gst_amount           = $tax_onepercentage*$row->gst_tax ;
                $total_cgst_amount          = $tax_onepercentage*($row->gst_tax/2) ;
                $total_sgst_amount          = $tax_onepercentage*($row->gst_tax/2) ;

                if($row->tax_included == 1) {
                    $included = 'Tax Included' ;
                    $gross_charge   = $row->price ;
                    $gross_value    = $row->price - $total_service_tax ;
                }else{
                    $included = 'Tax Excluded' ;
                    $gross_charge   = $row->price + $total_service_tax  ;
                    $gross_value    = $row->price ;
                }


                $html.='<tr id="'.$index.'"><td>'.$index.'</td>';
                $html.='<td>'.$row->name.  '( '.$included.' ) </td><td>'.$row->hsn_code.'</td>';
                $html.='<td class="right-align">';

                $html.='<ul><li class="display-flex justify-content-between"><span class="invoice-subtotal-title">Amount </span><h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($gross_value,2).'</h6></li>';
                
                $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">'.($row->gsttax->percentage/2).' % CGST </span>';
                $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($total_cgst_amount,2).'</h6></li>';

                $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">'.($row->gsttax->percentage/2).' % SGST</span>';
                $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($total_sgst_amount,2).'</h6></li>';

                if(count($row->additionaltax) > 0){
                    $html.='<li class="divider mt-2 mb-2"></li>';
                    foreach($row->additionaltax as $additional){
                        $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">' . $additional->percentage . ' % ' . $additional->name. '</span>';
                        $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($tax_onepercentage*$additional->percentage,2).'</h6></li>';
                    }
                }

                $html.='<li class="divider mt-2 mb-2"></li>';

                $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">Total</span><h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($gross_charge,2).'</h6></li>';

                    // $html.='<tr data-widget="expandable-table" aria-expanded="true"><td>'.$index.'</td><td>'.$row->name.' - SAC Code : '.$row->hsn_code.' ( '.$included.' )</td><td> '.number_format($gross_value,2).'</td></tr>';
                    // $html.='<tr class="expandable-body"> </tr>';
                    // $html.='<tr class="expandable-body" style="text-align:center;">';
                    // $html.='<td colspan="2">';
                    // $html.='<div id="cgst"> <span> '.($row->gst_tax/2).' % CGST -  </span> '.number_format($total_cgst_amount,2).' </div>';
                    // $html.='<div id="sgst"> <span> '.($row->gst_tax/2).' % SGST -  </span> '.number_format($total_sgst_amount,2).' </div>';
                    // if(count($row->additionaltax) > 0){
                    //     foreach($row->additionaltax as $additional){
                    //         $html.='<div id="additionalTax" class="test gst"> <span>  ' . $additional->percentage . ' % ' . $additional->name. '  </span> - '.number_format($tax_onepercentage*$additional->percentage,2). '</div>';
                    //     }
                    // }
                    // $html.='</td></tr>';
                    // $html.='<tr data-widget="expandable-table" aria-expanded="false"><td colspan="2">';
                    // $html.='<div style="text-align:right;"><b>Total </b><br></td><td><b>'.number_format($gross_charge,2).'</b></div></td></tr>';

                $grand_total = ($grand_total + $gross_charge); ;
                $index++;
            }
            
            // $table_footer='<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td><b>Total - <h4>₹ '.$total_tax.'</h4></b></td><td><b>Total - <h4>₹ '.number_format($total_amount,2).'</h4></b></td></tr></tfoot>';
        }

        return response()->json(['flagError' => false, 'grand_total' => $grand_total, 'html' => $html]);
    }
    
    public function calculateTaxTable(Request $request)
    {
        $store_data     = ShopBilling::where('shop_id', SHOP_ID)->first();
        $data_array     = array();
        $type           = $request->type;
        if ($type == 'services') {
            $result = Service::with('additionaltax', 'gsttax', 'hours', 'leadBefore', 'leadAfter')->where('shop_id', SHOP_ID)
                        ->whereIn('services.id', $request->data_ids)->orderBy('services.id', 'desc')->get();
        } else {
            $result = Package::with('service','additionaltax', 'gsttax')->where('shop_id', SHOP_ID)
                        ->whereIn('packages.id', $request->data_ids)->orderBy('packages.id', 'desc')->get();
        }


        if ($result) {
            $html                   = '';
            $index                  = 1 ;
            $total_percentage       = 0 ;
            $total_service_tax      = 0 ;
            $gross_charge           = 0 ;
            $gross_value            = 0 ;
            $grand_total            = 0 ;
            $total_minutes          = 0;
            $additional_tax_array   = array();
            $additional_amount      = 0;
            $package_full_name      = '';

            foreach ($result as $row) {
                $minutes        = 0;
                $lead_before    = 0;
                $lead_after     = 0;

                if ($type == 'services') {

                    $service_time       = Service::getDetails($row->id);
                    $data_array[]       = array('name' => $row->name, 'price' => $row->price, 'minutes' => $service_time['total_minutes']);

                } else {
                    foreach ($row->service as $service_row) {
                        $service_ids[]      = $service_row->id;
                    }   

                    $service_details        = Package::getDetails($row->id);
                    $package_full_name      = $row->name. ' (' .$service_details['package_services'].')';
                    $data_array[]           = array('name' => $package_full_name, 'price' => $row->price, 'minutes' => $service_details['total_minutes']);
                }

                // Case 1 - Store has no GST
                if ($store_data->gst_percentage != null) {
                                
                    if ($row->gst_tax != NULL) {
                        $total_percentage           = $row->gsttax->percentage ;
                        $tax_percentage             = $row->gsttax->percentage ;
                    } else {
                        $total_percentage           = $store_data->GSTTaxPercentage->percentage ;
                        $tax_percentage             = $store_data->GSTTaxPercentage->percentage ;
                    }
                } 
                
                $included = ($row->tax_included == 1)?'Tax Included':'Tax Excluded';
                $html.='<tr id="'.$index.'"><td>'.$index.'</td>';
                if ($type == 'services') {
                    $html.='<td>'.$row->name . ' - '. $row->hours->value . ' mns. ' . ' ( '.$included.' ) </td><td>'.$row->hsn_code.'</td>';
                } else {
                    $html.='<td>'.$package_full_name . ' - '. $service_details['total_minutes'] . ' mns. ' . ' ( '.$included.' ) </td><td>'.$row->hsn_code.'</td>';
                }
                    $html.='<td class="right-align">';
                    
                if ($total_percentage > 0) {
                    
                    $total_service_tax          = ($row->price/100) * $total_percentage ;        
                    $tax_onepercentage          = $total_service_tax/$total_percentage;
                    $total_gst_amount           = $tax_onepercentage*$total_percentage ;
                    $total_cgst_amount          = $tax_onepercentage*($total_percentage/2) ;
                    $total_sgst_amount          = $tax_onepercentage*($total_percentage/2) ;

                    if (count($row->additionaltax) > 0) {
                        foreach ($row->additionaltax as $additional) {
                            $total_percentage = $total_percentage+$additional->percentage;
                            $additional_amount = $tax_onepercentage*$additional->percentage;
                        } 
                    }

                    // gross_charge - total
                    // gross_value - amount

                    if ($row->tax_included == 1) {
                        $gross_charge           = $row->price;
                        $gross_value            = ( $row->price - $total_service_tax ) - $additional_amount;
                        $gross_value            = $gross_value - $additional_amount;
                        
                    } else {
                        $gross_charge           = $row->price + $total_service_tax  ;
                        $gross_value            = $row->price ;
                        $gross_charge           = $gross_charge + $additional_amount;
                        // echo $additional_amount . '--' . $gross_value; exit;
                    }

                    $html.='<ul><li class="display-flex justify-content-between"><span class="invoice-subtotal-title">Amount </span><h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($gross_value,2).'</h6></li>';
                  
                    $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">'.($tax_percentage/2).' % CGST </span>';
                    $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($total_cgst_amount,2).'</h6></li>';

                    $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">'.($tax_percentage/2).' % SGST</span>';
                    $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($total_sgst_amount,2).'</h6></li>';

                    // echo $gross_charge; exit;
                    if (count($row->additionaltax) > 0) {
                        $html.='<li class="divider mt-2 mb-2"></li>';
                        foreach($row->additionaltax as $additional) {
                            $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">' . $additional->percentage . ' % ' . $additional->name. '</span>';
                            $html.='<h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($tax_onepercentage*$additional->percentage,2).'</h6></li>';
                            
                            // if ($row->tax_included == 1) {
                            //     $gross_charge           = $gross_value - $additional_amount ;
                            // } else {
                            //     $gross_charge           = $gross_charge + $additional_amount  ;
                            // }
                        }
                    }

                } else {
                    if ($row->tax_included == 1) {
                        $gross_charge           = $row->price ;
                        $gross_value            = $row->price - $total_service_tax ;
                    } else {
                        $gross_charge           = $row->price + $total_service_tax  ;
                        $gross_value            = $row->price ;
                    }
                }

                
                    $html.='<li class="divider mt-2 mb-2"></li>';
                    $html.='<li class="display-flex justify-content-between"><span class="invoice-subtotal-title">Total</span><h6 class="invoice-subtotal-value indigo-text">₹ '.number_format($gross_charge,2).'</h6></li>';
                    // $html.='<tr><td>'.$index.'</td><td>'.$row->name.' - SAC Code : '.$row->hsn_code.' ( '.$included.' )</td><td> '.number_format($gross_value,2).'</td></tr>';
                    // $html.='<tr><td></td><td><span> '.($row->gsttax->percentage/2).' % CGSTaaaa -  </span></td><td> '.number_format($total_cgst_amount,2).'</td></tr>';
                    // $html.='<tr><td></td><td><span> '.($row->gsttax->percentage/2).' % SGST -  </span></td><td> '.number_format($total_sgst_amount,2).'</td></tr>';
                    // if(count($row->additionaltax) > 0){
                    //     foreach($row->additionaltax as $additional){
                    //         $html.='<tr><td></td><td><span>  ' . $additional->percentage . ' % ' . $additional->name. '  </span></td><td> '.number_format($tax_onepercentage*$additional->percentage,2).'</td></tr>';
                    //     }
                    // }
                    // $html.='<tr data-widget="expandable-table" aria-expanded="false"><td></td>';
                    // $html.='<td style="text-align:right;"><b>Total</b></td><td><b>'.number_format($gross_charge,2).'</b></td></tr>';
                $grand_total = ($grand_total + $gross_charge); 
                $index++;
            }
            // $table_footer='<tfoot><tr><td></td><td></td><td></td><td></td><td></td><td><b>Total - <h4>₹ '.$total_tax.'</h4></b></td><td><b>Total - <h4>₹ '.number_format($total_amount,2).'</h4></b></td></tr></tfoot>';
        }
        return response()->json(['flagError' => false, 'grand_total' => $grand_total, 'html' => $html, 'data' => $data_array, 'total_minutes' => $total_minutes]);
    }

    public function getBillingReports(Request $request) 
    {
        $total_bill_value = 0;
        $total_sale_value = 0;

        if (empty($request->start)) {
            // Return todays sales amount
            $billing_array          = Schedule::leftjoin('billings', 'billings.id', '=', 'schedules.billing_id')
                                            ->whereDate('schedules.start', Carbon\Carbon::today())->where('schedules.shop_id', SHOP_ID)->get();
            $sales_array            = Schedule::leftjoin('billings', 'billings.id', '=', 'schedules.billing_id')
                                            ->whereDate('schedules.start', Carbon\Carbon::today())->where('billings.payment_status', 1)->where('billings.shop_id', SHOP_ID)->get();
        }
        foreach ($billing_array as $row) {
            $total_bill_value +=$row->amount ;
        }

        foreach ($sales_array as $row) {
            $total_sale_value +=$row->amount ;
        }
        return response()->json(['flagError' => false, 'total_bookings' => count($billing_array), 'booking_amount' => $total_bill_value, 'total_sales' => count($sales_array), 'sales_amount' => $total_sale_value]); 
    }
    

    // public function getSubjects($curriculum_id)
    // {
    //     $subjects   = Subject::Join('subject_curriculum','subjects.id','subject_curriculum.subject_id')
    //                 ->where('subject_curriculum.curriculum_id',$curriculum_id)->pluck('subjects.name','subjects.id');
    //     $form       = Form::select('subject_id', $subjects , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Subject' , 'id'=>'subject_id' ]);
    //     return response($form);
    // }

    // public function getChapters($topic_id)
    // {

    //     $chapters     = Chapter::where('topic_id',$topic_id)->pluck('name','id');
    //     $form         = Form::select('chapter_id', $chapters , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Chapter' , 'id'=>'chapter_id' ]);

    //     return response($form);
    // }

    // public function getTopics(Request $request)
    // {
    //     $year  = $request->year;
    //     $paper = $request->paper;
    //     $topics   = Topic::where('curriculum_id',$request->curriculum)
    //                 ->where('subject_id',$request->subject)
    //                /* ->where(function($query) use ($year,$paper)  {
    //                    if($year>0) {
    //                       $query->where('year_id', $year);
    //                     }else{
    //                         $query->where('paper_id', $paper);
    //                     }
    //                  })*/
    //                 ->pluck('name','id');
                   
    //     $form       = Form::select('topic_id', $topics , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Topic' , 'id'=>'topic_id' ]);
    //     return response($form);
    //     
    // }

    // public function getUnits($chapter_id)
    // {
    //     $units   = Unit::where('chapter_id',$chapter_id)
    //                     ->pluck('name','id');
    //     $form       = Form::select('unit_id', $units , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Unit' , 'id'=>'unit_id' ]);
    //     return response($form);
    // }
    // public function getClasses($year_id)
    // {
    //     $user_id        = Auth::user()->id; 
    //     $classes   = Classes::where('year_id',$year_id)->where('teacher_id',$user_id)->pluck('name','id');
    //     $form         = Form::select('class_id', $classes , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Class' , 'id'=>'class_id' ]);

    //     return response($form);
    // }

    // public function getClassesByUser(Request $request)
    // {
    //     $year_id        = $request->year_id;
    //     $teacher_id     = $request->teacher_id;

    //     if($year_id != null)
    //         $class_query    = Classes::where('year_id',$year_id);

    //     if($teacher_id)
    //         $class_query    = $class_query->where('teacher_id',$teacher_id);


    //     $classes    = $class_query->pluck('name','id');
    //     $form         = Form::select('class_id', $classes , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Class' , 'id'=>'class_id' ]);

    //     return response($form);
    // }

    // public function getYears($curriculam_id)
    // {
    //     $component   = Curriculum::where('id',$curriculam_id)->first()->component_type;
    //     $ctype       = [];
    //     $ph          = '';
    //     if($component == 'paper')
    //     {
    //         $ph      = 'Select Paper';
    //         $ctype   = Paper::pluck('paper','id');
    //     }
    //     else if($component == 'year')
    //     {
    //         $ph      = 'Select Year';
    //         $ctype   = Year::pluck('year','id');
    //     }

    //     $form         = Form::select('year_id', $ctype , '', ['class' => 'form-control new-drop-section', 'placeholder' => $ph , 'id'=>'year_id' ]);

    //     return response($form);
    // }
    //  public function getStudents($class_id)
    // {
    //     $students     = Student::where('class_id',$class_id)->pluck('fname','id');
    //     $form         = Form::select('student_id', $students , '', ['class' => 'form-control new-drop-section','multiple'=>'multiple', 'placeholder' => 'Select Students' ,'name'=>'student_id[]', 'id'=>'student_id' ]);

    //     return response($form);
    // }

    // public function getRegions($country_id)
    // {
     
    //     $region =  Region::where('country_id',$country_id)->pluck('name','id');
    //     $form         = Form::select('region_id', $region , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select Region' , 'id'=>'region_id' ]);
    //     return response($form);
    // }
    // public function getSchool($region_id)
    // {
    //     $school =  School::where('region_id',$region_id)->pluck('name','id');
    //     $form         = Form::select('school_id', $school , '', ['class' => 'form-control new-drop-section', 'placeholder' => 'Select School' , 'id'=>'school_id' ]);
    //     return response($form);
    // }

    // public function getSyllubus( Request $request){

    //     $topicsSet   = Topic::select('id',DB::raw("CONCAT(topic_code,': ',name) AS topic"),'paper_id')
    //              ->where('country_id', $request->country_id)
    //              ->where('curriculum_id', $request->curriculum_id)
    //              ->where('subject_id', $request->subject_id);

    //      if($request->search )  {
    //         $topicsSet->where('topics.name', 'like', "%{$request->search}%"); 
    //         $topicsSet->orWhere('topics.topic_code', 'like', "%{$request->search}%"); 
    //      }      
    //     $topicsSet = $topicsSet->get()->toArray();
    //     $topicGroup   =  Helper::_array_group($topicsSet,'paper_id') ;
    //     if(!$topicGroup){
    //         return response("<p> No syllabus found </p>");    
    //     }
          
    //     $html = '';
    //     foreach ($topicGroup as $key => $topics) {
    //         $html.= '<div class="col-md-6">';
    //         foreach ($topics as $key => $topic) {
               
    //              $html.='<div class="scroll-content" id="topic-'.$topic['id'].'">';
    //              $html.='<div class="subject-detail-box-section"><div class="subject-detail-box-section-topic"><h2>'.$topic['topic'].'</h2></div>';

    //              $chapters   = Chapter::select('chapters.id as chapter_id',
    //                            DB::raw("CONCAT(chapter_code,': ',chapters.name) AS chapter"))
    //                            ->where('chapters.topic_id', $topic['id']);
    //             // if($request->search){
    //             //      $chapters->where('chapters.name', 'like', "%{$request->search}%"); 
    //             // }                
                
    //            $chapters = $chapters->get() ;
                               
    //              foreach ($chapters as $key => $chapter) {
    //                     $units = Unit::select('id as unit_id','name') 
    //                             ->where('chapter_id',$chapter->chapter_id)
    //                             ->get();
    //                     $html.='<div class="listed-section">';
    //                     $html.=' <h3>'.$chapter->chapter.'</h3>';

    //                     foreach ($units as $key => $unit) {      
    //                         $html.=' <p><a class="" href="javascript:void(0);" onclick="createQuestion('.$unit->unit_id.')">'.$unit->name.'</a></p>'; 
    //                     } 
    //                     $html.='</div><div class="line-strip"></div>';  

    //             }  

    //             $html.= '</div></div>';

    //         }
    //         $html.= '</div>';

            
    //     }


        
    //     return response($html);    
                      
    // }

    // public function getcurriculums($country_id)
    // {
        
    //     $curriculum   = Curriculum::where('country_id',$country_id)->select('id','name')->get();
    //     $html = '';
    //     foreach ($curriculum as $key => $data) {
    //        $html.='<button type="button" class="btn btn-info font-weight-bold btn-md mr-2 btn-cur btn-cur-'.$data->id.'" onclick="setCurriculum(this.id)" id="'. $data->id .'">'. $data->name .'</button>';
    //     }
    //     return response($html);
    // }

    // public function getTopicCodes(Request $request){

    //     $topics   = Topic::where('country_id',$request->country_id)
    //                 ->where('curriculum_id',$request->curriculum_id)
    //                 ->where('subject_id',$request->subject_id)
    //                 ->select('id','topic_code')
    //                 ->get();
    //      $html ='';           
    //     foreach ($topics as $key => $topic) {
    //        $html.='<button type="button" class="btn btn-success font-weight-bold btn-md mr-2 btn-top btn-top-'.$topic->id.'" onclick="gotoTopic(this.id)" id="'. $topic->id .'">'. $topic->topic_code .'</button>';
    //     }
    //     return response($html);           
    // }

    // public function getClassButtons($year_id)
    // {
    //     $user_id        = Auth::user()->id; 
    //     $claasess   = Classes::where('year_id',$year_id)->select('id','name')->where('teacher_id',$user_id)->get();
    //     $html = '';
    //     foreach ($claasess as $key => $data) {
    //        $html.='<button type="button" class="btn btn-info font-weight-bold btn-md mr-2 btn-cur btn-cur-'.$data->id.'" onclick="setClass(this.id)" id="'. $data->id .'">'.$data->name .'</button>';
    //     }
    //     return response($html);
    // }
    // // public function clearFilter()
    // // {
    // //     session()->forget(['country','curriculum','subject','year','topic','chapter','unit','paper']);
    // //     $message = "session cleared";
    // //     return response($message);
    // // }

    // public function ConfirmUser($id)
    // {
    //    // $user      = Token::where('token_code',$id)->first();
    //    // $user_id   = $user->user_id;
    //     $user_type = User::where('id',$id)->first();
    //     $user_type->is_verified = 1;
    //     $user_type->save();
        
    //     return redirect('/login');

    // }

    // public function ForgotPasswordUser($id)
    // {
    //     $user = Token::where('token_code',$id)->first();
    //     $user_id = $user->user_id;
    //     $user_type = User::where('id',$user_id)->first();

    //   return view('admin.setting.resetPassword',compact('user_id'));

    // }

    // public function setPassword(Request $request)
    // {

    //     $user = Token::where('token_code',$request->userId)->first();
       
    //     $user_type = User::where('id',$request->userId)->first();
    //     $user_type->password = Hash::make($request->password);
       
    //     if($user_type->save()){
    //         if($user_type->role == 'school'){
    //             $school = School::where('user_id',$request->userId)->first();
    //             $school->is_confirmed = 1;
    //             $school->save();
    //             $delete = Token::where('user_id',$request->userId)->delete();
    //         }elseif($user_type->role == 'student'){
    //             $student = Student::where('user_id',$request->userId)->first();
    //             $student->is_confirmed = 1;
    //             $student->save();
    //             $delete = Token::where('user_id',$request->userId)->delete();
    //         }
        
    //    }

}
