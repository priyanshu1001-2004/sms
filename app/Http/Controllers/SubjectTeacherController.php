<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get classes to filter the view
        $classes = Classes::all();
        $teachers = Teacher::where('status', 1)->get();

        // Get all active assignments with relations
        $assignments = SubjectTeacher::with(['classSubject.subject', 'classSubject.class', 'teacher'])->paginate(20);

        return view('pages.subject_teachers.index', compact('classes', 'teachers', 'assignments'));
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
        $orgId = currentOrgId();

        $request->validate([
            'class_subject_id' => [
                'required',
                'exists:class_subjects,id',
                \Illuminate\Validation\Rule::unique('subject_teachers')
                    ->where('organization_id', $orgId)
            ],
            'teacher_id' => 'required|exists:teachers,id',
        ], [
            'class_subject_id.unique' => 'A teacher is already assigned to this subject for the selected class.'
        ]);

        try {
            \App\Models\SubjectTeacher::create([
                'organization_id' => $orgId,
                'class_subject_id' => $request->class_subject_id,
                'teacher_id' => $request->teacher_id,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Teacher assigned successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubjectTeacher $subjectTeacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubjectTeacher $subjectTeacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubjectTeacher $subjectTeacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // IMPORTANT: Change ClassSubject to SubjectTeacher
        $assign = \App\Models\SubjectTeacher::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldData = $assign->toArray();

            // This will perform a Soft Delete because you added 'use SoftDeletes' to the model
            $assign->delete();

            // Audit Log for Subject Teacher Removal
            if (function_exists('auditLog')) {
                auditLog(
                    module: 'SubjectTeacher',
                    action: 'Delete',
                    recordId: $id,
                    old: $oldData,
                    new: null,
                    description: "Removed teacher assignment from class subject."
                );
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Teacher unassigned successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Action failed: ' . $e->getMessage()], 500);
        }
    }

    public function getSubjectsByClass($classId)
    {
        $subjects = ClassSubject::with('subject')
            ->where('class_id', $classId)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $subjects
        ]);
    }
}
