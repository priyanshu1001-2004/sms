<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Classes;
use App\Models\TimetableGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Classes::with(['timetableGroup']);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(code) like ?', ["%{$search}%"]);
            });
        }

        if ($request->filled('group_id')) {
            $query->where('timetable_group_id', $request->group_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $classes = $query->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $groups = TimetableGroup::where('status', 1)->get();

        return view('pages.classes.index', compact('classes', 'groups'));
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
            'timetable_group_id' => 'required|exists:timetable_groups,id', // Group validation
            'status'             => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $generatedCode = CodeGenerator('classes', 'code', 'CLS-', 4, currentOrgId());

            $class = Classes::create([
                'organization_id'    => currentOrgId(),
                'timetable_group_id' => $request->timetable_group_id,
                'code'               => $generatedCode,
                'name'               => trim($request->name),
                'status'             => $request->status,
                'created_by'         => auth()->id(),
            ]);

            // Audit Log for Creation
            auditLog(
                module: 'Classes',
                action: 'Create',
                recordId: $class->id,
                new: $class->toArray(),
                description: "Created new class: {$class->name} with group ID: {$request->timetable_group_id}",
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
            'timetable_group_id' => 'required|exists:timetable_groups,id', // Group validation
            'status'             => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $class->only(['name', 'status', 'timetable_group_id']);

            $class->update([
                'name'               => trim($request->name),
                'timetable_group_id' => $request->timetable_group_id,
                'status'             => $request->status,
            ]);

            $newData = $class->only(['name', 'status', 'timetable_group_id']);

            auditLog(
                module: 'Classes',
                action: 'Update',
                recordId: $class->id,
                old: $oldData,
                new: $newData,
                description: "Updated class: {$class->name} and assigned schedule group",
                event: 'class_updated'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Class updated successfully']);
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
