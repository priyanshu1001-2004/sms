<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginValue = trim($request->login_id);
        $field = 'username'; 

        if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
            $field = 'email'; // Admin
        } elseif (is_numeric($loginValue) && strlen($loginValue) >= 10) {
            $field = 'phone'; // Parent
        }

        $credentials = [
            $field     => $loginValue,
            'password' => $request->password,
        ];

        // 2. Attempt Login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Get the primary role
            $role = $user->getRoleNames()->first();

            // Use a variable to store the route so we can return JSON at the end
            $redirectRoute = route('dashboard');

            switch ($role) {
                case 'super_admin':
                    session(['impersonator_id' => $user->id]);
                    $redirectRoute = route('dashboard');
                    break;

                case 'master_admin':
                    $redirectRoute = route('dashboard');
                    break;

                case 'admin':
                    $redirectRoute = route('dashboard');
                    break;

                case 'teacher':
                    $redirectRoute = route('dashboard');
                    break;

                case 'student':
                    $redirectRoute = route('dashboard');
                    break;

                case 'parent':
                    $redirectRoute = route('dashboard');
                    break;

                default:
                    Auth::logout();
                    // Return error if role is missing
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'login_id' => ['Your account has no assigned role.'],
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful! Redirecting...',
                'redirect' => $redirectRoute
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'login_id' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function switch(Request $request)
    {
        // 1. Validate the Organization exists
        $request->validate([
            'organization_id' => 'required|exists:organizations,id'
        ]);

        // 2. Find the SPECIFIC User that is the Master Admin for this Org
        $targetUser = User::where('organization_id', $request->organization_id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'master_admin');
            })
            ->first();

        // 3. Safety check: If no Master Admin is found, stop here.
        if (!$targetUser) {
            return response()->json([
                'status' => false,
                'message' => 'Error: This organization does not have a Master Admin assigned.'
            ], 422);
        }

        // 4. Record who the original Super Admin is (so you can switch back)
        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => auth()->id()]);
        }

        // 5. Set the context for the session
        session([
            'selected_organization_id' => $request->organization_id,
        ]);

        // 6. Login as the Master Admin User ID
        // This logs in the USER, not the Organization.
        auth()->login($targetUser);

        return response()->json(['status' => true]);
    }


    /**
     * Return the Super Admin to their original account.
     */
    public function switchBack()
    {
        if (session()->has('impersonator_id')) {
            $originalAdminId = session('impersonator_id');

            session()->forget([
                'selected_branch_id',
            ]);

            Auth::loginUsingId($originalAdminId);

            return redirect()->route('dashboard')->with('success', 'Returned to Super Admin account.');
        }

        // If no impersonator session, just go back
        return redirect()->route('dashboard');
    }


    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully for ' . $user->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update password.'
            ], 500);
        }
    }



    public function getBranchesByOrg(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id'
        ]);

        // Fetch only active branches for the requested Org
        $branches = Branch::where('organization_id', $request->organization_id)
            ->where('status', 1)
            ->get(['id', 'name', 'code']);

        return response()->json($branches);
    }

    /**
     * Save the selected branch to the session
     */
    public function updateBranchSession(Request $request)
    {
        // If branch_id is 'all', we store 'all', otherwise we store the ID
        $branchId = $request->branch_id;

        session(['selected_branch_id' => $branchId]);

        return response()->json([
            'status' => 'success',
            'message' => 'Branch context updated'
        ]);
    }
}
