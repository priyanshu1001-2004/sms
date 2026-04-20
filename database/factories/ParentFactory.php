<?php

namespace Database\Factories;

use App\Models\ParentModal;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ParentFactory extends Factory
{

    protected $model = ParentModal::class;

    // database/factories/ParentFactory.php
    public function definition(): array
    {
        $org = \App\Models\Organization::first();
        $firstName = $this->faker->firstNameMale();
        $lastName = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();
        $uniqueId = $this->faker->unique()->numberBetween(1000, 9999);

        $user = \App\Models\User::create([
            'organization_id' => $org->id,
            'name'            => $firstName . ' ' . $lastName,
            'username'        => 'PRNT' . $uniqueId, // Username add kiya
            'phone'           => $this->faker->unique()->numerify('8#########'),
            'email'           => $email,
            'password'        => \Illuminate\Support\Facades\Hash::make('12345678'),
            'user_type'       => 'parent',
        ]);

        $user->assignRole('parent');

        return [
            'organization_id' => $org->id,
            'user_id'         => $user->id,
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'gender'          => 'Male',
            'email'           => $email,
            'mobile_number'   => $user->phone,
            'relation'        => 'Father',
            'status'          => 1,
        ];
    }
}
