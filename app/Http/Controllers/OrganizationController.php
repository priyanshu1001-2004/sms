<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Config\Exception\ValidationException;

class OrganizationController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('role:super_admin', only: ['index', 'show', 'create', 'store', 'destroy', 'toggleStatus']),
            new Middleware('role:super_admin|master_admin', only: ['updateFullProfile', 'updateLogo']),
            new Middleware('permission:organization.view', only: ['index', 'show']),
            new Middleware('permission:organization.create', only: ['create', 'store']),
            new Middleware('permission:organization.update', only: ['edit', 'update']),
            new Middleware('permission:organization.delete', only: ['destroy']),
        ];
    }


    public function index(Request $request)
    {
        $query = Organization::query();

        // Organization Name
        if ($request->filled('name')) {
            $query->where('name', 'ILIKE', '%' . trim($request->name) . '%');
        }

        // User Email
        if ($request->filled('email')) {
            $email = trim($request->email);

            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'ILIKE', "%{$email}%");
            });
        }

        // User Phone
        if ($request->filled('phone')) {
            $phone = trim($request->phone);

            $query->whereHas('user', function ($q) use ($phone) {
                $q->where('phone', 'ILIKE', "%{$phone}%");
            });
        }

        // Status
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $organizations = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.organizations.index', compact('organizations'));
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
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:organizations,name',
            'admin_name'     => 'required|string|max:255',
            'admin_phone'    => 'required|digits_between:10,15|unique:users,phone',
            'admin_email'    => 'nullable|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Validation failed. Please check the fields.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Create Organization Shell
            $organization = Organization::create([
                'name'        => trim($request->name),
                'slug'        => Str::slug($request->name), // Professional URL-friendly slug
                'status'      => true,
                'is_verified' => false, // Pending Master Admin onboarding
            ]);

            // 2. Create Master Admin
            $admin = User::create([
                'name'            => trim($request->admin_name),
                'phone'           => $request->admin_phone,
                'email'           => $request->admin_email ?? null,
                'password'        => Hash::make($request->admin_password),
                'organization_id' => $organization->id,
                'user_type'       => 'master_admin',
            ]);

            $admin->assignRole('master_admin');

            // 3. Initialize default trial subscription
            $organization->subscription()->create([
                'plan_name'  => 'Trial Plan',
                'amount'     => 0,
                'start_date' => now(),
                'end_date'   => now()->addDays(30),
                'status'     => 'active',
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'School registered successfully! Credentials ready for Master Admin.',
                'redirect' => route('organizations.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Super Admin Store Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to register school. ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Request $request) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization)
    {
        $admin = $organization->user; // Assumes a 'user' relationship in Organization model

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:organizations,name,' . $organization->id,
            'status'      => 'required|in:0,1',
            'admin_name'  => 'required|string|max:255',
            'admin_phone' => 'required|digits_between:10,15|unique:users,phone,' . ($admin ? $admin->id : 'NULL'),
            'admin_email' => 'nullable|email|max:255|unique:users,email,' . ($admin ? $admin->id : 'NULL'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Validation error! Please fix the highlighted fields.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Update School Basic Name & Status
            $organization->update([
                'name'   => trim($request->name),
                'slug'   => Str::slug($request->name),
                'status' => $request->status,
            ]);

            // 2. Update Admin Login Identity
            if ($admin) {
                $admin->update([
                    'name'  => trim($request->admin_name),
                    'phone' => $request->admin_phone,
                    'email' => $request->admin_email,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Organization details updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Super Admin Update Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Database update failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        DB::beginTransaction();

        try {
            if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
                Storage::disk('public')->delete($organization->logo);
            }

            if ($organization->user) {
                $organization->user->delete();
            }

            // 3. Delete the Organization
            $organization->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Organization and associated admin deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Organization Delete Error: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete organization. It might be linked to other data.'
            ], 500);
        }
    }

    public function toggleStatus($id)
    {

        DB::beginTransaction();
        try {
            $organization = Organization::findOrFail($id);
            $organization->status = !$organization->status;
            $organization->save();

            DB::commit();

            return response()->json([
                'status'     => true, // Returning boolean is easier for JS checks
                'message'    => 'Organization status updated successfully',
                'new_status' => $organization->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Toggle Status Error: " . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $org = auth()->user()->organization;

            if (!$org) {
                return response()->json(['status' => 'error', 'message' => 'Organization not found.'], 404);
            }

            // Delete old logo if it exists
            if ($org->logo && Storage::disk('public')->exists($org->logo)) {
                Storage::disk('public')->delete($org->logo);
            }

            // Store new logo
            $path = $request->file('logo')->store('logos/' . $org->id, 'public');

            $org->update([
                'logo' => $path,
                'is_verified' => true // Mark as partially onboarded
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'School logo updated successfully!',
                'logo_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateFullProfile(Request $request)
    {
        $user = auth()->user();
        $org = $user->organization;

        if (!$org) {
            return response()->json(['status' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $tab = $request->input('form_tab');
        $responseData = [];

        try {
            switch ($tab) {
                case 'identity':
                    $request->validate([
                        'name'                => 'required|string|max:255',
                        'short_name'          => 'nullable|string|max:50',
                        'established_at'      => 'nullable|integer|digits:4|max:' . date('Y'),
                        'registration_number' => 'nullable|string|max:100',
                        'principal_name'      => 'nullable|string|max:255',
                        'email'               => 'nullable|email|unique:organizations,email,' . $org->id,
                        'phone'               => 'nullable|string|max:20',
                        'motto'               => 'nullable|string|max:255',
                        'address'             => 'nullable|string',
                        'city'                => 'nullable|string|max:100',
                        'state'               => 'nullable|string|max:100',
                        'pincode'             => 'nullable|string|max:15',
                        'logo'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    ]);

                    $data = $request->only([
                        'name',
                        'short_name',
                        'established_at',
                        'registration_number',
                        'principal_name',
                        'motto',
                        'email',
                        'phone',
                        'address',
                        'city',
                        'state',
                        'pincode'
                    ]);

                    if ($request->hasFile('logo')) {
                        if ($org->logo && Storage::disk('public')->exists($org->logo)) {
                            Storage::disk('public')->delete($org->logo);
                        }
                        $data['logo'] = $request->file('logo')->store('uploads/logos/' . $org->id, 'public');
                    }

                    $data['slug'] = Str::slug($request->name);
                    break;

                case 'bank':
                    $request->validate([
                        'bank_name'      => 'required|string|max:255',
                        'account_holder' => 'required|string|max:255',
                        'account_number' => 'required|string|max:255',
                        'ifsc_code'      => 'required|string|max:20',
                        'tax_id'         => 'nullable|string|max:100',
                        'upi_id'         => 'nullable|string|max:255',
                    ]);

                    $data = $request->only(['bank_name', 'account_holder', 'account_number', 'ifsc_code', 'upi_id', 'tax_id']);

                    // Logic for Secure QR String
                    if ($request->filled('upi_id')) {
                        $pa = trim($request->upi_id);
                        $pn = rawurlencode($request->account_holder ?? $org->name);
                        $mc = "5211";
                        $tn = rawurlencode("Fee Payment " . $org->name); // Transaction Note

                        // Final Secure UPI String
                        $upiString = "upi://pay?pa={$pa}&pn={$pn}&mc={$mc}&tn={$tn}&cu=INR&mode=02";

                        // Generate high-resolution QR with "Quiet Zone" (margin)
                        $responseData['qr_code'] = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=20&data=" . urlencode($upiString);
                    }
                    break;

                default:
                    return response()->json(['status' => false, 'message' => 'Invalid form submission.'], 400);
            }

            // Always mark as verified when a tab is saved
            $data['is_verified'] = true;
            $org->update($data);

            return response()->json(array_merge([
                'status' => true,
                'message' => ucfirst($tab) . ' details updated successfully!'
            ], $responseData));
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
