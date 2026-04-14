<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassTeacher;
use Illuminate\Http\Request;

class ClassTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orgId = currentOrgId();

        // Get all classes in this org
        $classes = Classes::where('organization_id', $orgId)->get();

        // Get all active teachers in this org
        $teachers = \App\Models\Teacher::where('organization_id', $orgId)
            ->where('status', 1)
            ->get();

        // Get existing assignments with relationships
        $assignments = \App\Models\ClassTeacher::with(['schoolClass', 'teacher'])
            ->where('organization_id', $orgId)
            ->get();

        return view('pages.class_teachers.index', compact('classes', 'teachers', 'assignments'));
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
            'class_id' => [
                'required',
                // Ensure this class doesn't already have a teacher in this org
                \Illuminate\Validation\Rule::unique('class_teachers')->where('organization_id', $orgId)
            ],
            'teacher_id' => 'required|exists:teachers,id',
        ], [
            'class_id.unique' => 'This class already has an assigned Class Teacher.'
        ]);

        \App\Models\ClassTeacher::create([
            'organization_id' => $orgId,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'is_primary' => true
        ]);

        return response()->json(['status' => true, 'message' => 'Class Teacher assigned successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassTeacher $classTeacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassTeacher $classTeacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassTeacher $classTeacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $assignment = \App\Models\ClassTeacher::findOrFail($id);
        $assignment->delete();

        return response()->json(['status' => true, 'message' => 'Assignment removed.']);
    }
}
