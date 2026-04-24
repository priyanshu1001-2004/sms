<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Config
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            WeekDaySeeder::class
        ]);

        // 2. Super Admin
        $superAdmin = User::updateOrCreate(
            ['phone' => '1111111111'],
            [
                'name'      => 'Super Admin',
                'email'     => 'superadmin@gmail.com',
                'username'  => '1111111111',
                'password'  => Hash::make('12345678'),
                'user_type' => 'super_admin',
            ]
        );
        $superRole = Role::where('name', 'super_admin')->first();
        if ($superRole) $superAdmin->syncRoles([$superRole]);

        // 3. Organization
        $org = Organization::updateOrCreate(
            ['slug' => 'global-international-school'],
            [
                'name'        => 'Global International School',
                'short_name'  => 'GIS',
                'email'       => 'admin@globalschool.com',
                'phone'       => '9876543210',
                'is_verified' => true,
                'status'      => true,
            ]
        );

        // 4. Master Admin
        $masterAdmin = User::updateOrCreate(
            ['phone' => '0000000000'],
            [
                'organization_id' => $org->id,
                'name'            => 'Master Admin',
                'email'           => 'master@gmail.com',
                'username'        => 'master@gmail.com',
                'password'        => Hash::make('12345678'),
                'user_type'       => 'master_admin',
            ]
        );
        $masterRole = Role::where('name', 'master_admin')->first();
        if ($masterRole) $masterAdmin->syncRoles([$masterRole]);

        Subject::factory()->count(9)->create([ 'organization_id' => $org->id]);
    }
}