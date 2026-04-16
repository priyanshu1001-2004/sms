<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Grade extends Model
{
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

    /**
     * Professional Helper: Find the grade for a given percentage
     */
    public static function getGradeByPercentage($percentage)
    {
        return self::where('percent_from', '<=', $percentage)
                   ->where('percent_to', '>=', $percentage)
                   ->first();
    }
}