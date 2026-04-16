<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // app/Http/Controllers/ExamController.php

    public function index()
    {
        $exams = Exam::with('academicYear')
            ->withCount('schedules')
            ->orderBy('id', 'desc')
            ->get();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $currentYear = AcademicYear::where('is_active', true)->first();


        $classes = \App\Models\Classes::orderBy('name', 'asc')->get();

        return view('pages.exams.index', compact(
            'exams',
            'academicYears',
            'currentYear',
            'classes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'term_name' => 'nullable|string|max:100',
        ]);

        try {
            $exam = Exam::create([
                'name'             => $request->name,
                'academic_year_id' => $request->academic_year_id,
                'term_name'        => $request->term_name,
                'start_date'       => $request->start_date,
                'end_date'         => $request->end_date,
                'description'      => $request->description,
                'is_published'     => false, // Default to unpublished for safety
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Examination created successfully!',
                'data'    => $exam
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified examination in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Professional Validation
        $request->validate([
            'name'             => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'term_name'        => 'nullable|string|max:100',
        ]);

        try {
            // 2. Find the record (Global Scope handles organization isolation)
            $exam = Exam::findOrFail($id);

            // 3. Prepare data (Handle checkbox/switch specifically)
            $data = $request->all();
            $data['is_published'] = $request->has('is_published') ? true : false;

            // 4. Update the record
            $exam->update($data);

            // 5. Return success for your ajax-form script
            return response()->json([
                'status'  => true,
                'message' => 'Examination updated successfully!',
                'data'    => $exam
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error: Could not update examination. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified examination from storage.
     */
    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);

            if ($exam->results()->exists()) {
                return response()->json(['status' => false, 'message' => 'Cannot delete exam with existing marks!']);
            }

            $exam->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Examination moved to trash successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to delete the record.'
            ], 500);
        }
    }

    public function manageSchedule($id)
    {
        $exam = Exam::findOrFail($id);
        $subjects = \App\Models\Subject::orderBy('name', 'asc')->get();
        $existingSchedules = \App\Models\ExamSchedule::where('exam_id', $id)->get();

        return response()->json([
            'status' => true,
            'exam' => $exam,
            'subjects' => $subjects,
            'existingSchedules' => $existingSchedules
        ]);
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'schedules' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->schedules as $data) {
                if (!empty($data['exam_date'])) {
                    // Determine the Class ID
                    // Top SMS Tip: If the exam is for the whole school, 
                    // you might want to make class_id nullable in migration instead.
                    $classId = $data['class_id'] ?? null;

                    \App\Models\ExamSchedule::updateOrCreate(
                        [
                            'exam_id'    => $request->exam_id,
                            'subject_id' => $data['subject_id'],
                            'class_id'   => $classId, // ADDED THIS LINE
                        ],
                        [
                            'exam_date'  => $data['exam_date'],
                            'start_time' => $data['start_time'],
                            'end_time'   => $data['end_time'],
                            'full_marks' => $data['full_marks'],
                            'pass_marks' => $data['pass_marks'],
                        ]
                    );
                }
            }
        });

        return response()->json(['status' => true, 'message' => 'Schedules updated successfully!']);
    }

    public function togglePublish($id)
    {
        try {
            // 1. Find the exam and ensure it belongs to the current tenant/organization
            $exam = Exam::where('id', $id)
                ->where('organization_id', currentOrgId())
                ->firstOrFail();

            // 2. Toggle the status
            $exam->is_published = !$exam->is_published;
            $exam->save();

            $status = $exam->is_published ? 'published' : 'unpublished';

            return response()->json([
                'status' => true,
                'message' => "Exam successfully {$status}!",
                'is_published' => $exam->is_published
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
