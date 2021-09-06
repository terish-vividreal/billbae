<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;
use App\Models\BillingFormat;

class Billing extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function billingaddress()
    {
        return $this->belongsTo(BillingAddres::class,  'id', 'bill_id');
    }

    public function items()
    {
        return $this->hasMany(BillingItem::class, 'billing_id', 'id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(BillAmount::class, 'bill_id', 'id');
    }

    public function getDateRangeBilledDateAttribute($date)
    {
        return FunctionHelper::dateToTimeZone($this->billed_date, 'd-m-Y h:i A');
    }

    public function getDateRangeCheckinTimeAttribute($date)
    {
        return FunctionHelper::dateToTimeZone($this->checkin_time, 'd-m-Y h:i A');
    }
    public function getDateRangeCheckoutTimeAttribute($date)
    {
        return FunctionHelper::dateToTimeZone($this->checkout_time, 'd-m-Y h:i A');
    }

    public static function getDefaultFormat()
    {
        return BillingFormat::where('shop_id', SHOP_ID)->where('payment_type', 0)->first();
    }
}
