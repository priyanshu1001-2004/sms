<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
      
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            WeekDaySeeder::class
        ]);

       
        $superAdmin = User::updateOrCreate(
            ['phone' => '1111111111'],
            [
                'name'      => 'Super Admin',
                'email'     => 'superadmin@gmail.com',
                'username'  => 'SUPER001',
                'password'  => Hash::make('12345678'),
                'user_type' => 'super_admin',
            ]
        );
        $superRole = Role::where('name', 'super_admin')->first();
        if ($superRole) $superAdmin->syncRoles([$superRole]);

       
        $orgName = 'Global International School';
        $org = Organization::updateOrCreate(
            ['slug' => Str::slug($orgName)],
            [
                'name'        => $orgName,
                'short_name'  => 'GIS',
                'email'       => 'admin@globalschool.com',
                'phone'       => '9876543210',
                'is_verified' => true,
                'status'      => true,
            ]
        );

        
        $masterAdmin = User::updateOrCreate(
            ['phone' => '0000000000'],
            [
                'organization_id' => $org->id,
                'name'            => 'Master Admin',
                'email'           => 'master@gmail.com',
                'username'        => 'MASTER001',
                'password'        => Hash::make('12345678'),
                'user_type'       => 'master_admin',
            ]
        );
        $masterRole = Role::where('name', 'master_admin')->first();
        if ($masterRole) $masterAdmin->syncRoles([$masterRole]);

      
    }
}