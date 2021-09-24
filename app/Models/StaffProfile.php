<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    use HasFactory;

    public function scheduleColor()
    {
        return $this->belongsTo(ScheduleColor::class,  'id', 'schedule_colour');
    }
}
