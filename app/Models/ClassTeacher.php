<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClassTeacher extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function schoolClass()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                // Using table name prefix prevents the "Ambiguous column" error during joins
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
