<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassTimetable;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\WeekDay;
use Illuminate\Http\Request;

class ClassTimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classes = Classes::all();
        $days = WeekDay::orderBy('sort_order')->get();
        $slots = TimeSlot::orderBy('start_time')->get();
        $allTeachers = Teacher::where('status', 1)->get();

        $selectedClass = $request->class_id;
        $timetableData = [];

        if ($selectedClass) {
            // Fetch existing entries for the grid
            $entries = ClassTimetable::with(['subject', 'teacher'])
                ->where('class_id', $selectedClass)
                ->get();

            // Organize by Day and Slot for easy grid lookup
            foreach ($entries as $entry) {
                $timetableData[$entry->week_day_id][$entry->time_slot_id] = $entry;
            }
        }

        return view('pages.timetable.index', compact('classes', 'days', 'slots', 'timetableData', 'selectedClass', 'allTeachers'));
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
            'class_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'week_day_id' => 'required',
            'time_slot_id' => 'required',
        ]);

        $conflict = ClassTimetable::where([
            'week_day_id' => $request->week_day_id,
            'time_slot_id' => $request->time_slot_id,
        ])
            ->where('id', '!=', $request->entry_id)
            ->where(function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id)
                    ->orWhere('class_id', $request->class_id);
            })->first();

        if ($conflict) {
            return response()->json(['status' => false, 'message' => 'This slot or teacher is already occupied!']);
        }

        // Use updateOrCreate
        ClassTimetable::updateOrCreate(
            ['id' => $request->entry_id],
            [
                'organization_id' => currentOrgId(),
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'week_day_id' => $request->week_day_id,
                'time_slot_id' => $request->time_slot_id,
                'room_number' => $request->room_number,
            ]
        );

        return response()->json(['status' => true, 'message' => 'Timetable entry saved successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassTimetable $classTimetable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassTimetable $classTimetable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassTimetable $classTimetable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified timetable assignment.
     * * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // 1. Find the entry while enforcing Multi-Tenancy
            // This ensures a user from School A cannot delete a record from School B
            $timetable = \App\Models\ClassTimetable::where('id', $id)
                ->where('organization_id', currentOrgId())
                ->first();

            if (!$timetable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found or access denied.'
                ], 404);
            }

            // 2. Perform the deletion
            $timetable->delete();

            return response()->json([
                'status' => true,
                'message' => 'Assignment has been successfully removed.'
            ]);
        } catch (\Exception $e) {
            // Log the error for the developer
            \Log::error("Timetable Deletion Error: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while deleting the record.'
            ], 500);
        }
    }
    public function getTeacherBySubject($subjectId, $classId)
    {
        $classSubject = \App\Models\ClassSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$classSubject) {
            return response()->json(['data' => null, 'message' => 'Subject not linked to class']);
        }

        $assignment = \App\Models\SubjectTeacher::with('teacher')
            ->where('class_subject_id', $classSubject->id)
            ->first();

        return response()->json([
            'status' => true,
            'data' => $assignment
        ]);
    }
}
