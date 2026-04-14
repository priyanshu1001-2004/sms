@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Teachers.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Teachers Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Teacher
                            </button>
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
                                            <th>Mobile</th>
                                            <th>Gender</th>
                                            <th>Qualification</th>
                                            <th>Experience</th>
                                            <th>Joining Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teachers as $index => $teacher)
                                        <tr>
                                            <td>{{ $teachers->firstItem() + $index }}</td>
                                            <td>
                                                <img src="{{ $teacher->teacher_photo ? asset('storage/' . $teacher->teacher_photo) : asset('assets/images/users/default.png') }}"
                                                    alt="Photo" class="rounded-circle" width="40" height="40">
                                            </td>
                                            <td class="fw-bold">{{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </td>
                                            <td>{{ $teacher->email }}</td>
                                            <td>{{ $teacher->mobile_number ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($teacher->gender) }}</td>
                                            <td>{{ $teacher->qualification }}</td>
                                            <td>{{ $teacher->work_experience ?? 'Fresher' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($teacher->date_of_joining)->format('d-M-Y') }}
                                            </td>
                                            <td class="status-cell">
                                                <span
                                                    class="badge {{ $teacher->status ? 'bg-success' : 'bg-danger text-white' }}">
                                                    {{ $teacher->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    {{-- Edit Button --}}
                                                    <button type="button" class="btn btn-sm btn-info-light edit-btn"
                                                        data-id="{{ $teacher->id }}"
                                                        data-first_name="{{ $teacher->first_name }}"
                                                        data-last_name="{{ $teacher->last_name }}"
                                                        data-email="{{ $teacher->email }}"
                                                        data-mobile="{{ $teacher->mobile_number }}"
                                                        data-gender="{{ $teacher->gender }}"
                                                        data-dob="{{ $teacher->date_of_birth }}"
                                                        data-doj="{{ $teacher->date_of_joining }}"
                                                        data-qualification="{{ $teacher->qualification }}"
                                                        data-experience="{{ $teacher->work_experience }}"
                                                        data-marital_status="{{ $teacher->marital_status }}"
                                                        data-blood_group="{{ $teacher->blood_group }}"
                                                        data-current_address="{{ $teacher->current_address }}"
                                                        data-permanent_address="{{ $teacher->permanent_address }}"
                                                        data-note="{{ $teacher->note }}"
                                                        data-photo="{{ $teacher->teacher_photo ? asset('storage/' . $teacher->teacher_photo) : asset('assets/images/users/default.png') }}"
                                                        title="Edit Teacher">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    {{-- Change Password --}}
                                                    <button class="btn btn-sm btn-warning-light open-password-modal"
                                                        title="Change Password" data-id="{{ $teacher->user?->id }}"
                                                        data-name="{{ $teacher->first_name }} {{ $teacher->last_name }}">
                                                        <i class="fa-solid fa-key"></i>
                                                    </button>

                                                    {{-- Delete --}}
                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('teachers.destroy', $teacher->id) }}"
                                                        data-title="Delete Teacher"
                                                        data-message="Are you sure? This will soft-delete the teacher record.">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>

                                                    {{-- Toggle --}}
                                                    <div class="switch-toggle ms-1">
                                                        <p class="onoffswitch2 mb-0">
                                                            <input type="checkbox" id="status{{ $teacher->id }}"
                                                                class="onoffswitch2-checkbox globalStatusToggle"
                                                                data-url="{{ route('teachers.toggleStatus', $teacher->id) }}"
                                                                {{ $teacher->status ? 'checked' : '' }}>
                                                            <label class="onoffswitch2-label"
                                                                for="status{{ $teacher->id }}"></label>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">No teachers found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $teachers->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Teacher</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('teachers.store') }}"
                    enctype="multipart/form-data" data-reload="1">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">Personal Details</h6>
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
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" data-rules="required">
                        </div>

                        <div class="col-12 mt-3">
                            <h6 class="text-primary border-bottom pb-2">Professional & Contact</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" data-rules="required|email">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" class="form-control" data-rules="required">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_joining" class="form-control" data-rules="required"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Qualification <span class="text-danger">*</span></label>
                            <input type="text" name="qualification" class="form-control" data-rules="required"
                                placeholder="e.g. B.Ed, M.Sc">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Work Experience</label>
                            <input type="text" name="work_experience" class="form-control" placeholder="e.g. 5 Years">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Address <span class="text-danger">*</span></label>
                            <textarea name="current_address" class="form-control" rows="2"
                                data-rules="required"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea name="permanent_address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Login Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" data-rules="required|min:6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teacher Photo</label>
                            <input type="file" name="teacher_photo" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Resume (PDF)</label>
                            <input type="file" name="resume" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4">Save Teacher</button>
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
                <h5 class="modal-title">Edit Teacher Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="EditForm" class="ajax-form" method="POST" enctype="multipart/form-data" data-reload="1">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-info border-bottom pb-2">Personal Details</h6>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                        </div>

                        <div class="col-12 mt-3">
                            <h6 class="text-info border-bottom pb-2">Professional & Contact</h6>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control"
                                data-rules="required|email">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile_number" id="edit_mobile_number" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Joining</label>
                            <input type="date" name="date_of_joining" id="edit_date_of_joining" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" id="edit_qualification" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Work Experience</label>
                            <input type="text" name="work_experience" id="edit_experience" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" id="edit_marital_status" class="form-select">
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Address</label>
                            <textarea name="current_address" id="edit_current_address" class="form-control"
                                rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea name="permanent_address" id="edit_permanent_address" class="form-control"
                                rows="2"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teacher Photo</label>
                            <input type="file" name="teacher_photo" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3 text-center">
                            <label class="d-block">Current Photo</label>
                            <img id="edit_photo_preview" src="" class="img-thumbnail" style="height: 60px;">
                        </div>
                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info px-4">Update Teacher Profile</button>
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
                <h5 class="modal-title">Change Password - <span id="pass_user_name"></span></h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="PasswordForm" class="ajax-form" method="POST" data-reload="0">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label class="required">New Password</label>
                        <input type="password" name="password" id="new_password" class="form-control"
                            placeholder="Enter new password" data-rules="required|min:6">
                    </div>

                    <div class="form-group mb-3">
                        <label class="required">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Confirm new password" data-rules="required|same:password">
                    </div>

                    <div class="modal-footer border-top px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Update Password</button>
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

            // 1. Set Form Action URL
            $('#EditForm').attr('action', `/teachers/${id}`);

            // 2. Populate All Fields
            $('#edit_first_name').val(btn.data('first_name'));
            $('#edit_last_name').val(btn.data('last_name'));
            $('#edit_gender').val(btn.data('gender'));
            $('#edit_date_of_birth').val(btn.data('dob')); // YYYY-MM-DD

            $('#edit_email').val(btn.data('email'));
            $('#edit_mobile_number').val(btn.data('mobile'));
            $('#edit_date_of_joining').val(btn.data('doj')); // YYYY-MM-DD

            $('#edit_qualification').val(btn.data('qualification'));
            $('#edit_experience').val(btn.data('experience'));
            $('#edit_marital_status').val(btn.data('marital_status'));

            $('#edit_current_address').val(btn.data('current_address'));
            $('#edit_permanent_address').val(btn.data('permanent_address'));

            // 3. Photo Preview
            $('#edit_photo_preview').attr('src', btn.data('photo'));

            // 4. Open Modal
            $('#editModal').modal('show');
        });

        $(document).on('click', '.open-password-modal', function () {
            let userId = $(this).data('id'); // User ID (from users table)
            let userName = $(this).data('name');

            // Route ke mutabik URL set karein
            $('#PasswordForm').attr('action', '/teachers/' + userId + '/update-password');

            // Modal mein user ka naam dikhane ke liye (ID match honi chahiye)
            $('#pass_user_name').text(userName);

            // Form reset karein taaki purana input na dikhe
            $('#PasswordForm')[0].reset();

            $('#passwordModal').modal('show');
        });
    });
</script>
@endsection