<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAmount extends Model
{
    // protected $table = 'my_flights';

    public function paymentype()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type', 'id');
    }
}
