<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $classes = Classes::paginate(10);
        return view('pages.classes.index', compact('classes'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where('organization_id', currentOrgId())
            ],
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $generatedCode = CodeGenerator('classes', 'code', 'CLS-', 4, currentOrgId());

            $class = Classes::create([
                'organization_id' => currentOrgId(),
                'code'            => $generatedCode,
                'name'            => trim($request->name),
                'status'          => $request->status,
                'created_by'      => auth()->id(),
            ]);

            // Audit Log for Creation
            auditLog(
                module: 'Classes',
                action: 'Create',
                recordId: $class->id,
                new: $class->toArray(),
                description: "Created new class: {$class->name} with code {$generatedCode}",
                event: 'class_created'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Class created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classes $classes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classes $classes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where('organization_id', currentOrgId())->ignore($id)
            ],
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $class->only(['name', 'status']);

            $class->update([
                'name'   => trim($request->name),
                'status' => $request->status,
            ]);

            $newData = $class->only(['name', 'status']);

            auditLog(
                module: 'Classes',
                action: 'Update',
                recordId: $class->id,
                old: $oldData,
                new: $newData,
                description: "Updated class: {$class->name}",
                event: 'class_updated'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Class updated successfully']);
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
        // 1. Find the class (Global Scope ensures it belongs to the current org)
        $class = Classes::findOrFail($id);

        DB::beginTransaction();
        try {
            // 2. Capture data for Audit Log before deletion
            $oldData = $class->toArray();

            // 3. Perform Soft Delete
            $class->delete();

            // 4. Audit Log
            auditLog(
                module: 'Classes',
                action: 'Delete',
                recordId: $id,
                old: $oldData,
                description: "Deleted class: {$class->name} (Code: {$class->code})",
                event: 'class_deleted'
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Class moved to trash successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
