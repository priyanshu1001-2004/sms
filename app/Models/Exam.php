<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Exam extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Relationships
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }


    public function results()
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    /**
     * Multi-tenant Global Scope & Auto-Organization Mapping
     */
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                // Ground the query to the specific organization
                $builder->where($builder->getQuery()->from . '.organization_id', currentOrgId());
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->organization_id = $model->organization_id ?? currentOrgId();
            }
        });
    }
}
