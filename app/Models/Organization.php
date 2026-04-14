<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $with = ['user', 'subscription'];

    public function user()
    {
        return $this->hasOne(User::class, 'organization_id', 'id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
}
