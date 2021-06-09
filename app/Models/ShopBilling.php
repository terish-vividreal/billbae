<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Form;
use App\Models\District;
use App\Models\State;
use App\Models\Country;

class ShopBilling extends Model
{
    protected $table = 'shop_billings';
    use HasFactory;

    public function sho()
    {
        return $this->belongsTo(Country::class);
    }

}
