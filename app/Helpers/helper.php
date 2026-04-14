<?php

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;


if (!function_exists('CodeGenerator')) {
    function CodeGenerator(string $table, string $column, string $prefix, int $pad = 4, $organizationId = null)
    {
        return DB::transaction(function () use ($table, $column, $prefix, $pad, $organizationId) {
            $query = DB::table($table)->where($column, 'like', $prefix . '%');

            if ($organizationId) {
                $query->where('organization_id', $organizationId);
            }

            $lastCode = $query->orderByDesc($column)->lockForUpdate()->value($column);

            if ($lastCode) {
                preg_match('/(\d+)$/', $lastCode, $matches);
                $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return $prefix . str_pad($nextNumber, $pad, '0', STR_PAD_LEFT);
        });
    }
}

/*
|--------------------------------------------------------------------------
| Org Based Unique Phone Validation
|--------------------------------------------------------------------------
*/
if (!function_exists('uniquePhone')) {
    function uniquePhone($orgId, $ignoreId = null)
    {
        return Rule::unique('users', 'phone')
            ->where(fn($q) => $q->where('organization_id', $orgId))
            ->ignore($ignoreId);
    }
}

/*
|--------------------------------------------------------------------------
| Current Organization Helper
|--------------------------------------------------------------------------
*/
if (!function_exists('currentOrgId')) {
    function currentOrgId()
    {
        return Auth::user()->organization_id ?? null;
    }
}



function auditLog(
    $module,
    $action,
    $recordId = null,
    $field = null,
    $old = null,
    $new = null,
    $description = null,
    $event = null,
    $affectedUserId = null
) {
    try {
        // Only log if a user is authenticated, or handle guest actions
        $user = Auth::user();

        AuditLog::create([
            'user_id'         => $user ? $user->id : null,
            // CRITICAL: Change hotel_id to organization_id
            'organization_id' => $user ? $user->organization_id : null,

            'module'          => $module,
            'action'          => $action,
            'event'           => $event,
            'description'     => $description,

            'url'             => Request::fullUrl(),
            'method'          => Request::method(),

            'affected_user_id' => $affectedUserId,
            'record_id'       => $recordId,
            'field_name'      => $field,

            // Automatically handle arrays/objects for JSON columns
            'old_value'       => (is_array($old) || is_object($old)) ? json_encode($old) : $old,
            'new_value'       => (is_array($new) || is_object($new)) ? json_encode($new) : $new,

            'ip_address'      => Request::ip(),
            'user_agent'      => Request::header('User-Agent'),
        ]);
    } catch (\Exception $e) {
        // Log to Laravel's system log so we know the audit failed
        Log::error('Audit Log Failed: ' . $e->getMessage());
    }
}


function formatDate($date, $format = 'd M Y')
{
    if (empty($date)) {
        return null;
    }

    try {
        // This handles strings, timestamps, and Carbon objects safely
        return Carbon::parse($date)->format($format);
    } catch (\Exception $e) {
        return 'Invalid Date';
    }
}
