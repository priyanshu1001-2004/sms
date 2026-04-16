<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
                // FIX: Prefix organization_id with the table name to prevent ambiguity
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.organization_id', currentOrgId());
            }
        });
    }

    /**
     * Relationship: Students in this class
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->hasOne(ClassTeacher::class, 'class_id');
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'class_id');
    }

    // This refers to the pivot model directly
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    // This is the many-to-many relationship using your custom pivot table
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withPivot('status', 'organization_id') // Recommended to include these
            ->wherePivot('status', 1);
    }
}
