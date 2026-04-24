<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacher_assignments = TeacherSubject::with(['teacher', 'class', 'subject'])
            ->latest()
            ->paginate(15);

        $classList = Classes::orderBy('name', 'asc')->get();
        $teachers = User::role('teacher')->get();
        $subjectList = Subject::where('status', 1)->orderBy('name', 'asc')->get();

        return view('pages.teacher_subjects.index', compact(
            'teacher_assignments',
            'classList',
            'teachers',
            'subjectList'
        ));
    }

    public function getClassSubjectsForTeacher(Request $request)
    {
        $classId = $request->class_id;
        $teacherId = $request->teacher_id;

        // 1. Get all subjects that belong to this Class (from your class_subjects table)
        $classSubjects = ClassSubject::where('class_id', $classId)
            ->with('subject:id,name')
            ->get()
            ->pluck('subject');

        // 2. Get IDs of subjects already assigned to THIS teacher for THIS class
        $assignedIds = TeacherSubject::where('class_id', $classId)
            ->where('teacher_id', $teacherId)
            ->pluck('subject_id')
            ->toArray();

        return response()->json([
            'subjects' => $classSubjects,
            'assigned_ids' => $assignedIds
        ]);
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
            'teacher_id' => 'required',
            'class_id'   => 'required',
            'subject_id' => 'required|array',
        ]);

        $orgId = auth()->user()->organization_id;

        TeacherSubject::where('organization_id', $orgId)
            ->where('teacher_id', $request->teacher_id)
            ->where('class_id', $request->class_id)
            ->delete();

        foreach ($request->subject_id as $subId) {
            TeacherSubject::create([
                'organization_id' => $orgId,
                'teacher_id'      => $request->teacher_id,
                'class_id'        => $request->class_id,
                'subject_id'      => $subId,
                'status'          => $request->status ?? 1,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher assigned to subjects successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSubject $teacherSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherSubject $teacherSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherSubject $teacherSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $assignment = TeacherSubject::findOrFail($id);

        $deletedCount = TeacherSubject::where('teacher_id', $assignment->teacher_id)
            ->where('class_id', $assignment->class_id)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Successfully removed $deletedCount subject assignments for this teacher."
        ]);
    }

    // In TeacherSubjectController.php
    public function getQualifiedTeachers(Request $request)
    {
        $classId = $request->class_id;
        $subjectId = $request->subject_id;

        // We look for teachers mapped to THIS class and THIS subject
        $assignments = TeacherSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('status', 1)
            ->with('teacher:id,name') // Get the user object with name
            ->get();

        // Extract the teacher objects
        $teachers = $assignments->pluck('teacher')->filter();

        return response()->json([
            'teachers' => $teachers
        ]);
    }
}
