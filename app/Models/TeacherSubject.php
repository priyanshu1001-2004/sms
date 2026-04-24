<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
                $builder->where('organization_id', currentOrgId());
            }
        });
    }
}
