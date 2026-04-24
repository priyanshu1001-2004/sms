<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $subjects = [
            'Mathematics', 'Physics', 'Chemistry', 'Biology', 
            'Social Studies', 'History', 'Geography', 'Computer Science', 
            'Economics', 'Business Studies', 'Accountancy'
        ];

        return [
            'organization_id' => 1,
            'name' => $this->faker->unique()->randomElement($subjects),
            'type' => $this->faker->randomElement(['Theory', 'Practical', 'Both']),
            'code' => strtoupper($this->faker->unique()->bothify('SUB-###')),
            'status' => 1,
        ];
    }
}