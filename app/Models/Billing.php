<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FunctionHelper;
use App\Models\BillingFormat;
use App\Models\BillingItem;
use App\Models\Customer;

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

    public function schedule()
    {
        return $this->belongsTo(Schedule::class,  'id', 'billing_id');
    }

    public static function generateBill($request)
    {
        // echo "<pre>"; print_r($request); 
        $billed_date    = FunctionHelper::dateToTimeFormat($request['start']);
        // $checkin_time   = FunctionHelper::dateToTimeFormat($request->checkin_time);
        // $checkout_time  = FunctionHelper::dateToTimeFormat($request->checkout_time);

        $billing                    = new Billing();
        $billing->shop_id           = SHOP_ID;
        $billing->customer_id       = $request['customer_id'];
        $billing->customer_type     = Customer::isExisting($request['customer_id']);        
        $billing->amount            = $request['grand_total'];
        $billing->billed_date       = FunctionHelper::dateToUTC($billed_date, 'Y-m-d H:i:s A');
        // $billing->checkin_time      = FunctionHelper::dateToUTC($checkin_time, 'Y-m-d H:i:s A'); 
        // $billing->checkout_time     = FunctionHelper::dateToUTC($checkout_time, 'Y-m-d H:i:s A'); 
        $billing->payment_status    = 0 ;
        $billing->address_type      = 'customer' ;
        $billing->save();
        

        
        if($request['bill_item']){
            foreach($request['bill_item'] as $row){
                $item                   = new BillingItem();
                $item->billing_id       = $billing->id ;
                $item->customer_id      = $request['customer_id'] ;
                $item->item_type        = ($request['service_type'] == 1) ? 'services' : 'packages' ;
                $item->item_id          = $row ;
                $item->save();
            }       
        }
        if($billing)
            return $billing; 
    }

    public static function updateBill($request, $id)
    {
        // echo "<pre>"; print_r($request); 
        $billed_date    = FunctionHelper::dateToTimeFormat($request['start_time']);
        // $checkin_time   = FunctionHelper::dateToTimeFormat($request->checkin_time);
        // $checkout_time  = FunctionHelper::dateToTimeFormat($request->checkout_time);

        $billing                    = Billing::find($id);     
        $billing->amount            = $request['grand_total'];
        $billing->billed_date       = FunctionHelper::dateToUTC($billed_date, 'Y-m-d H:i:s A');
        $billing->payment_status    = 0 ;
        $billing->address_type      = 'customer' ;
        $billing->save();
        
        $old_bill_items = BillingItem::where('billing_id', $id)->where('customer_id', $request['customer_id'])->delete();
        
        if($request['bill_item']){
            foreach($request['bill_item'] as $row){
                $item                   = new BillingItem();
                $item->billing_id       = $billing->id ;
                $item->customer_id      = $request['customer_id'] ;
                $item->item_type        = ($request['service_type'] == 1) ? 'services' : 'packages' ;
                $item->item_id          = $row ;
                $item->save();
            }       
        }
        if($billing)
            return $billing; 
    }

    public static function deleteBill($id)
    {
        $data   =  self::find($id);
        BillingItem::where('billing_id',$data->id)->delete();

        if($data->delete())
            return true;
    }
}
