<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    // public function services()
    // {
    //     return $this->belongsToMany('App\Models\Year', 'task_year', 'task_id', 'year_id');
    // }

    public function service()
    {
        return $this->belongsToMany('App\Models\Service')->withTimestamps();
    }
}
