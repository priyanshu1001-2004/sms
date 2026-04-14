<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{


    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // get first role
        $role = $user->getRoleNames()->first();

        // map role → view
        $views = [
            'super_admin'  => 'dashboard.super_admin',
            'master_admin' => 'dashboard.master_admin',
            'admin'        => 'dashboard.admin',
            'teacher'      => 'dashboard.teacher',
            'student'      => 'dashboard.student',
            'parent'       => 'dashboard.parent',
        ];

        $view = $views[$role] ?? 'dashboard.default';

        return view($view, compact('user', 'role'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
