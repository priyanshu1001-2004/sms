<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

  
    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class, 'timetable_group_id')->orderBy('start_time');
    }

  
    public function classes()
    {
        return $this->hasMany(Classes::class, 'timetable_group_id'); 
    }

    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

   
}