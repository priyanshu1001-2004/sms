<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Step 1: Seed permissions & roles FIRST
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // ✅ Step 2: Create Super Admin (using phone login)
        $user = User::updateOrCreate(
            ['phone' => '0000000000'], // login with phone
            [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
            ]
        );

        // ✅ Step 3: Assign role safely
        $role = Role::where('name', 'super_admin')
                    ->where('guard_name', 'web')
                    ->first();

        if ($role) {
            $user->syncRoles([$role]);
        }
    }
}