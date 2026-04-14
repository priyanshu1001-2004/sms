<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentModal extends Model
{
    use SoftDeletes;

    protected $table = 'parents';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    // Multi-tenancy Scope
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
                $builder->where('organization_id', currentOrgId());
            }
        });
    }

    /**
     * Relationship: Link to the User account for login
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Link to the staff member who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}
