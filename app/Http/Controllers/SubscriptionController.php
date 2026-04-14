<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // We need $organizations for the dropdown in the filter and modal
        $organizations = Organization::orderBy('name', 'asc')->get();

        $subscriptions = Subscription::with('organization')
            ->when($request->organization_id, function ($query, $orgId) {
                return $query->where('organization_id', $orgId);
            })
            ->when($request->plan_name, function ($query, $plan) {
                return $query->where('plan_name', $plan);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('pages.subscriptions.index', compact('subscriptions', 'organizations'));
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
        // 1. Strict Validation
        $validatedData = $request->validate([
            'organization_id'   => 'required|exists:organizations,id',
            'plan_name'         => 'required|string|max:255',
            'amount'            => 'required|numeric|min:0',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'status'            => 'required|string|in:active,expired,cancelled,trial',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            if ($validatedData['status'] === 'active') {
                Subscription::where('organization_id', $validatedData['organization_id'])
                    ->where('status', 'active')
                    ->whereNull('deleted_at')
                    ->update([
                        'status'     => 'cancelled',
                        'updated_at' => now()
                    ]);
            }

            $subscription = Subscription::create($validatedData);

            $orgStatus = in_array($validatedData['status'], ['active', 'trial']) ? 1 : 0;
            $organization = Organization::findOrFail($validatedData['organization_id']);
            $organization->update(['status' => $orgStatus]);

            auditLog(
                module: 'Subscription',
                action: 'Store',
                recordId: $subscription->id,
                new: $validatedData,
                description: "New {$subscription->plan_name} plan activated. Previous active plans for {$organization->name} were archived."
            );

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Subscription created successfully. History updated.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription Flow Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while processing the subscription.'
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
