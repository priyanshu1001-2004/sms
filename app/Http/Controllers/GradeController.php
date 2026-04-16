<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grades = Grade::orderBy('percent_from', 'desc')->get();
        return view('pages.grades.index', compact('grades'));
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
            'name' => 'required|string|max:10',
            'percent_from' => 'required|numeric|min:0|max:100',
            'percent_to' => 'required|numeric|min:0|max:100|gt:percent_from',
            'grade_point' => 'required|numeric|min:0|max:10',
        ]);

        Grade::create($request->all());

        return response()->json(['status' => true, 'message' => 'Grade scale added successfully!']);
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
    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:10',
            'percent_from' => 'required|numeric',
            'percent_to' => 'required|numeric|gt:percent_from',
        ]);

        $grade->update($request->all());
        return response()->json(['status' => true, 'message' => 'Grade scale updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();
        return response()->json(['status' => true, 'message' => 'Grade deleted!']);
    }
}
