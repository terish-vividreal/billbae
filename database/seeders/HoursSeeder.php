<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hours;

class HoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [            
            ['id' => 1, 'name' => '10 mns'],
            ['id' => 2, 'name' => '15 mns'],
            ['id' => 3, 'name' => '30 mns'],
            ['id' => 4, 'name' => '45 mns'],
            ['id' => 5, 'name' => '60 mns'],
            ['id' => 6, 'name' => '90 mns'],
        ];
    
        foreach ($items as $item) {
            Hours::create($item);
        }
    }
}
