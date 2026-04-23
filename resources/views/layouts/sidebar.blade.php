<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="index.html">
                <img src="../assets/images/brand/logo.png" class="header-brand-img desktop-logo" alt="logo">
                <img src="../assets/images/brand/logo-1.png" class="header-brand-img toggle-logo" alt="logo">
                <img src="../assets/images/brand/logo-2.png" class="header-brand-img light-logo" alt="logo">
                <img src="../assets/images/brand/logo-3.png" class="header-brand-img light-logo1" alt="logo">
            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">
                <li class="sub-category">
                    <h3>Main</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('dashboard') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Dashboard</span></a>
                </li>

                @role('super_admin')
                <li class="sub-category">
                    <h3>SaaS Management</h3>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('organizations.*') ? 'active' : '' }}"
                        href="{{ route('organizations.index') }}">
                        <i class="side-menu__icon fe fe-box"></i>
                        <span class="side-menu__label">Organizations</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}"
                        href="{{ route('subscriptions.index') }}">
                        <i class="side-menu__icon fe fe-credit-card"></i>
                        <span class="side-menu__label">Subscriptions</span>
                    </a>
                </li>
                @endrole



                @hasrole('master_admin')
                <li class="sub-category">
                    <h3>School Management</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('admins.*') ? 'active' : '' }}"
                        href="{{ route('admins.index') }}">
                        <i class="side-menu__icon fe fe-user-check"></i>
                        <span class="side-menu__label">Staff Admins</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}"
                        href="{{ route('teachers.index') }}">
                        <i class="side-menu__icon fe fe-briefcase"></i>
                        <span class="side-menu__label">Teachers</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                        href="{{ route('students.index') }}">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">Students</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('parents.*') ? 'active' : '' }}"
                        href="{{ route('parents.index') }}">
                        <i class="side-menu__icon fe fe-user-plus"></i>
                        <span class="side-menu__label">Parents</span>
                    </a>
                </li>

                <li
                    class="slide {{ request()->is('academic*') || request()->is('classes*') || request()->is('subjects*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-book-open"></i>
                        <span class="side-menu__label">Academic Engine</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        {{-- Step 1: Basic Setup (Masters) --}}
                        <li><a href="{{ route('academic-years.index') }}" class="slide-item">Academic Years</a></li>
                        <li><a href="{{ route('timetable-groups.index') }}" class="slide-item">Schedule Profiles</a></li>
                        <li><a href="{{ route('classes.index') }}" class="slide-item">Classes & Sections</a></li>
                        <li><a href="{{ route('subjects.index') }}" class="slide-item">Subjects Master</a></li>

                        {{-- Step 2: Resource Allocation (Mapping) --}}

                        <li><a href="{{ route('class_subjects.index') }}" class="slide-item">Assign Class Subjects</a>
                        </li>
                        <li><a href="{{ route('subject_teachers.index') }}" class="slide-item">Assign Subject
                                Teachers</a></li>
                        <li><a href="{{ route('class_teachers.index') }}" class="slide-item">Assign Class Teachers</a>
                        </li>

                        {{-- Step 3: Timetable Configuration --}}
                        <li><a href="{{ route('time_slots.index') }}" class="slide-item">Time Slots Master</a></li>
                        <li><a href="{{ route('class_timetables.index') }}" class="slide-item">Manage Timetable</a></li>
                    </ul>
                </li>

                <li
                    class="slide {{ request()->is('exam-terms*') || request()->is('grade-scales*') || request()->is('mark-entries*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item {{ request()->is('exam-terms*') || request()->is('grade-scales*') || request()->is('mark-entries*') ? 'active' : '' }}"
                        data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-file-text"></i>
                        <span class="side-menu__label">Examinations</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">

                    </ul>
                </li>
                @endhasrole



                @hasrole('admin')
                <li class="sub-category">
                    <h3>Staff Workspace</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                        href="{{ route('students.index') }}">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">Student Admission</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link {{ request()->routeIs('parents.*') ? 'active' : '' }}"
                        href="{{ route('parents.index') }}">
                        <i class="side-menu__icon fe fe-user-plus"></i>
                        <span class="side-menu__label">Parent Directory</span>
                    </a>
                </li>

                <li
                    class="slide {{ request()->is('time*') || request()->is('class-timetables*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-calendar"></i>
                        <span class="side-menu__label">Schedules</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('time_slots.index') }}" class="slide-item">Manage Time Slots</a></li>
                        <li><a href="{{ route('class_timetables.index') }}" class="slide-item">Class Timetables</a></li>
                    </ul>
                </li>

                <li class="slide {{ request()->is('exam-results*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-edit"></i>
                        <span class="side-menu__label">Exam Records</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{ route('exam-results.index') }}" class="slide-item">Bulk Mark Entry</a></li>
                        <li><a href="{{ route('exam-results.student-wise') }}" class="slide-item">Student-wise Entry</a>
                        </li>
                    </ul>
                </li>
                @endhasrole


                @role('teacher')
                <li class="sub-category">
                    <h3>Teacher Portal</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('teacher.assignments') ? 'active' : '' }}"
                        href="{{ route('teacher.assignments') }}">
                        <i class="side-menu__icon fe fe-book"></i>
                        <span class="side-menu__label">My Classes & Subjects</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->is('teacher/my-students*') ? 'active' : '' }}"
                        href="{{ route('teacher.students') }}">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">My Students</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('teacher.timetable') ? 'active' : '' }}"
                        href="{{ route('teacher.timetable') }}">
                        <i class="side-menu__icon fe fe-clock"></i>
                        <span class="side-menu__label">My Schedule</span>
                    </a>
                </li>



                <li class="slide {{ request()->is('exams*') || request()->is('teacher/exam*') ? 'is-expanded' : '' }}">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-edit-3"></i>
                        <span class="side-menu__label">Grading & Exams</span>
                        <i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">

                    </ul>
                </li>
                @endrole

                @role('student')
                <li class="sub-category">
                    <h3>Student Learning Hub</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('student.subjects') ? 'active' : '' }}"
                        href="{{ route('student.subjects') }}">
                        <i class="side-menu__icon fe fe-book-open"></i>
                        <span class="side-menu__label">My Subjects</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('student.timetable') ? 'active' : '' }}"
                        href="{{ route('student.timetable') }}">
                        <i class="side-menu__icon fe fe-calendar"></i>
                        <span class="side-menu__label">Class Timetable</span>
                    </a>
                </li>



                @endrole

                @role('parent')
                <li class="sub-category">
                    <h3>Parent Workspace</h3>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->routeIs('parents.students') ? 'active' : '' }}"
                        href="{{ route('parents.students') }}">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">My Children</span>
                    </a>
                </li>

                <!-- <li class="slide">
                    <a class="side-menu__item" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-credit-card"></i>
                        <span class="side-menu__label">Fee Payments</span>
                        <span class="badge bg-warning text-dark ms-2" style="font-size: 9px;">Coming Soon</span>
                    </a>
                </li> -->
                @endrole

                <li class="sub-category">
                    <h3>Setting</h3>
                </li>


                <li class="slide">
                    <a class="side-menu__item {{ request()->is('my-profile') ? 'active' : '' }}"
                        href="{{ route('profile.index') }}">
                        <i class="fe fe-user side-menu__icon"></i>
                        <span class="side-menu__label">My Account</span>
                    </a>
                </li>

                <li>
                    <a class="side-menu__item has-link" href="{{ route('layout.switcher') }}"><i
                            class="side-menu__icon fe fe-zap"></i><span class="side-menu__label">Layout Settings
                        </span></a>
                </li>

            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </div>
    <!--/APP-SIDEBAR-->
</div>