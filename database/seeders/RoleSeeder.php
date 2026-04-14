<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // ✅ Create roles with guard
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        $masterAdmin = Role::firstOrCreate([
            'name' => 'master_admin',
            'guard_name' => 'web'
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $teacher = Role::firstOrCreate([
            'name' => 'teacher',
            'guard_name' => 'web'
        ]);

        $parent = Role::firstOrCreate([
            'name' => 'parent',
            'guard_name' => 'web'
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web'
        ]);

        $student = Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web'
        ]);

        // ✅ Get permissions safely
        $allPermissions = Permission::where('guard_name', 'web')->get();

        // ✅ Assign permissions

        // Super Admin → all permissions
        $superAdmin->syncPermissions($allPermissions);

        // Admin → limited
        $admin->syncPermissions(
            Permission::whereIn('name', [
                'organization.view'
            ])->get()
        );

        // Master Admin → admin permissions
        $masterAdmin->syncPermissions(
            Permission::whereIn('name', [
                'admin.view',
                'admin.create',
                'admin.update',
                'admin.delete',
            ])->get()
        );

        // Others → empty
        $teacher->syncPermissions([]);
        $staff->syncPermissions([]);
        $student->syncPermissions([]);
        $parent->syncPermissions([]);
    }
}