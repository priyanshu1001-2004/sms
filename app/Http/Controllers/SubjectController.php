<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::paginate(10);
        return view('pages.subjects.index', compact('subjects'));
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
            'name' => ['required', 'string', 'max:255', Rule::unique('subjects')->where('organization_id', currentOrgId())],
            'type' => 'required|string',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Generate SUBJ-0001
            $code = CodeGenerator('subjects', 'code', 'SUBJ-', 4, currentOrgId());

            $subject = Subject::create([
                'organization_id' => currentOrgId(),
                'code' => $code,
                'name' => trim($request->name),
                'type' => $request->type,
                'status' => $request->status,
            ]);

            auditLog('Subjects', 'Create', $subject->id, null, $subject->toArray(), "Created subject: {$subject->name}");

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Subject created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // findOrFail respects the Global Scope (tenant security)
        $subject = Subject::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->where('organization_id', currentOrgId())->ignore($id)
            ],
            'type'   => 'required|string',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $subject->only(['name', 'type', 'status']);

            $subject->update([
                'name'   => trim($request->name),
                'type'   => $request->type,
                'status' => $request->status,
            ]);

            $newData = $subject->only(['name', 'type', 'status']);

            // Audit Log
            auditLog(
                module: 'Subjects',
                action: 'Update',
                recordId: $subject->id,
                old: $oldData,
                new: $newData,
                description: "Updated subject: {$subject->name}",
                event: 'subject_updated'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Subject updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldData = $subject->toArray();

            $subject->delete(); // Soft Delete

            auditLog(
                module: 'Subjects',
                action: 'Delete',
                recordId: $id,
                old: $oldData,
                description: "Deleted subject: {$subject->name} (Code: {$subject->code})",
                event: 'subject_deleted'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Subject deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Delete failed'], 500);
        }
    }
}
