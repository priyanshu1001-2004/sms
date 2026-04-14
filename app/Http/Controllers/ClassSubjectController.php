<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classes::all();
        $subjects = Subject::all();

        $assign_subjects = ClassSubject::with(['class', 'subject'])->paginate(15);

        return view('pages.assign_subjects.index', compact('classes', 'subjects', 'assign_subjects'));
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


    // ClassSubjectController.php

    public function getAssignedSubjects($classId)
    {
        $assignedIds = ClassSubject::where('class_id', $classId)
            ->pluck('subject_id')
            ->toArray();

        return response()->json([
            'status' => true,
            'assigned_ids' => $assignedIds
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $orgId = currentOrgId();
            $classId = $request->class_id;
            $selectedSubjects = $request->subject_id;

            // 1. SOFT DELETE records that were UNCHECKED
            ClassSubject::where('organization_id', $orgId)
                ->where('class_id', $classId)
                ->whereNotIn('subject_id', $selectedSubjects)
                ->delete();

            foreach ($selectedSubjects as $subId) {
                $assignment = ClassSubject::withTrashed()
                    ->where('organization_id', $orgId)
                    ->where('class_id', $classId)
                    ->where('subject_id', $subId)
                    ->first();

                if ($assignment) {
                    if ($assignment->trashed()) $assignment->restore();

                    $assignment->update([
                        // Use request status if available, else keep existing
                        'status' => $request->has('status') ? $request->status : $assignment->status,
                        'created_by' => auth()->id()
                    ]);
                } else {
                    ClassSubject::create([
                        'organization_id' => $orgId,
                        'class_id' => $classId,
                        'subject_id' => $subId,
                        'status' => $request->status ?? 1,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Assignments synchronized successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Internal Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSubject $classSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSubject $classSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $assignment = ClassSubject::findOrFail($id);

        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();
        try {
            $old = $assignment->only(['status']);

            $assignment->update([
                'status' => $request->status
            ]);

            $new = $assignment->only(['status']);

            auditLog(
                module: 'AssignSubjects',
                action: 'Update',
                recordId: $assignment->id,
                old: $old,
                new: $new,
                description: "Updated status for assignment ID: {$id}",
                event: 'assignment_status_updated'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Assignment updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Update failed'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $assign = ClassSubject::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldData = $assign->toArray();
            $assign->delete();

            auditLog('AssignSubjects', 'Delete', $id, $oldData, null, "Unassigned subject from class");

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Subject unassigned successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Action failed'], 500);
        }
    }

    public function toggleStatus($id)
    {
        $assignment = ClassSubject::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldStatus = $assignment->status;
            $newStatus = $oldStatus == 1 ? 0 : 1;

            $assignment->update([
                'status' => $newStatus
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Update failed'], 500);
        }
    }

    public function getSubjectsByClass($classId)
    {
        // Important: Use 'subject' relationship so the JS can find 'item.subject.name'
        $subjects = ClassSubject::with('subject')
            ->where('class_id', $classId)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $subjects // This must match your JS 'response.data'
        ]);
    }
}
