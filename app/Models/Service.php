<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceCategory;
use App\Models\GstTaxPercentage;
use App\Models\Shop;
use App\Models\Hours;
use Auth;

class Service extends Model
{

    use HasFactory;

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function hours()
    {
        return $this->belongsTo(Hours::class);
    }

    public function leadBefore()
    {
        return $this->belongsTo(Hours::class, 'lead_before', 'id');
    }

    public function leadAfter()
    {
        return $this->belongsTo(Hours::class, 'lead_after', 'id');
    }

    public function package()
    {
        return $this->belongsToMany('App\Models\Package');
    }

    public function additionaltax()
    {
        return $this->belongsToMany('App\Models\Additionaltax');
    }
    
    public function gsttax()
    {
        return $this->belongsTo('App\Models\GstTaxPercentage', 'gst_tax', 'id');
    }

    /**
     * Get the options for generating the slug.
     */
    // public function getSlugOptions() : SlugOptions
    // {
    //     return SlugOptions::create()
    //         ->generateSlugsFrom('name')
    //         ->saveSlugsTo('slug');
    // }

    public static function getScheduleDetails($item_ids)
    {
        $total_minutes  = 0;
        $total_amount   = 0;
        $lead_before    = 0;
        $lead_after     = 0;
        $description    = '';
        $data_price     = 0;
        $result         = array();
        $store          = Shop::find(Auth::user()->shop_id);

        foreach($item_ids as $key => $item){
            $data = self::find($item);
                
            $total_minutes  = ($total_minutes+$data->hours->value);
            $data_price     = Service::getPriceAfterTax($item);
            $total_amount   += $data_price;


            if($data->lead_before != null){
                $total_minutes += $data->leadBefore->value;
                $lead_before = $data->leadBefore->value;
            }

            if($data->lead_after != null){
                $total_minutes += $data->leadAfter->value;
                $lead_after = $data->leadAfter->value;

            }

            $description .= $data->name. ' ( ' . ($data->hours->value+$lead_before+$lead_after) . ' mns ) - ' . $store->billing->currencyCode->symbol. ' ' .number_format($data_price,2) .' <br>';
            
            $lead_before    = 0;
            $lead_after     = 0;
            
        }
        $description .= "<br> Price : ". number_format($total_amount,2);
        $result = array('total_hours' => $total_minutes, 'description' => $description);
        return $result;
    }

    public static function getDetails($id)
    {
        $result         = array();
        $minutes        = 0;
        $lead_before    = 0;
        $lead_after     = 0;
        $data           = self::find($id);

        if($data) {
            $minutes += $data->hours->value;

            if($data->lead_before != null) {
                $minutes        += $data->leadBefore->value;
                $lead_before    = $data->leadBefore->value;
            }
                
            if($data->lead_after != null) {
                $minutes        += $data->leadAfter->value;
                $lead_after     = $data->leadAfter->value;
            }
                
            $result = array('total_minutes' => $minutes, 'lead_before' => $lead_before, 'lead_after' => $lead_after);
            return $result;
        }
        return false;
    }

    public static function getPriceAfterTax($id)
    {
        $total_percentage       = 0 ;
        $gross_charge           = 0 ;
        $gross_value            = 0 ;
        $grand_total            = 0 ;

        $data                   = self::find($id);
        
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
        
}
