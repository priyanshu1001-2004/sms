<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\TimetableGroup;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $groups = TimetableGroup::where('organization_id', $orgId)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        $query = TimeSlot::with('group')->where('organization_id', $orgId);

        // Apply Filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('group_id')) {
            $query->where('timetable_group_id', $request->group_id);
        }

        if ($request->filled('is_break')) {
            $query->where('is_break', $request->is_break);
        }

        // Use paginate instead of get
        $slots = $query->orderBy('timetable_group_id')
            ->orderBy('start_time', 'asc')
            ->paginate(10) // Shows 15 records per page
            ->withQueryString(); // CRITICAL: This keeps filters active when clicking page 2, 3, etc.

        return view('pages.time_slots.index', compact('slots', 'groups'));
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
            'timetable_group_id' => 'required|exists:timetable_groups,id',
            'name'               => 'required|string|max:255',
            'start_time'         => 'required',
            'end_time'           => 'required|after:start_time',
            'is_break'           => 'nullable|boolean'
        ]);

        $orgId = auth()->user()->organization_id;
        $groupId = $request->timetable_group_id;
        $start = $request->start_time;
        $end = $request->end_time;

        $overlap = TimeSlot::where('organization_id', $orgId)
            ->where('timetable_group_id', $groupId)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('start_time', '<=', $start)
                        ->where('end_time', '>', $start);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                        ->where('end_time', '>=', $end);
                });
            })->exists();

        if ($overlap) {
            return response()->json([
                'status' => false,
                'message' => 'This time slot overlaps with an existing slot in the selected group.'
            ], 422);
        }

        TimeSlot::create([
            'organization_id'    => $orgId,
            'timetable_group_id' => $groupId,
            'name'               => $request->name,
            'start_time'         => $start,
            'end_time'           => $end,
            'is_break'           => $request->is_break ?? false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Time slot registered and validated successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'timetable_group_id' => 'required|exists:timetable_groups,id',
            'name'               => 'required|string|max:255',
            'start_time'         => 'required',
            'end_time'           => 'required|after:start_time',
            'is_break'           => 'nullable|boolean'
        ]);

        $orgId = auth()->user()->organization_id;
        $groupId = $request->timetable_group_id;
        $start = $request->start_time;
        $end = $request->end_time;

        // --- OVERLAP VALIDATION (Ignoring Current ID) ---
        $overlap = TimeSlot::where('organization_id', $orgId)
            ->where('timetable_group_id', $groupId)
            ->where('id', '!=', $id) // CRITICAL: Ignore this record
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('start_time', '<=', $start)
                        ->where('end_time', '>', $start);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                        ->where('end_time', '>=', $end);
                });
            })->exists();

        if ($overlap) {
            return response()->json([
                'status' => false,
                'message' => 'Update failed: This time conflicts with another existing slot.'
            ], 422);
        }

        $slot = TimeSlot::where('organization_id', $orgId)->findOrFail($id);

        $slot->update([
            'timetable_group_id' => $groupId,
            'name'               => $request->name,
            'start_time'         => $start,
            'end_time'           => $end,
            'is_break'           => $request->is_break ?? false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Time slot updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
