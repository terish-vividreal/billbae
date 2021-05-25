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
}
