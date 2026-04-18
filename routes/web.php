<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AcademicYearController,
    AdminController,
    BoardController,
    BranchController,
    ClassesController,
    ClassSubjectController,
    ClassTeacherController,
    ClassTimetableController,
    DashboardController,
    ExamController,
    ExamResultController,
    GradeController,
    OrganizationController,
    ParentController,
    ProfileController,
    StudentController,
    SubjectController,
    SubjectTeacherController,
    SubscriptionController,
    TeacherController,
    TimeSlotController,
    UserController
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All logged-in users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard (accessible to allowed roles only)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | User Utilities
    |--------------------------------------------------------------------------
    */
    Route::put('users/{user}/update-password', [UserController::class, 'updatePassword'])
        ->name('users.updatePassword');

    Route::post('organizations/switch', [UserController::class, 'switch'])
        ->name('organization.switch');

    Route::get('organizations/switch/back', [UserController::class, 'switchBack'])
        ->name('switch.back');

    Route::get('get-branches-by-org', [UserController::class, 'getBranchesByOrg'])
        ->name('get.branches.by.org');

    Route::post('session/update-branch', [UserController::class, 'updateBranchSession'])
        ->name('branch.session.update');


    Route::resource('admins', AdminController::class);
    Route::resource('students', StudentController::class);
    Route::post('students/toggle-status/{id}', [StudentController::class, 'toggleStatus'])->name('students.toggleStatus');
    Route::put('students/{id}/update-password', [StudentController::class, 'updatePassword'])->name('students.updatePassword');
    Route::get('my-subjects', [StudentController::class, 'mySubjects'])->name('student.subjects');
    Route::get('student/my-timetable', [StudentController::class, 'myTimetable'])->name('student.timetable');
    Route::get('my-results', [StudentController::class, 'myResults'])->name('student.results');
    Route::get('student/exam-timetable', [StudentController::class, 'myExamTimetable'])->name('student.exam.timetable');


    Route::resource('parents', ParentController::class);
    Route::post('parents/toggle-status/{id}', [ParentController::class, 'toggleStatus'])->name('parents.toggleStatus');
    Route::put('parents/{id}/update-password', [ParentController::class, 'updatePassword'])->name('parents.updatePassword');
    Route::get('my-children', [ParentController::class, 'parent_students'])->name('parents.students');
    Route::get('parents/student-subjects/{student_id}', [ParentController::class, 'getStudentSubjects']);
    Route::get('parents/student-timetable/{studentId}', [ParentController::class, 'getStudentTimetable'])->name('student_timetable');
    Route::get('children-results', [ParentController::class, 'childrenResults'])->name('parent.results');


    Route::get('parents/{id}/students', [ParentController::class, 'getStudents'])
        ->name('parents.getStudents');

    // 2. Assign a parent to a student (From the Student List action)
    Route::post('students/assign-parent', [StudentController::class, 'assignParent'])
        ->name('students.assignParent');

    // 3. Optional: Unlink a student from a parent
    Route::post('students/unlink-parent/{id}', [StudentController::class, 'unlinkParent'])->name('students.unlinkParent');


    // Common entry point
    Route::get('my-profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::put('profile/student', [ProfileController::class, 'updateStudent'])->name('profile.update.student');
    Route::put('profile/teacher', [ProfileController::class, 'updateTeacher'])->name('profile.update.teacher');
    Route::put('profile/parent', [ProfileController::class, 'updateParent'])->name('profile.update.parent');
    Route::put('profile/admin', [ProfileController::class, 'updateAdmin'])->name('profile.update.admin');
    Route::put('profile/master', [ProfileController::class, 'updateMaster'])->name('profile.update.master');
    Route::put('profile/super-admin', [ProfileController::class, 'updateSuperAdmin'])->name('profile.update.super');

    Route::put('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');

    /*
    |--------------------------------------------------------------------------
    | Common Resources (All authenticated users)
    |--------------------------------------------------------------------------
    */
    Route::resource('subscriptions', SubscriptionController::class);
    Route::resource('academic-years', AcademicYearController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('boards', BoardController::class);
    Route::resource('classes', ClassesController::class);

    Route::resource('subjects', SubjectController::class);
    Route::resource('class_subjects', ClassSubjectController::class);
    Route::resource('class_teachers', ClassTeacherController::class);
    Route::post('class_subjects/toggle-status/{id}', [ClassSubjectController::class, 'toggleStatus'])->name('class_subjects.toggleStatus');
    Route::get('get-assigned-subjects/{classId}', [ClassSubjectController::class, 'getAssignedSubjects'])->name('class_subjects.getAssigned');

    Route::resource('subject_teachers', SubjectTeacherController::class);
    Route::get('get-subjects-by-class/{classId}', [SubjectTeacherController::class, 'getSubjectsByClass'])
        ->name('get.subjects.by.class');


    Route::resource('teachers', TeacherController::class);
    Route::post('teachers/toggle-status/{id}', [TeacherController::class, 'toggleStatus'])->name('teachers.toggleStatus');
    Route::put('teachers/{id}/update-password', [TeacherController::class, 'updatePassword'])->name('teachers.updatePassword');
    Route::get('my-assignments', [TeacherController::class, 'myClassesAndSubjects'])->name('teacher.assignments');
    Route::get('teacher/get-class-students/{classId}', [TeacherController::class, 'getClassStudents']);
    Route::get('teacher/my-students', [TeacherController::class, 'myStudents'])->name('teacher.students');
    Route::get('teacher/my-timetable', [TeacherController::class, 'myTimetable'])->name('teacher.timetable');
    Route::get('teacher/exam-timetable', [TeacherController::class, 'exam_timetable'])->name('teacher.exam.timetable');


    // time table
    Route::resource('time_slots', TimeSlotController::class);
    Route::resource('class_timetables', ClassTimetableController::class);
    Route::get('get-teacher-by-subject-class/{subjectId}/{classId}', [ClassTimetableController::class, 'getTeacherBySubject'])->name('get.teacher.by.subject');

    // exam 
    Route::get('exams/{exam}/schedule', [ExamController::class, 'manageSchedule'])->name('exams.schedule.manage');
    Route::post('exams/schedule/store', [ExamController::class, 'storeSchedule'])->name('exams.schedule.store');
    Route::post('exams/toggle-publish/{id}', [ExamController::class, 'togglePublish'])->name('exams.toggle-publish');

    Route::resource('exams', ExamController::class);
    Route::resource('grades', GradeController::class);

    Route::get('exam-results/create', [ExamResultController::class, 'create'])->name('exam-results.create');
    Route::get('exam-results/student', [ExamResultController::class, 'studentWise'])->name('exam-results.student-wise');
    Route::post('exam-results/store-bulk', [ExamResultController::class, 'storeBulk'])->name('exam-results.store-bulk');
    Route::get('get-students-by-class/{class_id}', [ExamResultController::class, 'getStudentsByClass']);
    Route::resource('exam-results', ExamResultController::class);



    /*
    |--------------------------------------------------------------------------
    | Organization (Restricted Roles)
    |--------------------------------------------------------------------------
    */


    Route::middleware('role:super_admin|master_admin')->group(function () {
        Route::resource('organizations', OrganizationController::class);
        Route::post('organizations/{id}/toggle-status', [OrganizationController::class, 'toggleStatus'])->name('organizations.toggleStatus');
        Route::put('organization/update-full', [OrganizationController::class, 'updateFullProfile'])->name('organization.update.full');
        Route::put('organization/update-logo', [OrganizationController::class, 'updateLogo'])->name('organization.update.logo');
    });

    /*
    |--------------------------------------------------------------------------
    | UI Views
    |--------------------------------------------------------------------------
    */
    Route::view('layout/switcher', 'layouts.switcher')
        ->name('layout.switcher');
});

require __DIR__ . '/auth.php';
