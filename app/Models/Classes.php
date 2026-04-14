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
                $builder->where('organization_id', currentOrgId());
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

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }
}