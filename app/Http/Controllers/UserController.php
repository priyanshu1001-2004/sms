<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            $role = $user->getRoleNames()->first();

            switch ($role) {
                case 'super_admin':
                    session(['impersonator_id' => $user->id]);
                    return redirect()->route('dashboard');

                case 'master_admin':
                    return redirect()->route('dashboard');

                case 'admin':
                    return redirect()->route('dashboard');

                case 'teacher':
                    return redirect()->route('dashboard');

                case 'parent':
                    return redirect()->route('dashboard');

                case 'student':
                    return redirect()->route('dashboard');

                case 'staff':
                    return redirect()->route('dashboard');

                default:
                    Auth::logout();
                    return back()->withErrors(['phone' => 'Access denied. Role not configured.']);
            }
        }

        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
        ])->onlyInput('phone');
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
