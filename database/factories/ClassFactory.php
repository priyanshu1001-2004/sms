<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\TimetableGroup;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassFactory extends Factory
{
    protected $model = Classes::class;

    // Ek static variable banate hain jo counter ka kaam karega
    protected static $order = 1;

    public function definition(): array
    {
        $org = Organization::first();
        
        $groupName = (static::$order <= 5) ? 'Junior Section' : 'Senior Section';
        $group = TimetableGroup::where('organization_id', $org?->id)
                                ->where('name', $groupName)
                                ->first();

        $currentNumber = static::$order++;

        return [
            'organization_id' => $org?->id ?? 1,
            'timetable_group_id' => $group?->id ?? null,
            'name' => 'Class ' . $currentNumber,
            'code' => 'CLS-' . str_pad($currentNumber, 2, '0', STR_PAD_LEFT), // e.g., CLS-01, CLS-02
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /** * Factory ko reset karne ke liye method (Optional)
     * Agar aap multiple orgs ke liye seed kar rahe hain
     */
    public static function resetOrder()
    {
        static::$order = 1;
    }
}