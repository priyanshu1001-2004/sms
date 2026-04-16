<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExamResult extends Model
{
    protected $guarded = ['id'];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        // 1. Tenant Security
        static::addGlobalScope('tenant', function ($builder) {
            if (Auth::check() && !Auth::user()->hasRole('super_admin')) {
                $builder->where($builder->getQuery()->from . '.organization_id', currentOrgId());
            }
        });

        // 2. The "Professional Processor"
        static::saving(function ($model) {
            // Auto-fill metadata
            if (Auth::check()) {
                $model->organization_id = $model->organization_id ?? currentOrgId();
                $model->created_by = $model->created_by ?? Auth::id();
            }

            if ($model->attendance == 'P') {
                // Fetch full marks from the schedule
                $schedule = ExamSchedule::where('exam_id', $model->exam_id)
                    ->where('subject_id', $model->subject_id)
                    ->first();

                if ($schedule && $schedule->full_marks > 0) {
                    $percentage = ($model->marks_obtained / $schedule->full_marks) * 100;

                    /**
                     * Fetching the Grade using the percent range.
                     * We use a direct query here to ensure it uses the tenant-scoped Grade scale.
                     */
                    $grade = Grade::where('percent_from', '<=', $percentage)
                        ->where('percent_to', '>=', $percentage)
                        ->first();

                    if ($grade) {
                        $model->grade_name = $grade->name;
                        $model->grade_point = $grade->grade_point;
                    }
                }
            } else {
                // Attendance is 'A' (Absent) or 'M' (Medical)
                $model->marks_obtained = 0;
                $model->grade_name = $model->attendance == 'A' ? 'Absent' : 'Medical';
                $model->grade_point = 0.00;
            }
        });
    }

    public static function getGradeByPercentage($percentage)
    {
        return self::where('percent_from', '<=', $percentage)
            ->where('percent_to', '>=', $percentage)
            ->first();
    }
}
