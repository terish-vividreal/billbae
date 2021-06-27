<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function billingaddress()
    {
        return $this->belongsTo(BillingAddres::class,  'id', 'customer_id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public static function isExisting($id)
    {

        $data =  self::find($id);
        $result = (count($data->billings) > 0)?'1':'0';
        return $result;
        
        // if($is_existing){
        //     return "Yes" ;
        // }else{
        //     return "NO";
        // }
    }
}
