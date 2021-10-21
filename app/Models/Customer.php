<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['shop_id', 'name', 'email', 'mobile', 'gender', 'dob'];

    public function billingaddress()
    {
        return $this->belongsTo(BillingAddres::class,  'id', 'customer_id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
    public function getDobAttribute()
    {
        $dob = new Carbon\Carbon($this->attributes['dob']);
        return $dob;
    }

    public static function isExisting($id)
    {
        $data   =  self::find($id);
        $result = (count($data->billings) > 0)?'1':'0';
        return $result;
        // if($is_existing){
        //     return "Yes" ;
        // }else{
        //     return "NO";
        // }
    }
}
