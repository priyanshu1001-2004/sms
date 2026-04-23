<?php

namespace Database\Seeders;

use App\Models\WeekDay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeekDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            ['name' => 'Monday', 'sort_order' => 1 , 'fullcalendar_day' => 1],
            ['name' => 'Tuesday', 'sort_order' => 2, 'fullcalendar_day' => 2],
            ['name' => 'Wednesday', 'sort_order' => 3, 'fullcalendar_day' => 3],
            ['name' => 'Thursday', 'sort_order' => 4, 'fullcalendar_day' => 4],
            ['name' => 'Friday', 'sort_order' => 5, 'fullcalendar_day' => 5],
            ['name' => 'Saturday', 'sort_order' => 6, 'fullcalendar_day' => 6],
            ['name' => 'Sunday', 'sort_order' => 7, 'fullcalendar_day' => 0],
        ];

        foreach ($days as $day) {
            WeekDay::updateOrCreate(['name' => $day['name']], $day);
        }
    }
}
