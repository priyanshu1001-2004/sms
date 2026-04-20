<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Classes;
use App\Models\ParentModal; // <--- 1. Sahi model import karein
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    protected static $rollCounter = 1;

    // database/factories/StudentFactory.php
    public function definition(): array
    {
        $org = \App\Models\Organization::first();
        $class = \App\Models\Classes::inRandomOrder()->first();
        $parent = \App\Models\ParentModal::inRandomOrder()->first() ?? \App\Models\ParentModal::factory()->create();

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $uniqueId = $this->faker->unique()->numberBetween(1000, 9999);

        // Create Login for Student
        $user = \App\Models\User::create([
            'organization_id' => $org->id,
            'name'            => $firstName . ' ' . $lastName,
            'username'        => 'STU' . $uniqueId, // Username add kiya
            'phone'           => $this->faker->unique()->numerify('7#########'),
            'email'           => $this->faker->unique()->safeEmail(),
            'password'        => \Illuminate\Support\Facades\Hash::make('12345678'),
            'user_type'       => 'student',
        ]);

        $user->assignRole('student');

        return [
            'organization_id'  => $org->id,
            'user_id'          => $user->id,
            'admission_number' => 'STU' . $uniqueId,
            'class_id'         => $class->id,
            'parent_id'        => $parent->id,
            'first_name'       => $firstName,
            'last_name'        => $lastName,
            'gender'           => $this->faker->randomElement(['Male', 'Female']),
            'date_of_birth'    => $this->faker->date('Y-m-d', '-12 years'),
            'admission_date'   => now(),
            'status'           => 1,
        ];
    }
}
