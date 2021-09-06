<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceCategory;
use App\Models\GstTaxPercentage;
use App\Models\Hours;

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
}
