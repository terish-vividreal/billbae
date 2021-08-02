<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;

class Billing extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
}
