<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\ParentModal;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parents = ParentModal::with('user', 'creator')->paginate(10);
        return view('pages.parents.index', compact('parents'));
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
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'mobile_number' => 'required|string',
            'password'      => 'required|min:6',
            'relation'      => 'required',
            'parent_photo'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $orgId = currentOrgId();

            // 1. Handle Photo Upload
            $photoPath = null;
            if ($request->hasFile('parent_photo')) {
                $file = $request->file('parent_photo');
                $fileName = time() . '_parent.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/parents', $fileName, 'public');
            }

            // 2. Create User Account for Login
            $user = User::create([
                'name'            => trim($request->first_name . ' ' . $request->last_name),
                'email'           => $request->email,
                'phone'           => $request->mobile_number,
                'password'        => Hash::make($request->password),
                'organization_id' => $orgId,
                'user_type'       => 'parent',
            ]);

            $user->assignRole('parent');

            // 3. Create Parent Profile
            ParentModal::create([
                'user_id'         => $user->id,
                'organization_id' => $orgId,
                'first_name'      => trim($request->first_name),
                'last_name'       => trim($request->last_name),
                'gender'          => $request->gender,
                'email'           => $request->email,
                'mobile_number'   => $request->mobile_number,
                'occupation'      => $request->occupation,
                'relation'        => $request->relation,
                'parent_photo'    => $photoPath,
                'status'          => 1,
                'created_by'      => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Parent admitted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentModal $parentModal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentModal $parentModal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $parent = ParentModal::findOrFail($id);

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $parent->user_id,
            'mobile_number' => 'required',
            'relation'      => 'required',
        ]);

        DB::beginTransaction();
        try {
            $photoPath = $parent->parent_photo;

            if ($request->hasFile('parent_photo')) {
                if ($photoPath) Storage::disk('public')->delete($photoPath);

                $file = $request->file('parent_photo');
                $fileName = time() . '_parent.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/parents', $fileName, 'public');
            }

            $parent->update([
                'first_name'    => trim($request->first_name),
                'last_name'     => trim($request->last_name),
                'email'         => $request->email,
                'mobile_number' => $request->mobile_number,
                'occupation'    => $request->occupation,
                'relation'      => $request->relation,
                'parent_photo'  => $photoPath,
            ]);

            // Sync to User Account
            if ($parent->user) {
                $parent->user->update([
                    'name'  => trim($request->first_name . ' ' . $request->last_name),
                    'email' => $request->email,
                    'phone' => $request->mobile_number,
                ]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Parent updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $parent = ParentModal::findOrFail($id);

        DB::beginTransaction();
        try {
            $parent->delete();

            if ($parent->user) {
                $parent->user->update(['status' => 0]);
            }

            // 3. Audit Log
            auditLog(
                module: 'Parents',
                action: 'Delete',
                recordId: $id,
                description: "Deleted parent: {$parent->first_name} {$parent->last_name}",
                event: 'parent_deleted'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Parent deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        // Global scope handles the organization_id check automatically
        $parent = ParentModal::with('user')->findOrFail($id);

        try {
            $newStatus = $parent->status == 1 ? 0 : 1;

            // Update Parent Record
            $parent->update(['status' => $newStatus]);

            // Sync with User Account so they can/cannot login
            if ($parent->user) {
                $parent->user->update(['status' => $newStatus]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        // 1. Validation
        // 'confirmed' looks for 'password_confirmation' in your request
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // 2. Find the User (The $id comes from the URL)
            $user = User::findOrFail($id);

            // 3. SaaS Security: Ensure the user belongs to the logged-in admin's school
            if ($user->organization_id !== currentOrgId()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You cannot change passwords for other organizations.'
                ], 403);
            }

            // 4. Update the Password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // 5. Optional: Log the action
            auditLog(
                module: 'User Management',
                action: 'Password Change',
                recordId: $user->id,
                description: "Password reset performed for: {$user->name} ({$user->user_type})",
                event: 'password_updated'
            );

            return response()->json([
                'status' => true,
                'message' => "Password updated successfully for {$user->name}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStudents($id)
    {
        $parent = ParentModal::with('students.class')->findOrFail($id);
        return response()->json(['students' => $parent->students]);
    }

    public function parent_students()
    {
        $user = auth()->user();
        $parent = \App\Models\ParentModal::where('user_id', $user->id)->firstOrFail();

        $students = \App\Models\Student::with([
            'class.classTeacher.teacher', // Eager load the Head Class Teacher
        ])
            ->where('parent_id', $parent->id)
            ->get();

        return view('parent.students', compact('students'));
    }

    public function getStudentSubjects($student_id)
    {
        $student = \App\Models\Student::findOrFail($student_id);

        // Fetch curriculum and join with the SubjectTeacher assignment
        $subjects = \App\Models\ClassSubject::with([
            'subject',
            'assignedTeacher.teacher' // Get the specific teacher for this subject
        ])
            ->where('class_id', $student->class_id)
            ->get();

        return response()->json(['data' => $subjects]);
    }

   
}
