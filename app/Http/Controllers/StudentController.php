<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ParentModal;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::paginate(10);
        $classes  = Classes::all();
        $allParents = ParentModal::all();
        return view('pages.students.index', compact('students', 'classes', 'allParents'));
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
            'admission_number' => 'required|string|max:255',
            'class_id'         => 'required|exists:classes,id',
            'roll_number'      => 'required|string|max:50', // Added roll number validation
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'gender'           => 'required|string',
            'date_of_birth'    => 'required|date',
            'admission_date'   => 'required|date',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|min:6',
            'student_photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $orgId = currentOrgId();

            // 1. Check for Duplicate Admission Number in Org
            $exists = Student::where('organization_id', $orgId)
                ->where('admission_number', $request->admission_number)
                ->exists();

            if ($exists) {
                return response()->json(['status' => false, 'message' => 'Admission number already exists.'], 422);
            }

            // 2. NEW: Check for Duplicate Roll Number in the SAME Class
            $rollExists = Student::where('organization_id', $orgId)
                ->where('class_id', $request->class_id)
                ->where('roll_number', $request->roll_number)
                ->exists();

            if ($rollExists) {
                return response()->json(['status' => false, 'message' => 'This Roll Number is already assigned to another student in this class.'], 422);
            }

            $photoPath = null;
            if ($request->hasFile('student_photo')) {
                $file = $request->file('student_photo');
                $fileName = time() . '_' . $request->admission_number . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/students', $fileName, 'public');
            }

            $user = User::create([
                'name'            => trim($request->first_name . ' ' . $request->last_name),
                'email'           => $request->email,
                'phone'           => $request->mobile_number,
                'password'        => Hash::make($request->password),
                'organization_id' => $orgId,
                'user_type'       => 'student',
            ]);

            $user->assignRole('student');

            $student = Student::create([
                'user_id'          => $user->id,
                'organization_id'  => $orgId,
                'admission_number' => $request->admission_number,
                'roll_number'      => $request->roll_number,
                'class_id'         => $request->class_id,
                'first_name'       => trim($request->first_name),
                'last_name'        => trim($request->last_name),
                'gender'           => $request->gender,
                'date_of_birth'    => $request->date_of_birth,
                'admission_date'   => $request->admission_date,
                'religion'         => $request->religion,
                'caste'            => $request->caste,
                'mobile_number'    => $request->mobile_number,
                'blood_group'      => $request->blood_group,
                'email'            => $request->email,
                'student_photo'    => $photoPath,
                'status'           => $request->status ?? 1,
                'created_by'       => auth()->id(),
            ]);

            auditLog(module: 'Students', action: 'Admission', recordId: $student->id, new: $student->toArray(), description: "Admitted student: {$user->name}", event: 'student_admission');

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Student admitted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($photoPath)) Storage::disk('public')->delete($photoPath);
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'admission_number' => "required|string|max:255|unique:students,admission_number,{$id},id,organization_id," . currentOrgId(),
            'class_id'         => 'required|exists:classes,id',
            'roll_number'      => 'required|string|max:50',
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'gender'           => 'required|string',
            'date_of_birth'    => 'required|date',
            'admission_date'   => 'required|date',
            'student_photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $orgId = currentOrgId();

            // NEW: Check Roll Number Uniqueness in class (excluding current student)
            $rollExists = Student::where('organization_id', $orgId)
                ->where('class_id', $request->class_id)
                ->where('roll_number', $request->roll_number)
                ->where('id', '!=', $id)
                ->exists();

            if ($rollExists) {
                return response()->json(['status' => false, 'message' => 'This Roll Number is already taken by another student in this class.'], 422);
            }

            $oldPhoto = $student->student_photo;
            $photoPath = $oldPhoto;

            if ($request->hasFile('student_photo')) {
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
                $file = $request->file('student_photo');
                $fileName = time() . '_' . $request->admission_number . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/students', $fileName, 'public');
            }

            $student->update([
                'admission_number' => $request->admission_number,
                'roll_number'      => $request->roll_number,
                'class_id'         => $request->class_id,
                'first_name'       => trim($request->first_name),
                'last_name'        => trim($request->last_name),
                'gender'           => $request->gender,
                'date_of_birth'    => $request->date_of_birth,
                'admission_date'   => $request->admission_date,
                'religion'         => $request->religion,
                'caste'            => $request->caste,
                'mobile_number'    => $request->mobile_number,
                'blood_group'      => $request->blood_group,
                'student_photo'    => $photoPath,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name'  => trim($request->first_name . ' ' . $request->last_name),
                    'phone' => $request->mobile_number
                ]);
            }

            auditLog(module: 'Students', action: 'Update', recordId: $student->id, old: $student->getOriginal(), new: $student->toArray(), description: "Updated student: {$student->first_name}", event: 'student_updated');

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Student record updated successfully!']);
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
        $student = Student::findOrFail($id);

        DB::beginTransaction();
        try {
            $student->delete();

            if ($student->user) {
                $student->user->delete();
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Student record deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function updatePassword(Request $request, $id) // Add $id here
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // Use the $id from the URL parameter
            $user = User::findOrFail($id);

            if ($user->organization_id !== currentOrgId()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully for ' . $user->name
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        // findOrFail respects the Global Tenant Scope automatically
        $student = Student::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            $oldStatus = $student->status;
            $newStatus = ($oldStatus == 1) ? 0 : 1;

            // 1. Update Student Status
            $student->update([
                'status' => $newStatus
            ]);

            // 2. Sync with User Account (if exists)
            if ($student->user) {
                $student->user->update([
                    'status' => $newStatus
                ]);
            }

            // 3. Audit Log
            auditLog(
                module: 'Students',
                action: 'Toggle Status',
                recordId: $student->id,
                old: ['status' => $oldStatus],
                new: ['status' => $newStatus],
                description: "Changed status for {$student->first_name} to " . ($newStatus ? 'Active' : 'Inactive'),
                event: 'student_status_updated'
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student status updated successfully',
                'new_status' => $newStatus // Required for your Global JS to update the badge
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignParent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'parent_id' => 'required|exists:parents,id'
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->parent_id !== null) {
            if ($student->parent_id == $request->parent_id) {
                return response()->json(['status' => false, 'message' => 'This student is already linked to this parent.'], 422);
            }

            return response()->json([
                'status' => false,
                'message' => 'This student is already assigned to another parent. Please unlink them first.'
            ], 422);
        }

        $student->update(['parent_id' => $request->parent_id]);

        return response()->json(['status' => true, 'message' => 'Student linked successfully!']);
    }

    public function unlinkParent($id)
    {
        try {
            $student = Student::findOrFail($id);

            // Security check for multi-tenancy
            if ($student->organization_id !== currentOrgId()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            // Set parent_id to null to unlink
            $student->update(['parent_id' => null]);

            return response()->json([
                'status' => true,
                'message' => 'Student successfully unlinked from parent.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // StudentController.php

    public function mySubjects()
    {
        $user = auth()->user();

        $student = \App\Models\Student::with('class')->where('user_id', $user->id)->firstOrFail();

        $subjects = \App\Models\ClassSubject::with(['subject', 'assignedTeacher.teacher'])
            ->where('class_id', $student->class_id)
            ->get(); 

        return view('student.subjects', compact('subjects', 'student'));
    }
}
