<?php

namespace App\Http\Controllers;

use App\Models\ExamSchedule;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::paginate(10);
        return view('pages.teachers.index', compact('teachers'));
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
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'mobile_number'   => 'required|string',
            'gender'          => 'required',
            'date_of_birth'   => 'required|date',
            'date_of_joining' => 'required|date',
            'qualification'   => 'required',
            'password'        => 'required|min:6',
            'teacher_photo'   => 'nullable|image|max:2048',
            'resume'          => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $orgId = currentOrgId();

            // 1. Handle File Uploads
            $photoPath = null;
            if ($request->hasFile('teacher_photo')) {
                $photoPath = $request->file('teacher_photo')->store('uploads/teachers/photos', 'public');
            }

            $resumePath = null;
            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->store('uploads/teachers/resumes', 'public');
            }

            // 2. Create User Account
            $user = User::create([
                'name'            => trim($request->first_name . ' ' . $request->last_name),
                'email'           => $request->email,
                'phone'           => $request->mobile_number,
                'password'        => Hash::make($request->password),
                'organization_id' => $orgId,
                'user_type'       => 'teacher',
            ]);

            $user->assignRole('teacher');

            // 3. Create Teacher Profile
            Teacher::create([
                'user_id'                  => $user->id,
                'organization_id'          => $orgId,
                'first_name'               => trim($request->first_name),
                'last_name'                => trim($request->last_name),
                'gender'                   => $request->gender,
                'date_of_birth'            => $request->date_of_birth,
                'email'                    => $request->email,
                'mobile_number'            => $request->mobile_number,
                'emergency_contact_number' => $request->emergency_contact_number,
                'current_address'          => $request->current_address,
                'permanent_address'        => $request->permanent_address,
                'qualification'            => $request->qualification,
                'work_experience'          => $request->work_experience,
                'date_of_joining'          => $request->date_of_joining,
                'teacher_photo'            => $photoPath,
                'resume_path'              => $resumePath,
                'note'                     => $request->note,
                'created_by'               => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Teacher profile created successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $teacher->user_id,
            'mobile_number' => 'required',
            'teacher_photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $photoPath = $teacher->teacher_photo;

            // Handle Photo Update
            if ($request->hasFile('teacher_photo')) {
                if ($photoPath) Storage::disk('public')->delete($photoPath);
                $photoPath = $request->file('teacher_photo')->store('uploads/teachers/photos', 'public');
            }

            // 1. Update Teacher Record
            $teacher->update([
                'first_name'        => trim($request->first_name),
                'last_name'         => trim($request->last_name),
                'gender'            => $request->gender,
                'date_of_birth'     => $request->date_of_birth,
                'email'             => $request->email,
                'mobile_number'     => $request->mobile_number,
                'date_of_joining'   => $request->date_of_joining,
                'qualification'     => $request->qualification,
                'work_experience'   => $request->work_experience,
                'marital_status'    => $request->marital_status,
                'current_address'   => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'teacher_photo'     => $photoPath,
            ]);

            // 2. Sync to User Account (Name & Email)
            if ($teacher->user) {
                $teacher->user->update([
                    'name'  => trim($request->first_name . ' ' . $request->last_name),
                    'email' => $request->email,
                    'phone' => $request->mobile_number,
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Teacher updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Soft Delete Teacher Record
            $teacher->delete();

            // 2. Deactivate User Account (to prevent login while record is in trash)
            if ($teacher->user) {
                $teacher->user->update(['status' => 0]);
            }

            // 3. Log the Action
            auditLog(
                module: 'Teachers',
                action: 'Delete',
                recordId: $id,
                description: "Soft deleted teacher: {$teacher->first_name} {$teacher->last_name}",
                event: 'teacher_deleted'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Teacher deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Delete failed: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        // The Global Scope 'tenant' ensures we only find teachers in the current school
        $teacher = Teacher::with('user')->findOrFail($id);

        try {
            // Switch the status (1 to 0 or 0 to 1)
            $newStatus = $teacher->status == 1 ? 0 : 1;

            // 1. Update Teacher Table
            $teacher->update(['status' => $newStatus]);

            // 2. Update User Table (Login Access)
            if ($teacher->user) {
                $teacher->user->update(['status' => $newStatus]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Teacher status updated successfully.',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::findOrFail($id);

            if ($user->organization_id !== currentOrgId()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized access.'], 403);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully for ' . $user->name
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function myClassesAndSubjects()
    {
        $user = auth()->user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->firstOrFail();

        // 1. CHANGE HERE: Use ->get() instead of ->first()
        // Use the full path inside the query to bypass any import issues
        $headClasses = \App\Models\ClassTeacher::with(['schoolClass' => function ($query) {
            $query->withCount('students');
        }])
            ->where('teacher_id', $teacher->id)
            ->get();

        // 2. Get Subject Assignments (Staffing)
        $subjectAssignments = \App\Models\SubjectTeacher::with([
            'classSubject.subject',
            'classSubject.class' => function ($query) {
                $query->withCount('students');
            }
        ])
            ->where('teacher_id', $teacher->id)
            ->get();

        return view('teacher.my_assignments', compact('teacher', 'headClasses', 'subjectAssignments'));
    }

    /**
     * AJAX to fetch students for a specific class
     */
    public function getClassStudents($classId)
    {
        $students = \App\Models\Student::where('class_id', $classId)
            ->where('organization_id', currentOrgId())
            ->get();

        return response()->json(['data' => $students]);
    }

    public function myStudents(Request $request)
    {
        $user = auth()->user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->firstOrFail();

        // 1. Get IDs of all classes this teacher is involved with
        $classIdsFromHead = \App\Models\ClassTeacher::where('teacher_id', $teacher->id)->pluck('class_id')->toArray();
        $classIdsFromSubjects = \App\Models\SubjectTeacher::where('teacher_id', $teacher->id)
            ->join('class_subjects', 'subject_teachers.class_subject_id', '=', 'class_subjects.id')
            ->pluck('class_subjects.class_id')
            ->toArray();

        // Combine and remove duplicates
        $allClassIds = array_unique(array_merge($classIdsFromHead, $classIdsFromSubjects));

        // 2. Fetch the Students
        $query = \App\Models\Student::with('class')
            ->whereIn('class_id', $allClassIds);

        // 3. Optional: Filter by specific class if selected in UI
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->paginate(20);
        $myClasses = \App\Models\Classes::whereIn('id', $allClassIds)->get();

        return view('teacher.students', compact('students', 'myClasses'));
    }

    public function myTimetable()
    {
        $user = auth()->user();

        // Check if the user actually has a teacher profile
        // Assuming your User model has a hasOne relationship named 'teacher'
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found for this account.');
        }

        $days = \App\Models\WeekDay::orderBy('sort_order')->get();
        $slots = \App\Models\TimeSlot::orderBy('start_time')->get();

        // Now it is safe to use $teacher->id
        $entries = \App\Models\ClassTimetable::with(['subject', 'class'])
            ->where('teacher_id', $teacher->id)
            ->get();

        $timetableData = [];
        foreach ($entries as $entry) {
            $timetableData[$entry->week_day_id][$entry->time_slot_id] = $entry;
        }

        return view('teacher.timetable', compact('days', 'slots', 'timetableData', 'teacher'));
    }

    public function exam_timetable(Request $request)
    {
        $user = auth()->user();
        $teacherProfile = $user->teacher; // Consistent with your 'teacher' relationship

        if (!$teacherProfile) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        // Fetch schedules for classes assigned to this teacher
        $schedules = ExamSchedule::whereHas('class.classTeachers', function ($q) use ($teacherProfile) {
            $q->where('teacher_id', $teacherProfile->id);
        })
            ->with(['exam', 'class', 'subject'])
            ->orderBy('exam_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('exam.name'); // Group by Exam (e.g., First Term, Final Term)

        return view('teacher.exam_timetable', compact('schedules'));
    }
}
