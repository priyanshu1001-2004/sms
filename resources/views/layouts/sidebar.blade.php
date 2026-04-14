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

                @hasrole('master_admin')

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('admins.index') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Admin</span></a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('teachers.index') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Teachers</span></a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('students.index') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Student</span></a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('parents.index') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Parents</span></a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0)"><i
                            class="side-menu__icon fe fe-slack"></i><span class="side-menu__label">Acadmic</span><i
                            class="angle fe fe-chevron-right"></i></a>
                    <ul class="slide-menu">
                        @hasrole('master_admin')

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('academic-years.index') }}"><i
                                    class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Academic
                                    Years</span></a>
                        </li>

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('classes.index') }}"><i class="side-menu__icon fe fe-home"></i><span
                                    class="side-menu__label ">Classes</span></a>
                        </li>

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('subjects.index') }}"><i class="side-menu__icon fe fe-home"></i><span
                                    class="side-menu__label">Subjects</span></a>
                        </li>

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('class_subjects.index') }}"><i
                                    class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Assign Class
                                    Subject</span></a>
                        </li>

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('class_teachers.index') }}"><i
                                    class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Assign Class
                                    Teacher</span></a>
                        </li>

                        <li class="slide">
                            <a class="side-menu__item has-link" data-bs-toggle="slide" style="padding: 8px 0px;"
                                href="{{ route('subject_teachers.index') }}"><i
                                    class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Assign Subject
                                    Teachers</span></a>
                        </li>

                        @endhasrole
                    </ul>
                </li>

                @endhasrole





                @hasrole('super_admin')
                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide"
                        href="{{ route('organizations.index') }}"><i class="side-menu__icon fe fe-home"></i><span
                            class="side-menu__label">Organizations</span></a>
                </li>

                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide"
                        href="{{ route('subscriptions.index') }}"><i class="side-menu__icon fe fe-home"></i><span
                            class="side-menu__label">Subscriptions</span></a>
                </li>
                @endhasrole


                @hasrole('teacher')
                <li class="slide">
                    <a class="side-menu__item {{ request()->is('my-assignments') ? 'active' : '' }}"
                        href="{{ route('teacher.assignments') }}">
                        <i class="side-menu__icon fe fe-book"></i>
                        <span class="side-menu__label">My Class & Subject</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item {{ request()->is('teacher/my-students*') ? 'active' : '' }}"
                        href="{{ route('teacher.students') }}">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">My Students</span>
                    </a>
                </li>
                @endhasrole

                @hasrole('student')
                <li class="slide">
                    <a class="side-menu__item {{ request()->is('my-subjects') ? 'active' : '' }}"
                        href="{{ route('student.subjects') }}">
                        <i class="side-menu__icon fe fe-book"></i>
                        <span class="side-menu__label">My Subjects</span>
                    </a>
                </li>
                @endhasrole

                @hasrole('parent')
                <li class="slide">
                    <a class="side-menu__item has-link" data-bs-toggle="slide" href="{{ route('parents.students') }}"><i
                            class="side-menu__icon fe fe-home"></i><span class="side-menu__label">Students</span></a>
                </li>
                @endhasrole

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