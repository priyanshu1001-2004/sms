<!-- app-Header -->
<div class="app-header header sticky">
    <div class="container-fluid main-container">
        <div class="d-flex">
            <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar"
                href="javascript:void(0)"></a>
            <!-- sidebar-toggle-->
            <a class="logo-horizontal " href="index.html">
                <img src="../assets/images/brand/logo.png" class="header-brand-img desktop-logo" alt="logo">
                <img src="../assets/images/brand/logo-3.png" class="header-brand-img light-logo1" alt="logo">
            </a>
            <!-- LOGO -->

            <div class="d-flex w-80 align-items-center">
                {{-- 1. Organization Dropdown --}}
                @if(session()->has('impersonator_id'))
                <div class="w-15">
                    <select name="organization_id" id="session_org_id" class="form-control select2">
                        <option value="">Select Organization</option>

                        @foreach($organizations as $organization)
                        {{--
                        We pass $organization->id.
                        In the controller, we use this ID to find the 'master_admin'
                        --}}
                        <option value="{{ $organization->id }}" {{ session('selected_organization_id')==$organization->
                            id ? 'selected' : '' }}>
                            {{ $organization->name }} ({{ $organization->user->name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif


                {{-- 3. Back to Super Admin Button --}}
                @if(session()->has('impersonator_id') && auth()->user()->hasRole('master_admin'))
                <div class="mt-0">
                    <a title="Back To Super Admin" href="{{ route('switch.back') }}" class="btn btn-sm btn-danger ms-3">
                        <i class="fa-solid fa-person-walking-arrow-loop-left"></i> 
                    </a>
                </div>
                @endif
            </div>



            <div class="d-flex order-lg-2 ms-auto header-right-icons">
                <div class="dropdown d-none">
                    <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                        <i class="fe fe-search"></i>
                    </a>
                    <div class="dropdown-menu header-search dropdown-menu-start">
                        <div class="input-group w-100 p-2">
                            <input type="text" class="form-control" placeholder="Search....">
                            <div class="input-group-text btn btn-primary">
                                <i class="fe fe-search" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SEARCH -->
                <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                    aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                </button>

                <div class="navbar navbar-collapse responsive-navbar p-0">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                        <div class="d-flex order-lg-2">
                            <!-- search input  -->
                            <div class="dropdown d-lg-none d-flex">
                                <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                                    <i class="fe fe-search"></i>
                                </a>
                                <div class="dropdown-menu header-search dropdown-menu-start">
                                    <div class="input-group w-100 p-2">
                                        <input type="text" class="form-control" placeholder="Search....">
                                        <div class="input-group-text btn btn-primary">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- theme mode  -->
                            <div class="d-flex country">
                                <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                    <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                    <span class="light-layout"><i class="fe fe-sun"></i></span>
                                </a>
                            </div>

                            <!-- Full Screen -->
                            <div class="dropdown d-flex">
                                <a class="nav-link icon full-screen-link nav-link-bg">
                                    <i class="fe fe-minimize fullscreen-button"></i>
                                </a>
                            </div>

                            <!-- SIDE-MENU -->
                            <div class="dropdown d-flex profile-1">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown"
                                    class="nav-link leading-none d-flex">
                                    <img src="../assets/images/users/21.jpg" alt="profile-user"
                                        class="avatar  profile-user brround cover-image">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <div class="drop-heading">
                                        <div class="text-center">
                                            <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ Auth::user()->name }}</h5>
                                            <small class="text-muted">
                                                {{ Auth::user()->roles->first()?->name ?? 'No Role' }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider m-0"></div>
                                    <a class="dropdown-item" href="profile.html">
                                        <i class="dropdown-icon fe fe-user"></i> Profile
                                    </a>
                                    <a class="dropdown-item" href="email-inbox.html">
                                        <i class="dropdown-icon fe fe-mail"></i> Inbox
                                        <span class="badge bg-danger rounded-pill float-end">5</span>
                                    </a>
                                    <a class="dropdown-item" href="lockscreen.html">
                                        <i class="dropdown-icon fe fe-lock"></i> Lockscreen
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="dropdown-icon fe fe-alert-circle"></i> Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /app-Header -->


<script>
    $(document).ready(function () {

        $('#session_org_id').on('change', function () {
            let orgId = $(this).val();
            if (!orgId) return;

            $.ajax({
                url: "{{ route('organization.switch') }}",
                method: "POST",
                data: { _token: "{{ csrf_token() }}", organization_id: orgId },
                success: function (res) {
                    toastr.success("Organization Switched");
                    window.location.href = "{{ route('dashboard') }}";
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON.message || "Switch failed");
                }
            });
        });


    });
</script>