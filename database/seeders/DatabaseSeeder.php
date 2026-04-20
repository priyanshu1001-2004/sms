<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\AcademicYear;
use App\Models\TimetableGroup;
use App\Models\Classes;
use App\Models\ParentModal;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Step 1: Roles & Permissions (Sabse Pehle)
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            WeekDaySeeder::class
        ]);

        // ✅ Step 2: Super Admin Create Karein (System Owner)
        // Login: phone -> 1111111111, pass -> 12345678
        $superAdmin = User::updateOrCreate(
            ['phone' => '1111111111'],
            [
                'name'      => 'Super Admin',
                'email'     => 'superadmin@gmail.com',
                'username'  => 'SUPER001',
                'password'  => Hash::make('12345678'),
                'user_type' => 'super_admin', // Type super_admin
            ]
        );
        $superRole = Role::where('name', 'super_admin')->first();
        if ($superRole) {
            $superAdmin->syncRoles([$superRole]);
        }


        // ✅ Step 3: Dummy Organization Create Karein
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

        // ✅ Step 4: Master Admin Create Karein (School Owner)
        // Login: phone -> 0000000000, pass -> 12345678
        $masterAdmin = User::updateOrCreate(
            ['phone' => '0000000000'],
            [
                'organization_id' => $org->id,
                'name'            => 'Master Admin',
                'email'           => 'master@gmail.com',
                'username'        => 'MASTER001',
                'password'        => Hash::make('12345678'),
                'user_type'       => 'master_admin', // Type master_admin
            ]
        );
        $masterRole = Role::where('name', 'master_admin')->first();
        if ($masterRole) {
            $masterAdmin->syncRoles([$masterRole]);
        }


        // ✅ Step 5: Trial Subscription (Global Scope Bypass)
        Subscription::withoutGlobalScope('tenant')->updateOrCreate(
            ['organization_id' => $org->id],
            [
                'plan_name'  => 'Trial Plan',
                'amount'     => 0,
                'start_date' => now(),
                'end_date'   => now()->addDays(30),
                'status'     => 'active',
            ]
        );

        // ✅ Step 6: Academic Year
        AcademicYear::withoutGlobalScope('tenant')->updateOrCreate(
            ['organization_id' => $org->id, 'name' => '2026-2027'],
            [
                'start_date' => '2026-04-01',
                'end_date'   => '2027-03-31',
                'is_active'  => 1,
            ]
        );

        // ✅ Step 7: Timetable Groups
        $juniorGroup = TimetableGroup::withoutGlobalScope('tenant')->updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Junior Section'],
            ['status' => 1]
        );

        $seniorGroup = TimetableGroup::withoutGlobalScope('tenant')->updateOrCreate(
            ['organization_id' => $org->id, 'name' => 'Senior Section'],
            ['status' => 1]
        );

        // ✅ Step 8: Dummy Classes
        Classes::factory()->count(5)->create([
            'organization_id' => $org->id,
            'timetable_group_id' => $juniorGroup->id
        ]);

        Classes::factory()->count(5)->create([
            'organization_id' => $org->id,
            'timetable_group_id' => $seniorGroup->id
        ]);

        // ✅ Step 9: Create 10 Subjects
        Subject::factory()->count(10)->create([
            'organization_id' => $org->id
        ]);

        // 1. Pehle Teachers banayein (e.g. 10 teachers)
        Teacher::factory()->count(10)->create();

        // 2. Parents banayein (e.g. 30 parents)
        ParentModal::factory()->count(30)->create();

        // 3. Phir Students banayein (e.g. 50 students)
        // Note: Har student random class aur parent pick kar lega
        Student::factory()->count(50)->create();

        // --- DatabaseSeeder.php ke end mein add karein ---

        $this->command->info('Mapping Classes, Subjects and Teachers...');

        $allClasses = \App\Models\Classes::all();
        $allSubjects = \App\Models\Subject::all();
        $allTeachers = \App\Models\Teacher::all();

        foreach ($allClasses as $class) {
            // 1. Assign 5-6 Random Subjects to each Class (class_subjects)
            $selectedSubjects = $allSubjects->random(rand(5, 7));

            foreach ($selectedSubjects as $subject) {
                $classSubject = \Illuminate\Support\Facades\DB::table('class_subjects')->insertGetId([
                    'organization_id' => $org->id,
                    'class_id'        => $class->id,
                    'subject_id'      => $subject->id,
                    'status'          => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 2. Assign a Subject Teacher for this specific Class-Subject (subject_teachers)
                // Har subject ke liye ek random teacher assign kar rahe hain
                \Illuminate\Support\Facades\DB::table('subject_teachers')->insert([
                    'organization_id'  => $org->id,
                    'class_subject_id' => $classSubject,
                    'teacher_id'       => $allTeachers->random()->id,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // 3. Assign one Primary Class Teacher for this Class (class_teachers)
            \Illuminate\Support\Facades\DB::table('class_teachers')->insert([
                'organization_id' => $org->id,
                'class_id'        => $class->id,
                'teacher_id'      => $allTeachers->random()->id,
                'is_primary'      => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        $this->command->info('Mapping Completed! Teachers are now assigned to Classes and Subjects.');

        // DatabaseSeeder.php mein Mapping logic ke baad add karein

        $this->command->info('Creating Time Slots...');

        $groups = \App\Models\TimetableGroup::all();
        foreach ($groups as $group) {
            // Period 1 to 4
            for ($i = 1; $i <= 4; $i++) {
                \App\Models\TimeSlot::create([
                    'organization_id'    => $org->id,
                    'timetable_group_id' => $group->id,
                    'name'               => "Period $i",
                    'start_time'         => (8 + $i) . ':00:00', // 9AM, 10AM, 11AM...
                    'end_time'           => (8 + $i) . ':45:00',
                    'is_break'           => false,
                ]);
            }

            // Lunch Break
            \App\Models\TimeSlot::create([
                'organization_id'    => $org->id,
                'timetable_group_id' => $group->id,
                'name'               => 'Lunch Break',
                'start_time'         => '13:00:00',
                'end_time'           => '13:45:00',
                'is_break'           => true,
            ]);

            // Period 5 to 6
            for ($i = 5; $i <= 6; $i++) {
                \App\Models\TimeSlot::create([
                    'organization_id'    => $org->id,
                    'timetable_group_id' => $group->id,
                    'name'               => "Period $i",
                    'start_time'         => (9 + $i) . ':00:00', // 2PM, 3PM
                    'end_time'           => (9 + $i) . ':45:00',
                    'is_break'           => false,
                ]);
            }
        }

        $this->command->info('Generating Class Timetables...');

        $weekDays = \App\Models\WeekDay::all(); // Make sure WeekDaySeeder is run
        $allClasses = \App\Models\Classes::with('timetableGroup')->get();

        foreach ($allClasses as $class) {
            $slots = \App\Models\TimeSlot::where('timetable_group_id', $class->timetable_group_id)
                ->where('is_break', false)
                ->get();

            // Is class ko jo subjects aur teachers humne pehle assign kiye the
            $assignedMappings = \Illuminate\Support\Facades\DB::table('subject_teachers')
                ->join('class_subjects', 'subject_teachers.class_subject_id', '=', 'class_subjects.id')
                ->where('class_subjects.class_id', $class->id)
                ->select('class_subjects.subject_id', 'subject_teachers.teacher_id')
                ->get();

            if ($assignedMappings->isEmpty()) continue;

            foreach ($weekDays as $day) {
                foreach ($slots as $index => $slot) {
                    // Pick a subject mapping for this slot
                    $mapping = $assignedMappings[$index % $assignedMappings->count()];

                    try {
                        \App\Models\ClassTimetable::create([
                            'organization_id' => $org->id,
                            'class_id'        => $class->id,
                            'subject_id'      => $mapping->subject_id,
                            'teacher_id'      => $mapping->teacher_id,
                            'week_day_id'     => $day->id,
                            'time_slot_id'    => $slot->id,
                            'room_number'     => 'Room-' . rand(101, 505),
                        ]);
                    } catch (\Exception $e) {
                        // Unique constraint (teacher conflict) skip kar dega automatically
                        continue;
                    }
                }
            }
        }

        $this->command->info('Timetable Generated Successfully!');

        $this->command->info('Setting up Exams, Grades, and Results...');

        // 1. Create Default Grades (Standard School Grading)
        $grades = [
            ['name' => 'A+', 'percent_from' => 90, 'percent_to' => 100, 'grade_point' => 4.0, 'remarks' => 'Outstanding'],
            ['name' => 'A',  'percent_from' => 80, 'percent_to' => 89.99, 'grade_point' => 3.5, 'remarks' => 'Excellent'],
            ['name' => 'B',  'percent_from' => 70, 'percent_to' => 79.99, 'grade_point' => 3.0, 'remarks' => 'Very Good'],
            ['name' => 'C',  'percent_from' => 60, 'percent_to' => 69.99, 'grade_point' => 2.5, 'remarks' => 'Good'],
            ['name' => 'D',  'percent_from' => 40, 'percent_to' => 59.99, 'grade_point' => 2.0, 'remarks' => 'Fair'],
            ['name' => 'F',  'percent_from' => 0,  'percent_to' => 39.99, 'grade_point' => 0.0, 'remarks' => 'Fail'],
        ];

        foreach ($grades as $g) {
            \App\Models\Grade::create(array_merge($g, ['organization_id' => $org->id]));
        }

        // 2. Create an Exam (Term 1)
        $academicYear = \App\Models\AcademicYear::where('organization_id', $org->id)->first();
        $exam = \App\Models\Exam::create([
            'organization_id' => $org->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'First Term Examination',
            'term_name' => 'Term 1',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(20),
            'is_published' => true,
            'status' => true,
        ]);

        // 3. Create Exam Schedules & Results
        $classes = \App\Models\Classes::all();
        $masterAdmin = \App\Models\User::where('user_type', 'master_admin')->first();

        foreach ($classes as $class) {
            // Is class ke subjects nikaalein (Pivot table se)
            $classSubjects = \Illuminate\Support\Facades\DB::table('class_subjects')
                ->where('class_id', $class->id)
                ->get();

            foreach ($classSubjects as $index => $cs) {
                // Schedule Banayein
                $schedule = \App\Models\ExamSchedule::create([
                    'exam_id' => $exam->id,
                    'class_id' => $class->id,
                    'subject_id' => $cs->subject_id,
                    'exam_date' => now()->addDays(10 + $index)->format('Y-m-d'),
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'full_marks' => 100,
                    'pass_marks' => 33,
                ]);

                // Students ke liye Marks entry karein
                $students = \App\Models\Student::where('class_id', $class->id)->get();
                foreach ($students as $student) {
                    $marks = rand(30, 95); // Random marks

                    // Grade calculate karein logic se
                    $appliedGrade = collect($grades)->first(function ($g) use ($marks) {
                        return $marks >= $g['percent_from'] && $marks <= $g['percent_to'];
                    });

                    \App\Models\ExamResult::create([
                        'organization_id' => $org->id,
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $cs->subject_id,
                        'class_id' => $class->id,
                        'marks_obtained' => $marks,
                        'attendance' => 'P',
                        'grade_name' => $appliedGrade['name'] ?? 'F',
                        'grade_point' => $appliedGrade['grade_point'] ?? 0,
                        'created_by' => $masterAdmin->id,
                    ]);
                }
            }
        }

        $this->command->info('Exam data seeded! Students now have report cards.');
    }
}
