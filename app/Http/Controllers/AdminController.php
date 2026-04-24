<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::paginate(10);

        return view('pages.admins.index', compact('admins'));
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
        $ordId = currentOrgId();

        $validated = $request->validate([
            'name' => 'required|string|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->where('organization_id', $ordId),
                Rule::unique('users', 'email'),
            ],
            'phone' => [
                'required',
                Rule::unique('admins', 'phone')->where('organization_id', $ordId),
            ],
            'password' => 'required|min:6',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $hashedPassword = Hash::make($validated['password']);

            $user = User::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'],
                'password'        => $hashedPassword,
                'username'        => $validated['email'],
                'organization_id' => $ordId,
                'user_type'       => 'admin',
            ]);

            $user->assignRole('admin');

            $admin = Admin::create([
                'user_id'         => $user->id, // Linking the two tables
                'organization_id' => $ordId,
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'phone'           => $validated['phone'],
                'password'        => $hashedPassword,
                'status'          => $validated['status'],
                'description'     => $validated['description'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully',
                'data'    => $admin
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }



    public function update(Request $request, $id)
    {
        $ordId = currentOrgId();

        $validated = $request->validate([
            'name' => 'required|string|min:3',

            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')
                    ->where('organization_id', $ordId)
                    ->ignore($id),
            ],

            'phone' => [
                'required',
                Rule::unique('admins', 'phone')
                    ->where('organization_id', $ordId)
                    ->ignore($id),
            ],

            'status' => 'required|boolean',
            'description' => 'nullable|string',
            'password' => 'nullable|min:6',
        ]);

        DB::beginTransaction();

        try {

            // 1. Find Admin
            $admin = Admin::where('organization_id', $ordId)
                ->findOrFail($id);

            // 2. Update Admin table
            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            $admin->phone = $validated['phone'];
            $admin->status = $validated['status'];
            $admin->description = $validated['description'] ?? null;

            if (!empty($validated['password'])) {
                $admin->password = Hash::make($validated['password']);
            }

            $admin->save();

            // 3. Update USER table also (IMPORTANT SYNC STEP)
            $user = User::where('email', $admin->email)->first();

            if ($user) {

                $user->name = $admin->name;
                $user->email = $admin->email;
                $user->phone = $admin->phone;

                if (!empty($validated['password'])) {
                    $user->password = $admin->password;
                }

                $user->organization_id = $ordId;
                $user->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully',
                'data' => $admin
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
