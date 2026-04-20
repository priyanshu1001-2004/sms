<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Subscription extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'amount'     => 'decimal:2',
    ];

    protected function daysRemaining(): Attribute
    {
        return Attribute::get(function () {
            $today = now()->startOfDay();
            $expiry = Carbon::parse($this->end_date)->startOfDay();

            // If the date is in the past, return 0
            if ($today->gt($expiry)) {
                return 0;
            }

            return (int) $today->diffInDays($expiry);
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.organization_id', currentOrgId());
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
