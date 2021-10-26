<?php

namespace App\Imports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;

class ServicesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Service([
            'shop_id' => SHOP_ID,
            'name' => $row['name'],
            'slug' => $row['name'],
            'hours_id' => 3,
            'price' => $row['price'],
        ]);
    }
}
