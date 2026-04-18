<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Admin;
use App\Models\ParentModal;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userType = $user->user_type;

        // dd($userType);

        // Check if user exists first to avoid accidental 404s
        try {
            return match ($userType) {
                'teacher'     => $this->teacherProfile($user),
                'student'     => $this->studentProfile($user),
                'parent'      => $this->parentProfile($user),
                'admin'       => $this->adminProfile($user),
                'master_admin' => view('pages.profile.master_admin', [
                    'user' => $user,
                    'org'  => $user->organization 
                ]),
                'super_admin' => view('pages.profile.super_admin', compact('user')),
                default       => abort(404, "User type not recognized"),
            };
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Profile record not found in the ' . $userType . ' table.');
        }
    }

    private function teacherProfile($user)
    {
        $profile = Teacher::where('user_id', $user->id)->firstOrFail();
        return view('pages.profile.teacher', compact('user', 'profile'));
    }

    private function studentProfile($user)
    {
        $profile = Student::with('class')->where('user_id', $user->id)->firstOrFail();
        return view('pages.profile.student', compact('user', 'profile'));
    }

    private function parentProfile($user)
    {
        $profile = ParentModal::where('user_id', $user->id)->firstOrFail();
        return view('parent.profile', compact('user', 'profile'));
    }

    private function adminProfile($user)
    {
        $profile = Admin::where('user_id', $user->id)->firstOrFail();
        return view('pages.profile.admin', compact('user', 'profile'));
    }

    // --- Update Profiles ---

    public function updateTeacher(Request $request)
    {
        // 1. Validation for Teacher-specific fields
        $request->validate([
            'mobile_number'            => 'required|string|max:20',
            'emergency_contact_number' => 'nullable|string|max:20',
            'marital_status'           => 'nullable|string',
            'blood_group'              => 'nullable|string',
            'current_address'          => 'required|string',
            'permanent_address'        => 'nullable|string',
            'teacher_photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $photoPath = $teacher->teacher_photo;

            // 2. Profile Photo Management
            if ($request->hasFile('teacher_photo')) {
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('teacher_photo')->store('uploads/teachers/photos', 'public');
            }

            // 3. Update Teacher Table (Excludes designation, salary, epf etc. for security)
            $teacher->update([
                'mobile_number'            => $request->mobile_number,
                'emergency_contact_number' => $request->emergency_contact_number,
                'marital_status'           => $request->marital_status,
                'blood_group'              => $request->blood_group,
                'current_address'          => $request->current_address,
                'permanent_address'        => $request->permanent_address,
                'teacher_photo'            => $photoPath,
            ]);

            // 4. Update Main User Table (Phone sync)
            $user->update([
                'phone' => $request->mobile_number,
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Professional profile updated!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Profile Update Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateStudent(Request $request)
    {
        // 1. Validation
        $request->validate([
            'mobile_number' => 'required|string|max:20',
            'blood_group'   => 'nullable|string|max:10',
            'religion'      => 'nullable|string|max:100',
            'caste'         => 'nullable|string|max:100',
            'student_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $photoPath = $student->student_photo;

            // 2. Handle Photo Update
            if ($request->hasFile('student_photo')) {
                // Delete old photo if it exists
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                // Store new photo
                $file = $request->file('student_photo');
                $fileName = time() . '_profile_' . $user->id . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/students', $fileName, 'public');
            }

            // 3. Update Student Profile Table
            $student->update([
                'mobile_number' => $request->mobile_number,
                'blood_group'   => $request->blood_group,
                'religion'      => $request->religion,
                'caste'         => $request->caste,
                'student_photo' => $photoPath,
            ]);

            // 4. Sync common data to User table
            $user->update([
                'phone' => $request->mobile_number,
            ]);

            // 5. Audit Log (Optional but recommended)
            auditLog(
                module: 'Profile',
                action: 'Update',
                recordId: $student->id,
                description: "Student updated their own profile settings.",
                event: 'profile_self_update'
            );

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Profile updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    public function updateParent(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'address'       => 'nullable|string',
        ]);

        $user = auth()->user();
        $parent = \App\Models\ParentModal::where('user_id', $user->id)->firstOrFail();

        DB::beginTransaction();
        try {
            // Update Parent Table
            $parent->update([
                'mobile_number' => $request->mobile_number,
                'occupation'    => $request->occupation,
                'address'       => $request->address,
            ]);

            // Sync with User Table
            $user->update(['phone' => $request->mobile_number]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Parent profile updated!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateAdmin(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'description' => 'nullable|string', // Added validation for description
        ]);

        $user = auth()->user();

        // Safety check: Ensure we only update the admin belonging to this user's email AND org
        $admin = \App\Models\Admin::where('email', $user->email)
            ->where('organization_id', currentOrgId())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // 1. Update Admin Table
            $admin->update([
                'name'        => $request->name,
                'phone'       => $request->phone,
                'description' => $request->description,
            ]);

            // 2. Update User Table (Sync Name and Phone)
            $user->update([
                'name'  => $request->name,
                'phone' => $request->phone,
            ]);

            DB::commit();

            // Natural human-like touch: return a friendly message
            return response()->json([
                'status' => true,
                'message' => 'Admin profile updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMaster(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        try {
            $user = auth()->user();

            // Update user record
            $user->update([
                'name'  => $request->name,
                'phone' => $request->phone,
            ]);

            return response()->json(['status' => true, 'message' => 'Master profile updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSuperAdmin(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return response()->json(['status' => true, 'message' => 'System credentials updated!']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function updatePassword(Request $request)
    {
        // 1. Validation
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = auth()->user();

            // 2. Update the Password in the Users table
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Your security password has been updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }



    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
