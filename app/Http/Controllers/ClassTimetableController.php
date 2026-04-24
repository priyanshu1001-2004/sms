<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassTimetable;
use App\Models\Teacher;
use App\Models\TeacherSubject;
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

        $orgId = currentOrgId();
        $entryId = $request->entry_id;

        $newSlot = TimeSlot::findOrFail($request->time_slot_id);
        $newStart = $newSlot->start_time;
        $newEnd = $newSlot->end_time;

        // 1. Check for Conflicts (Teacher or Class already busy)
        $conflict = ClassTimetable::where('week_day_id', $request->week_day_id)
            ->where('id', '!=', $entryId)
            ->where(function ($query) use ($request, $newStart, $newEnd) {
                $query->where(function ($q) use ($request, $newStart, $newEnd) {
                    // Conflict: Same Teacher at same time
                    $q->where('teacher_id', $request->teacher_id)
                        ->whereHas('timeSlot', function ($t) use ($newStart, $newEnd) {
                            $t->where('start_time', '<', $newEnd)
                                ->where('end_time', '>', $newStart);
                        });
                })
                    ->orWhere(function ($q) use ($request, $newStart, $newEnd) {
                        // Conflict: Same Class at same time
                        $q->where('class_id', $request->class_id)
                            ->whereHas('timeSlot', function ($t) use ($newStart, $newEnd) {
                                $t->where('start_time', '<', $newEnd)
                                    ->where('end_time', '>', $newStart);
                            });
                    });
            })
            ->with(['teacher', 'class', 'timeSlot'])
            ->first();

        // 2. Handle Conflict Message
        if ($conflict) {
            $teacherName = $conflict->teacher->name; // Using 'name' instead of first/last
            $className = $conflict->class->name;
            $slotTime = date('h:i A', strtotime($conflict->timeSlot->start_time)) . ' - ' . date('h:i A', strtotime($conflict->timeSlot->end_time));

            if ($conflict->teacher_id == $request->teacher_id) {
                $msg = "Conflict: {$teacherName} is already teaching Class {$className} at {$slotTime}.";
            } else {
                $msg = "Conflict: Class {$className} already has a subject scheduled at {$slotTime}.";
            }

            return response()->json(['status' => false, 'message' => $msg], 422);
        }

        // 3. Save or Update
        // Note: organization_id is handled automatically if you use the Multitenantable trait
        ClassTimetable::updateOrCreate(
            ['id' => $entryId],
            [
                'class_id'     => $request->class_id,
                'subject_id'    => $request->subject_id,
                'teacher_id'    => $request->teacher_id,
                'week_day_id'   => $request->week_day_id,
                'time_slot_id'  => $request->time_slot_id,
                'room_number'   => $request->room_number,
            ]
        );

        return response()->json(['status' => true, 'message' => 'Timetable updated successfully!']);
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

    public function getQualifiedTeachers(Request $request)
    {
        $classId = $request->class_id;
        $subjectId = $request->subject_id;

        // 1. Find mappings for this Class + Subject
        // Ensure TeacherSubject model has a 'teacher' relationship pointing to the Teacher model
        $assignments = TeacherSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('status', 1)
            ->with('teacher')
            ->get();

        // 2. Format for Select2
        $teachers = $assignments->map(function ($a) {
            if ($a->teacher) {
                return [
                    'id'   => $a->teacher->id,
                    'name' => $a->teacher->first_name . ' ' . $a->teacher->last_name
                ];
            }
        })->filter(); // Remove nulls if any mapping has a missing teacher record

        return response()->json(['teachers' => $teachers]);
    }
}
