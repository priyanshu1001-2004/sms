<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    // database/factories/TeacherFactory.php
    public function definition(): array
    {
        $org = \App\Models\Organization::first();
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();
        $uniqueId = $this->faker->unique()->numberBetween(1000, 9999);

        // 1. Create User account first
        $user = \App\Models\User::create([
            'organization_id' => $org->id,
            'name'            => $firstName . ' ' . $lastName,
            'username'        => 'TCH' . $uniqueId, // Username add kiya
            'phone'           => $this->faker->unique()->numerify('9#########'),
            'email'           => $email,
            'password'        => \Illuminate\Support\Facades\Hash::make('12345678'),
            'user_type'       => 'teacher',
        ]);

        // 2. Assign Spatie Role
        $user->assignRole('teacher');

        return [
            'organization_id' => $org->id,
            'user_id'         => $user->id,
            'code'            => 'TCH' . $uniqueId, 
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'gender'          => $this->faker->randomElement(['Male', 'Female']),
            'date_of_birth'   => $this->faker->date('Y-m-d', '-30 years'),
            'email'           => $email,
            'mobile_number'   => $user->phone,
            'current_address' => $this->faker->address(),
            'qualification'   => 'B.Ed',
            'date_of_joining' => now(),
            'status'          => 1,
        ];
    }
}
