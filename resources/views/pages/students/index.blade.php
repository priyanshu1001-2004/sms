@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Students.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Students Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Student
                            </button>
                        </div>

                        <div class="card mb-4">
                            
                            <div class="card-body">
                                <form id="filterForm" action="{{ route('students.index') }}" method="GET">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Student Info</label>
                                            <input type="text" name="search" class="form-control form-control"
                                                placeholder="Name or Adm. No." value="{{ request('search') }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Academic Class</label>
                                            <select name="class_id" class="form-control form-control select2">
                                                <option value="">All Classes</option>
                                                @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ request('class_id')==$class->id ?
                                                    'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Admission Date</label>
                                            <input type="date" name="adm_date" class="form-control form-control"
                                                value="{{ request('adm_date') }}">
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label text-muted small">Gender</label>
                                            <select name="gender" class="form-control form-control-sm select2">
                                                <option value="">All</option>
                                                <option value="Male" {{ request('gender')=='Male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="Female" {{ request('gender')=='Female' ? 'selected' : ''
                                                    }}>Female</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Current Status</label>
                                            <select name="status" class="form-control form-control-sm select2">
                                                <option value="">All Status</option>
                                                <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label d-block">&nbsp;</label> <button type="submit"
                                                class="btn btn-primary btn-sm px-3">
                                                <i class="fe fe-search"></i> Search
                                            </button>
                                            <a href="{{ route('students.index') }}"
                                                class="btn btn-light btn-sm px-3 btn-reset">
                                                <i class="fe fe-refresh-cw"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Photo</th>
                                            <th data-orderby="true">Name</th>
                                            <th>Email</th>
                                            <th>Admission <br> Number</th>
                                            <th data-orderby="true">Roll <br> Number</th>
                                            <th data-orderby="true">Class</th>
                                            <th>Gender</th>
                                            <th>Date of <br> Birth</th>
                                            <th>Mobile Number</th>
                                            <th>Admisson Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $index => $student)
                                        <tr>
                                            <td>{{ $students->firstItem() + $index }}</td>
                                            <td>
                                                @if($student->student_photo)
                                                <img src="{{ asset('storage/' . $student->student_photo) }}" alt="Photo"
                                                    class="rounded-circle" width="40" height="40"
                                                    onerror="this.src='{{ asset('assets/images/users/default.png') }}'">
                                                @else
                                                <img src="{{ asset('assets/images/users/default.png') }}" alt="Default"
                                                    class="rounded-circle" width="40" height="40">
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}
                                            </td>
                                            <td>{{ $student->email ?? 'N/A' }}</td>
                                            <td>{{ $student->admission_number }}</td>
                                            <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                            <td>{{ $student->class->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($student->gender) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') }}
                                            </td>
                                            <td>{{ $student->mobile_number ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($student->admission_date)->format('d-M-Y') }}
                                            </td>
                                            <td class="status-cell">
                                                <span
                                                    class="badge {{ $student->status ? 'bg-success' : 'bg-danger text-white' }}">
                                                    {{ $student->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    {{-- Edit Button --}}
                                                    <button type="button" class="btn btn-sm btn-info-light edit-btn"
                                                        data-id="{{ $student->id }}"
                                                        data-admission_number="{{ $student->admission_number }}"
                                                        data-roll_number="{{ $student->roll_number }}"
                                                        data-class_id="{{ $student->class_id }}"
                                                        data-admission_date="{{ $student->admission_date }}"
                                                        data-first_name="{{ $student->first_name }}"
                                                        data-last_name="{{ $student->last_name }}"
                                                        data-gender="{{ $student->gender }}"
                                                        data-dob="{{ $student->date_of_birth }}"
                                                        data-religion="{{ $student->religion }}"
                                                        data-caste="{{ $student->caste }}"
                                                        data-mobile="{{ $student->mobile_number }}"
                                                        data-blood_group="{{ $student->blood_group }}"
                                                        data-photo="{{ $student->student_photo ? asset('storage/' . $student->student_photo) : asset('assets/images/users/default.png') }}"
                                                        title="Edit Student">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    {{-- Change Password Button --}}
                                                    <button class="btn btn-sm btn-warning-light open-password-modal"
                                                        title="Change Password" data-id="{{ $student->user?->id }}"
                                                        data-name="{{ $student->first_name }} {{ $student->last_name }}">
                                                        <i class="fa-solid fa-key"></i>
                                                    </button>

                                                    {{-- Delete Button --}}
                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('students.destroy', $student->id) }}"
                                                        data-title="Delete Student"
                                                        data-message="Are you sure? This will soft-delete the student record.">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>

                                                    {{-- Global Status Toggle --}}
                                                    <div class="switch-toggle ms-1">
                                                        <p class="onoffswitch2 mb-0">
                                                            <input type="checkbox" id="status{{ $student->id }}"
                                                                class="onoffswitch2-checkbox globalStatusToggle"
                                                                data-url="{{ route('students.toggleStatus', $student->id) }}"
                                                                {{ $student->status ? 'checked' : '' }}>
                                                            <label class="onoffswitch2-label"
                                                                for="status{{ $student->id }}"></label>
                                                        </p>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1">
                                                        {{-- Other buttons like Edit, Delete... --}}

                                                        @if(is_null($student->parent_id))
                                                        {{-- Show Assign Button only if parent_id is NULL --}}
                                                        <button class="btn btn-sm btn-success-light assign-parent-btn"
                                                            data-student_id="{{ $student->id }}"
                                                            data-student_name="{{ $student->first_name }} {{ $student->last_name }}"
                                                            title="Assign Parent">
                                                            <i class="fe fe-link"></i> Assign Parent
                                                        </button>
                                                        @else
                                                        <!-- <span
                                                            class="btn btn-sm btn-success text-white disabled"
                                                            title="Already Linked">
                                                            <i class="fe fe-user-check"></i>
                                                        </span> -->
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="13" class="text-center text-muted">No students found for this
                                                organization.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="assignParentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Assign Parent to: <span id="target_student_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('students.assignParent') }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="assign_student_id">

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Select Parent <span class="text-danger">*</span></label>
                        <select name="parent_id" id="parent_select" class="form-control select2-modal  "
                            data-rules="required">
                            <option value="">-- Search/Select Parent --</option>
                            @foreach($allParents as $parent)
                            <option value="{{ $parent->id }}">
                                {{ $parent->first_name }} {{ $parent->last_name }} ({{ $parent->mobile_number }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">Assign Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl"> {{-- XL for better field layout --}}
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Student Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('students.store') }}"
                    enctype="multipart/form-data" data-reload="1">
                    @csrf
                    <div class="row">
                        {{-- Academic Section --}}
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Academic Information</h6>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select select2" data-rules="required">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control" data-rules="required"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Personal Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-primary border-bottom pb-2">Personal Information</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" data-rules="required">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" data-rules="required">
                        </div>

                        {{-- Social Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-primary border-bottom pb-2">Social & Contact</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Caste</label>
                            <input type="text" name="caste" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_number" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Select</option>
                                <option value="A+">A+</option>
                                <option value="O+">O+</option>
                                <option value="B+">B+</option>
                                <option value="AB+">AB+</option>
                                <option value="A-">A-</option>
                                <option value="B-">B-</option>
                            </select>
                        </div>

                        {{-- Account Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-primary border-bottom pb-2">Account Details</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email (Login ID)</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Student Photo</label>
                            <input type="file" name="student_photo" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Complete Admission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="EditForm" class="ajax-form" method="POST" enctype="multipart/form-data" data-reload="1">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Academic Section --}}
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2">Academic Information</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                            <input type="text" name="admission_number" id="edit_admission_number" class="form-control"
                                data-rules="required" disabled>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_number" id="edit_roll_number" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="edit_class_id" class="form-select select2"
                                data-rules="required">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" id="edit_admission_date" class="form-control"
                                data-rules="required">
                        </div>

                        {{-- Personal Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-info border-bottom pb-2">Personal Information</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="edit_gender" class="form-select" data-rules="required">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control"
                                data-rules="required">
                        </div>

                        {{-- Social Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-info border-bottom pb-2">Social & Contact</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" id="edit_religion" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Caste</label>
                            <input type="text" name="caste" id="edit_caste" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_number" id="edit_mobile_number" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" id="edit_blood_group" class="form-select">
                                <option value="">Select</option>
                                <option value="A+">A+</option>
                                <option value="O+">O+</option>
                                <option value="B+">B+</option>
                                <option value="AB+">AB+</option>
                            </select>
                        </div>

                        {{-- Image Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-info border-bottom pb-2">Update Photo</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Change Student Photo</label>
                            <input type="file" name="student_photo" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 text-center">
                            <label class="d-block mb-1">Current Photo</label>
                            <img id="edit_photo_preview" src="" class="img-thumbnail" style="height: 80px;">
                        </div>
                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info px-4">Update Student Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- update password modal  -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Change Password - <span id="pass_user_name_display"></span></h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="PasswordForm" class="ajax-form" method="POST" data-reload="0">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label class="required fw-bold">New Password</label>
                        <input type="password" name="password" id="new_password" class="form-control"
                            placeholder="Enter new password" data-rules="required|min:6">
                    </div>

                    <div class="form-group mb-3">
                        <label class="required fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Confirm new password" data-rules="required|same:password">
                    </div>

                    <div class="modal-footer border-top px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger px-4">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $(document).on('click', '.edit-btn', function () {
            let btn = $(this);
            let id = btn.data('id');

            // Set Form Action
            $('#EditForm').attr('action', `/students/${id}`);

            // Populate Fields
            $('#edit_admission_number').val(btn.data('admission_number'));
            $('#edit_roll_number').val(btn.data('roll_number'));
            $('#edit_class_id').val(btn.data('class_id')).trigger('change');
            $('#edit_admission_date').val(btn.data('admission_date'));
            $('#edit_first_name').val(btn.data('first_name'));
            $('#edit_last_name').val(btn.data('last_name'));
            $('#edit_gender').val(btn.data('gender'));
            $('#edit_date_of_birth').val(btn.data('dob'));
            $('#edit_religion').val(btn.data('religion'));
            $('#edit_caste').val(btn.data('caste'));
            $('#edit_mobile_number').val(btn.data('mobile'));
            $('#edit_blood_group').val(btn.data('blood_group'));

            // Photo Preview
            $('#edit_photo_preview').attr('src', btn.data('photo'));

            $('#editModal').modal('show');
        });

        $(document).on('click', '.open-password-modal', function () {
            let userId = $(this).data('id');
            let userName = $(this).data('name');

            $('#PasswordForm').attr('action', '/students/' + userId + '/update-password');

            $('#pass_user_name_display').text(userName);
            $('#passwordModal').modal('show');
        });
    });

    $(document).on('click', '.assign-parent-btn', function () {
        $('#assign_student_id').val($(this).data('student_id'));
        $('#target_student_name').text($(this).data('student_name'));
        $('#assignParentModal').modal('show');
    });
</script>
@endsection