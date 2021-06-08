<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    public function getShowImageAttribute()
    {
        return ($this->image != '') ? asset('storage/store/logo/' . $this->image) : asset('admin/img/dummy-logo.jpg');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function customer()
    {
        return $this->hasMany('App\Models\Customer');
    }

    public function service()
    {
        return $this->hasMany('App\Models\Service');
    }

    public function business_types()
    {
        return $this->hasOne('App\Models\BusinessType', 'id', 'business_type_id');
    }

    public function billing()
    {
        return $this->belongsTo('App\Models\ShopBilling', 'id', 'shop_id');
    }

}
