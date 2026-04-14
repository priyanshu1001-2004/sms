<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SubjectTeacher extends Model
{

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                // Fixes the "Ambiguous column" error automatically
                $builder->where($builder->getQuery()->from . '.organization_id', currentOrgId());
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->organization_id = $model->organization_id ?? currentOrgId();
                $model->created_by = $model->created_by ?? Auth::id();
            }
        });
    }
}