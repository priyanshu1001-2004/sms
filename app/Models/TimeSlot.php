<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TimeSlot extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                $builder->where($builder->getQuery()->from . '.organization_id', currentOrgId());
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->organization_id = $model->organization_id ?? currentOrgId();
            }
        });
    }

    public function group()
    {
        return $this->belongsTo(TimetableGroup::class, 'timetable_group_id');
    }
}
