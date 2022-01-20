<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GstTaxPercentage;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Hours;
use App\Models\Shop;
use Auth;

class Package extends Model
{
    use HasFactory;

    // public function services()
    // {
    //     return $this->belongsToMany('App\Models\Year', 'task_year', 'task_id', 'year_id');
    // }

    public function service()
    {
        return $this->belongsToMany('App\Models\Service')->withTimestamps();
    }

    public function additionaltax()
    {
        return $this->belongsToMany('App\Models\Additionaltax');
    }

    public function gsttax()
    {
        return $this->belongsTo('App\Models\GstTaxPercentage', 'gst_tax', 'id');
    }

    public static function getDetails($id)
    {
        $result             = array();
        $minutes            = 0;
        $total_minutes      = 0;
        $lead_before        = 0;
        $lead_after         = 0;
        $package_services   = '';
        $data               = self::find($id);

        if($data) {

            foreach($data->service as $row) {

                $package_services .= $row->name .', '; 
                $minutes += $row->hours->value;

                if($row->lead_before != null) {
                    $minutes        += $row->leadBefore->value;
                    $lead_before    = $row->leadBefore->value;
                }
                    
                if($row->lead_after != null) {
                    $minutes        += $row->leadAfter->value;
                    $lead_after     = $row->leadAfter->value;
                }

                $total_minutes      += $minutes;
                $minutes            = 0;
                $lead_before        = 0;
                $lead_after         = 0;
            }
            
                
            $result = array('full_name' => $data->name, 'package_services' => rtrim($package_services, ', '), 'total_minutes' => $total_minutes, 'lead_before' => $lead_before, 'lead_after' => $lead_after);
            return $result;
        }
        return false;
    }

    public static function getScheduleDetails($item_ids)
    {
        $total_minutes  = 0;
        $service_minutes= 0;
        $total_amount   = 0;
        $service_amount = 0;
        $lead_before    = 0;
        $lead_after     = 0;
        $description    = '';
        $result         = array();
        $store          = Shop::find(Auth::user()->shop_id);

        foreach ($item_ids as $key => $item) {
            $package = self::find($item);
            $description.= $package->name. " - ";

            $data_price     = Package::getPriceAfterTax($item);
            $total_amount   += $data_price;

            foreach($package->service as $row) {

                $service_minutes += $row->hours->value;

                if($row->lead_before != null){
                    $service_minutes    += $row->leadBefore->value;
                    $lead_before        = $row->leadBefore->value;
                }
    
                if($row->lead_after != null){
                    $service_minutes    += $row->leadAfter->value;
                    $lead_after         = $row->leadAfter->value;
                }

                $description    .= $row->name. ' ( ' . ($row->hours->value+$lead_before+$lead_after) . ' mns ), ';
                $total_minutes  = ($total_minutes+$service_minutes);

                $lead_before        = 0;
                $lead_after         = 0;
                $service_minutes    = 0;
               
                if($package->service->last() == $row) {
                    $description = rtrim($description, ', ');
                    $description.= '<br>';
                }
            }
        }
        $description .= "<br> Price : ". number_format($total_amount,2);
        $result = array('total_hours' => $total_minutes, 'description' => $description);
        return $result;
    }

    public static function getPriceAfterTax($id)
    {
        $total_percentage       = 0 ;
        $gross_charge           = 0 ;
        $gross_value            = 0 ;
        $grand_total            = 0 ;

        $data           = self::find($id);
        
        $total_percentage = $data->gsttax->percentage ;                
        if(count($data->additionaltax) > 0){
            foreach($data->additionaltax as $additional){
                $total_percentage = $total_percentage+$additional->percentage;
            } 
        }

        $total_service_tax          = ($data->price/100) * $total_percentage ;        
        $tax_onepercentage          = $total_service_tax/$total_percentage;
        $total_gst_amount           = $tax_onepercentage*$data->gsttax->percentage ;
        $total_cgst_amount          = $tax_onepercentage*($data->gsttax->percentage/2) ;
        $total_sgst_amount          = $tax_onepercentage*($data->gsttax->percentage/2) ;

        if($data->tax_included == 1) {
            $included = 'Tax Included' ;
            $gross_charge   = $data->price ;
            $gross_value    = $data->price - $total_service_tax ;
        }else{
            $included = 'Tax Excluded' ;
            $gross_charge   = $data->price + $total_service_tax  ;
            $gross_value    = $data->price ; 
        }

        return $gross_charge;
    }

    public static function getServiceIds($item_ids)
    {
        $service_ids    = [];
        foreach($item_ids as $key => $item) {
            $data           = self::find($item);
            foreach($data->service as $row) {
                $service_ids[] = $row->id ;
            } 
        }
        return $service_ids;
    }
}
