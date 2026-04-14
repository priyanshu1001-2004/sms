<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = Board::paginate(10);
        return view('pages.boards.index', compact('boards'));
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
        // 1. Log or Check the currentOrgId() to ensure it's not null
        $orgId = currentOrgId();

        $validated = $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('boards', 'name')
                    ->where('organization_id', $orgId)
                    ->whereNull('deleted_at')
            ],
            'short_name' => [
                'nullable',
                'max:20',
                Rule::unique('boards', 'short_name')
                    ->where('organization_id', $orgId)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $code = CodeGenerator('boards', 'code', 'BRD', 4);

        DB::beginTransaction();
        try {
            $validated['organization_id'] = $orgId;
            $validated['code'] = $code;

            Board::create($validated);

            DB::commit();
            return response()->json(['message' => 'Board created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while creating the board: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Board $board)
    {
        $orgId = currentOrgId();

        $validated = $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('boards', 'name')
                    ->ignore($board->id) // Ignores current record
                    ->where('organization_id', $orgId)
                    ->whereNull('deleted_at')
            ],
            'short_name' => [
                'nullable',
                'max:20',
                Rule::unique('boards', 'short_name')
                    ->ignore($board->id) // Ignores current record
                    ->where('organization_id', $orgId)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $board->update($validated);
            DB::commit();
            return response()->json(['message' => 'Board updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while updating the board: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        try {
            $board->delete();
            return response()->json(['message' => 'Board deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the board: ' . $e->getMessage()], 500);
        }
    }

    
}
