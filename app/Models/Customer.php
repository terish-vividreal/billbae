<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\FunctionHelper;
use App\Models\Billing;
use Carbon;
use DB;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['shop_id', 'customer_code', 'name', 'email', 'mobile', 'gender', 'dob'];

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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function getCreatedAtAttribute()
    {
        return FunctionHelper::dateToTimeZone($this->attributes['created_at'], 'd-m-Y h:i A');
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

    public static function lastActivity($id)
    {
        return Billing::where('customer_id', $id)->orderBy('billed_date','DESC')->first();
    }

    public static function completedBills($id)
    {
        return Billing::where('shop_id', SHOP_ID)->where('customer_id', $id)->where('payment_status', 1)->get(); 
    }

    public static function pendingBills($id)
    {
        return Billing::where('shop_id', SHOP_ID)->where('customer_id', $id)->where('payment_status', 0)->get(); 
    }
}
