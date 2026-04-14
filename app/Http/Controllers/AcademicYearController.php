<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::where('organization_id', currentOrgId())->paginate(10);


        return view('pages.academic_years.index', compact('academicYears'));
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
        $validated = $request->validate([
            'name'            => 'required|string',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'is_active'       => 'boolean'
        ]);

        // Use your helper here
        $orgId = currentOrgId();


        if (!$orgId) {
            return response()->json(['status' => 'error', 'message' => 'Organization context not found.'], 422);
        }

        $validated['organization_id'] = $orgId;

        DB::beginTransaction();
        try {
            if ($request->is_active) {
                AcademicYear::where('organization_id', $validated['organization_id'])
                    ->update(['is_active' => 0]);
            }

            $academicYear = AcademicYear::create($validated);

            auditLog(
                module: 'AcademicYear',
                action: 'Create',
                recordId: $academicYear->id,
                description: "New academic year {$academicYear->name} created."
            );

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Academic Year created.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'nullable|boolean'
        ]);

        $academicYear = AcademicYear::findOrFail($id);
        $orgId = $academicYear->organization_id; 

        DB::beginTransaction();
        try {
            // Convert checkbox 'on' or null to boolean
            $isActive = $request->has('is_active') ? 1 : 0;
            $validated['is_active'] = $isActive;

            // Logic: If this year is being set to Active, deactivate others
            if ($isActive) {
                AcademicYear::where('organization_id', $orgId)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => 0]);
            }

            $academicYear->update($validated);

            auditLog(
                module: 'AcademicYear',
                action: 'Update',
                recordId: $id,
                description: "Updated academic year: {$academicYear->name}"
            );

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Academic Year updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        //
    }
}
