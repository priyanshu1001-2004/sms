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
        // 1. Saari active classes uthayein filter ke liye
        $classes = Classes::with('timetableGroup')->where('status', 1)->get();
        $days = WeekDay::orderBy('sort_order')->get();
        $allTeachers = Teacher::where('status', 1)->get();

        $selectedClass = $request->class_id;
        $slots = collect(); // Default khali collection
        $timetableData = [];
        $activeGroup = null;

        if ($selectedClass) {
            // 2. Selected class ka data nikalte hain taaki uska Group pata chale
            $classData = Classes::with('timetableGroup')->find($selectedClass);

            if ($classData && $classData->timetable_group_id) {
                $activeGroup = $classData->timetableGroup;

                // 3. Sabse Important: Sirf wahi slots uthayein jo is class ke assigned Group mein hain
                $slots = TimeSlot::where('timetable_group_id', $classData->timetable_group_id)
                    ->orderBy('start_time')
                    ->get();

                // 4. Existing timetable entries fetch karein grid populate karne ke liye
                $entries = ClassTimetable::with(['subject', 'teacher'])
                    ->where('class_id', $selectedClass)
                    ->get();

                foreach ($entries as $entry) {
                    $timetableData[$entry->week_day_id][$entry->time_slot_id] = $entry;
                }
            }
        }

        return view('pages.timetable.index', compact(
            'classes',
            'days',
            'slots',
            'timetableData',
            'selectedClass',
            'allTeachers',
            'activeGroup'
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
            'class_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'week_day_id' => 'required',
            'time_slot_id' => 'required',
        ]);

        // 1. Current Slot ki timings fetch karein
        $newSlot = TimeSlot::findOrFail($request->time_slot_id);
        $entryId = $request->entry_id; // Edit mode ke liye

        // 2. TEACHER FREE CHECK (The Main Logic)
        $conflict = ClassTimetable::where('week_day_id', $request->week_day_id)
            ->where('id', '!=', $entryId) // Edit karte waqt khud se conflict na ho
            ->where(function ($query) use ($request, $newSlot) {

                $query->where(function ($q) use ($request, $newSlot) {
                    $q->where('teacher_id', $request->teacher_id)
                        ->whereHas('timeSlot', function ($t) use ($newSlot) {
                            $t->where('start_time', '<', $newSlot->end_time)
                                ->where('end_time', '>', $newSlot->start_time);
                        });
                })
                    ->orWhere(function ($q) use ($request) {
                        $q->where('class_id', $request->class_id)
                            ->where('time_slot_id', $request->time_slot_id);
                    });
            })->first();

        if ($conflict) {
            $teacherName = $conflict->teacher->first_name . ' ' . $conflict->teacher->last_name;
            $className = $conflict->class->name;

            $errorMsg = ($conflict->teacher_id == $request->teacher_id)
                ? "Conflict: {$teacherName} is already busy in Class {$className} at this time!"
                : "Conflict: This class slot is already occupied by another subject.";

            return response()->json(['status' => false, 'message' => $errorMsg], 422);
        }

        ClassTimetable::updateOrCreate(
            ['id' => $entryId],
            [
                'organization_id' => currentOrgId(),
                'class_id'       => $request->class_id,
                'subject_id'     => $request->subject_id,
                'teacher_id'     => $request->teacher_id,
                'week_day_id'    => $request->week_day_id,
                'time_slot_id'   => $request->time_slot_id,
                'room_number'    => $request->room_number,
            ]
        );

        return response()->json(['status' => true, 'message' => 'Assignment successful!']);
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
    public function update(Request $request, $id)
    {

        $request->merge(['entry_id' => $id]);


        return $this->store($request);
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
