<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    use HasFactory;
    
    public function additionalTax()
    {
        return $this->hasMany(BillingItemAdditionalTax::class, 'bill_item_id', 'id');
    }

    // public function appliedtax()
    // {
    //     return $this->hasMany(BillingItemTax::class, 'bill_item_id', 'id');
    // }

}
