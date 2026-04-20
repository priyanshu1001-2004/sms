<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $orgId = Organization::first()->id ?? 1;
        
        // List of common school subjects
        $subjects = [
            'Mathematics', 'Science', 'English', 'Hindi', 'History', 
            'Geography', 'Physics', 'Chemistry', 'Biology', 'Computer Science'
        ];

        $name = $this->faker->unique()->randomElement($subjects);

        return [
            'organization_id' => $orgId,
            'name' => $name,
            'type' => $this->faker->randomElement(['Theory', 'Practical', 'Both']),
            'code' => strtoupper(substr($name, 0, 3)) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}