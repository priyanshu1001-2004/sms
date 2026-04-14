<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class OrganizationController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('role:super_admin'),

            new Middleware('permission:organization.view', only: ['index', 'show']),
            new Middleware('permission:organization.create', only: ['create', 'store']),
            new Middleware('permission:organization.update', only: ['edit', 'update']),
            new Middleware('permission:organization.delete', only: ['destroy']),
            new Middleware('permission:organization.toggle_status', only: ['toggleStatus']),
        ];
    }


    public function index(Request $request)
    {
        $organizations = Organization::with('user', 'subscription')

            ->when($request->name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            // Filter by Email
            ->when($request->email, function ($query, $email) {
                return $query->where('email', 'like', '%' . $email . '%');
            })
            // Filter by Phone
            ->when($request->phone, function ($query, $phone) {
                return $query->where('phone', 'like', '%' . $phone . '%');
            })
            // Filter by Status (active/inactive)
            ->when($request->status, function ($query, $status) {
                $val = ($status === 'active') ? 1 : 0;
                return $query->where('status', $val);
            })
            ->orderBy('id', 'desc')
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:organizations,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',

            'admin_name' => 'required|string|max:255',
            'admin_phone' => 'required|digits_between:10,15|unique:users,phone',
            'admin_email' => 'nullable|email|max:255',
            'admin_password' => 'required|string|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $logoPath = null;

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }


            // 1. Create Organization
            $organization = Organization::create([
                'name' => trim($request->name),
                'email' => $request->email ?? null,
                'phone' => $request->phone ?? null,
                'address' => $request->address ?? null,
                'status' => $request->status ?? true,
                'logo' => $logoPath,
            ]);


            // 3. Create Admin User
            $admin = User::create([
                'name' => trim($request->admin_name),
                'phone' => $request->admin_phone,
                'email' => $request->admin_email ?? null,
                'password' => Hash::make($request->admin_password),
                'organization_id' => $organization->id,
                'user_type'       => 'master_admin',
            ]);

            $admin->assignRole('master_admin');


            DB::commit();

            return redirect()->route('organizations.index')
                ->with('success', 'Organization created successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Organization create error: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ]);
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
        $admin = $organization->user;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255|unique:organizations,email,' . $organization->id,
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'status'      => 'required|boolean',
            'logo'        => 'nullable|image|max:2048',
            'admin_name'  => 'required|string|max:255',
            'admin_phone' => ['required', Rule::unique('users', 'phone')->where(fn($q) => $q->where('organization_id', $organization->id))->ignore($admin->id)],
            'admin_email' => 'nullable|email|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($organization->logo && Storage::disk('public')->exists($organization->logo)) {
                    Storage::disk('public')->delete($organization->logo);
                }
                $organization->logo = $request->file('logo')->store('logos', 'public');
            }

            // Update organization
            $organization->update([
                'name'    => trim($validated['name']),
                'email'   => $validated['email'],
                'phone'   => $validated['phone'],
                'address' => $validated['address'],
                'status'  => $validated['status'],
                'logo'    => $organization->logo,
            ]);

            // Update admin
            if ($admin) {
                $admin->update([
                    'name'  => trim($validated['admin_name']),
                    'phone' => $validated['admin_phone'],
                    'email' => $validated['admin_email'],
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Organization updated successfully'
                ]);
            }

            return redirect()->route('organizations.index')
                ->with('success', 'Organization updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Organization update error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Something went wrong. Please try again.'
                ], 500);
            }

            return back()->withInput()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ]);
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
}
