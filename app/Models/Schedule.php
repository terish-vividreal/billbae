<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon;

class Schedule extends Model
{
    use HasFactory;

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }

    public static function checkTimeAvailability($request)
    {
        // echo "<pre>"; print_r($request); exit;
        // Calculating end time
        $formatted_start_date       = new Carbon\Carbon($request['start_time']);
        $start_date                 = new Carbon\Carbon($request['start_time']);
        $end_time                   = $formatted_start_date->addMinutes($request['total_minutes']);

        $data   =  self::where('user_id', $request['user_id'])->whereBetween('start', [$start_date, $end_time])->get();
        
        if($data){
            return $data;
        }

    }
}
