<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamResultController extends Controller
{
    /**
     * BULK ENTRY: List students by Exam, Class, and Subject
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $exams = Exam::all();
        $subjects = Subject::all();

        // 1. Get the teacher profile once to avoid repeating queries
        $teacherProfile = $user->hasRole('teacher') ? $user->teacher : null;

        // 2. Filter Classes dropdown
        $query = Classes::query();

        if ($user->hasRole('teacher')) {
            $teacherProfile = $user->teacher;

            if ($teacherProfile) {
                // This looks into your class_teachers pivot table
                $query->whereHas('classTeachers', function ($q) use ($teacherProfile) {
                    $q->where('teacher_id', $teacherProfile->id);
                });
            } else {
                // Fallback: If no profile, try matching by User ID directly if that's how you stored it
                $query->whereHas('classTeachers', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
            }
        }
        $classes = $query->get();

        $students = [];
        if ($request->filled(['exam_id', 'class_id', 'subject_id'])) {

            // 3. Security Check: Verify class assignment via Pivot Table
            if ($user->hasRole('teacher')) {
                if (!$teacherProfile) {
                    return redirect()->back()->with('error', 'Your teacher profile is missing.');
                }

                $isAssigned = \DB::table('class_teachers')
                    ->where('class_id', $request->class_id)
                    ->where('teacher_id', $teacherProfile->id)
                    ->exists();

                if (!$isAssigned) {
                    return redirect()->back()->with('error', 'You are not authorized to enter marks for this class.');
                }
            }

            // 4. Fetch Students with filtered Exam Results
            $students = Student::where('class_id', $request->class_id)
                ->with(['examResults' => function ($q) use ($request) {
                    $q->where('exam_id', $request->exam_id)
                        ->where('subject_id', $request->subject_id);
                }])
                ->orderBy('roll_number', 'asc')
                ->get();
        }

        return view('pages.exams.mark_entry', compact('exams', 'classes', 'subjects', 'students'));
    }

    /**
     * STUDENT-WISE ENTRY: List subjects for a specific student
     */
    public function studentWise(Request $request)
    {
        $user = auth()->user();
        $exams = Exam::orderBy('start_date', 'desc')->get();

        // 1. Filter Classes based on Teacher Assignment (Matching your Bulk Logic)
        $query = Classes::query();

        if ($user->hasRole('teacher')) {
            $teacherProfile = $user->teacher; // Using 'teacher' as per your update

            if ($teacherProfile) {
                $query->whereHas('classTeachers', function ($q) use ($teacherProfile) {
                    $q->where('teacher_id', $teacherProfile->id);
                });
            } else {
                // Fallback: If no profile record, try matching by User ID directly
                $query->whereHas('classTeachers', function ($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                });
            }
        }
        $classes = $query->orderBy('name', 'asc')->get();

        $student = null;
        $subjects = [];
        $existingResults = [];

        // 2. Process student selection
        if ($request->filled(['exam_id', 'student_id'])) {
            $student = Student::with('class')->findOrFail($request->student_id);

            // --- SECURITY CHECK ---
            // Verify this student belongs to a class assigned to this teacher
            if ($user->hasRole('teacher')) {
                $teacherId = $user->teacher?->id ?? $user->id;
                $isAuthorized = \DB::table('class_teachers')
                    ->where('class_id', $student->class_id)
                    ->where('teacher_id', $teacherId)
                    ->exists();

                if (!$isAuthorized) {
                    return redirect()->route('exam-results.student-wise')
                        ->with('error', 'You are not authorized to access this student\'s records.');
                }
            }

            // 3. Fetch subjects linked to this class via pivot
            $subjects = Subject::whereHas('classes', function ($q) use ($student) {
                $q->where('classes.id', $student->class_id)
                    ->where('class_subjects.status', 1);
            })->get();

            // 4. Pre-fill existing marks
            $existingResults = ExamResult::where('exam_id', $request->exam_id)
                ->where('student_id', $request->student_id)
                ->get()
                ->keyBy('subject_id');
        }

        return view('pages.exams.student_wise_entry', compact('exams', 'classes', 'student', 'subjects', 'existingResults'));
    }

    /**
     * UNIFIED STORE: Handles both Bulk (Class-wise) and Student-wise entry
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'marks' => 'required|array',
        ]);

        $user = auth()->user();

        if ($user->hasRole('teacher')) {
            $teacherProfile = $user->teacher_profile;

            if (!$teacherProfile) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error: Your teacher profile is not set up. Please contact admin.'
                ], 403);
            }

            // 2. Check assignment using the Profile ID
            $isAssigned = DB::table('class_teachers')
                ->where('class_id', $request->class_id)
                ->where('teacher_id', $teacherProfile->id)
                ->exists();

            if (!$isAssigned) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You are not assigned to this class.'
                ], 403);
            }
        }
        // ---------------------------------

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->marks as $studentId => $data) {

                    // Detect Student-wise Entry
                    if (is_array(reset($data))) {
                        foreach ($data as $subjectId => $values) {
                            $this->saveResult(
                                $request->exam_id,
                                $studentId,
                                $subjectId,
                                $request->class_id,
                                $values
                            );
                        }
                    }
                    // Detect Bulk Entry
                    else {
                        $this->saveResult(
                            $request->exam_id,
                            $studentId,
                            $request->subject_id,
                            $request->class_id,
                            $data
                        );
                    }
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Marks processed and grades calculated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Private Helper: Database logic for Result processing
     */
    private function saveResult($examId, $studentId, $subjectId, $classId, $data)
    {
        // Ignore rows with no data
        if (!isset($data['score']) && !isset($data['attendance'])) {
            return;
        }

        ExamResult::updateOrCreate(
            [
                'exam_id'    => $examId,
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'class_id'   => $classId,
            ],
            [
                'marks_obtained'  => $data['score'] ?? 0,
                'attendance'      => $data['attendance'] ?? 'P',
                'teacher_remarks' => $data['remarks'] ?? null,
            ]
        );
    }

    /**
     * AJAX Helper: Fetch students by class
     */
    public function getStudentsByClass($class_id)
    {
        $students = Student::where('class_id', $class_id)
            ->select('id', 'first_name', 'last_name', 'roll_number')
            ->orderBy('roll_number', 'asc')
            ->get();

        return response()->json($students);
    }
}
