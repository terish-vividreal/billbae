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

    public static function totalTime($item_ids)
    {
        $total_minutes  = 0;
        $lead_before    = 0;
        $lead_after     = 0;
        $description    = '';
        $result         = array();
        $store          = Shop::find(Auth::user()->shop_id);

        foreach($item_ids as $key => $item){
            $data = self::find($item);
                
            $total_minutes = ($total_minutes+$data->hours->value);


            if($data->lead_before != null){
                $total_minutes += $data->leadBefore->value;
                $lead_before = $data->leadBefore->value;
            }

            if($data->lead_after != null){
                $total_minutes += $data->leadAfter->value;
                $lead_after = $data->leadAfter->value;

            }

            $description .= $data->name. ' ( ' . ($data->hours->value+$lead_before+$lead_after) . ' mns ) - ' . $store->billing->currencyCode->symbol. ' ' .$data->price .' <br>';
            $lead_before    = 0;
            $lead_after     = 0;
            
        }
        // echo $total_minutes;
        $result = array('total_hours' => $total_minutes, 'description' => $description);
        return $result;
    }

    // public static function getDescriptions($item_ids)
    // {
    //     $description = '';
    //     foreach($item_ids as $item){
    //         $data = self::find($item);
    //         $description .= $data->name.', ';
    //     }
    //     return rtrim($description, ', ');
    // }
}
