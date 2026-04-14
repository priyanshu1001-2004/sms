<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ClassSubject extends Model
{
    use SoftDeletes;

    // Use guarded to protect primary keys and timestamps
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Relationship: The class assigned to this record.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Relationship: The subject assigned to this record.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relationship: The user who created this assignment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTeacher()
    {
        // We link to SubjectTeacher based on the class_subject_id
        return $this->hasOne(SubjectTeacher::class, 'class_subject_id');
    }


    /**
     * Global Scope for Multi-Tenancy.
     * Ensures organizations only see their own assignments.
     */
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            // Check if user is logged in and is NOT a super_admin
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                $builder->where('organization_id', currentOrgId());
            }
        });
    }
}
