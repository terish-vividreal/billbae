<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function business_types()
    {
        return $this->hasOne('App\Models\BusinessType', 'id', 'business_type_id');
    }

}
