<?php

namespace App\Http\Controllers;

use App\Models\TimetableGroup;
use Illuminate\Http\Request;

class TimetableGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = TimetableGroup::orderBy('id', 'desc')->get();
        return view('pages.timetable_groups.index', compact('groups'));
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:morning,afternoon,evening,full_day'
        ]);

        TimetableGroup::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'type' => $request->type,
            'status' => 1
        ]);

        return response()->json(['status' => true, 'message' => 'Profile created successfully']);
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
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $group = TimetableGroup::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($id);

        $statusValue = ($request->status == 'active') ? 1 : 0;

        $group->update([
            'name'   => $request->name,
            'status' => $statusValue,
            'type' => $request->type,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Schedule Profile updated successfully'
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
