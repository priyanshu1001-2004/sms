@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please manage admin users from this panel.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Parent Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Parent
                            </button>
                        </div>

                        <div class="card-body">
                            <form id="filterForm" action="{{ route('parents.index') }}" method="GET">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <label class="form-label text-muted small">Parent Name</label>
                                        <input type="text" name="search" class="form-control form-control"
                                            placeholder="Search name..." value="{{ request('search') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label text-muted small">Contact Info</label>
                                        <input type="text" name="contact" class="form-control form-control"
                                            placeholder="Email or Mobile" value="{{ request('contact') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label text-muted small">Relation</label>
                                        <select name="relation" class="form-control form-control select2">
                                            <option value="">All Relations</option>
                                            <option value="Father" {{ request('relation')=='Father' ? 'selected' : ''
                                                }}>Father</option>
                                            <option value="Mother" {{ request('relation')=='Mother' ? 'selected' : ''
                                                }}>Mother</option>
                                            <option value="Guardian" {{ request('relation')=='Guardian' ? 'selected'
                                                : '' }}>Guardian</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label text-muted small">Occupation</label>
                                        <input type="text" name="occupation" class="form-control form-control"
                                            placeholder="e.g. Engineer" value="{{ request('occupation') }}">
                                    </div>

                                    <div class="col-md-1">
                                        <label class="form-label text-muted small">Status</label>
                                        <select name="status" class="form-control form-control select2">
                                            <option value="">All</option>
                                            <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label d-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary px-3">
                                            <i class="fe fe-search"></i> Search
                                        </button>
                                        <a href="{{ route('parents.index') }}" class="btn btn-light  px-3 btn-reset">
                                            <i class="fe fe-refresh-cw"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Gender</th>
                                            <th>Phone</th>
                                            <th>Occupation</th>
                                            <th>Relation</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($parents as $index => $parent)
                                        <tr>
                                            <td>{{ $parents->firstItem() + $index }}</td>
                                            <td>
                                                <img src="{{ $parent->parent_photo ? asset('storage/' . $parent->parent_photo) : asset('assets/images/users/default.png') }}"
                                                    class="rounded-circle" width="35" height="35">
                                            </td>
                                            <td class="fw-bold">{{ $parent->first_name }} {{ $parent->last_name }}</td>
                                            <td>{{ $parent->email }}</td>
                                            <td>{{ ucfirst($parent->gender) }}</td>
                                            <td>{{ $parent->mobile_number ?? 'N/A' }}</td>
                                            <td>{{ $parent->occupation ?? 'N/A' }}</td>
                                            <td><span class="badge bg-primary">{{ $parent->relation
                                                    }}</span></td>
                                            <td>
                                                <div class="status-cell">
                                                    <span
                                                        class="badge {{ $parent->status ? 'bg-success' : 'bg-danger text-white' }} p-2">
                                                        {{ $parent->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button class="btn btn-sm btn-info-light edit-btn"
                                                        data-id="{{ $parent->id }}"
                                                        data-first_name="{{ $parent->first_name }}"
                                                        data-last_name="{{ $parent->last_name }}"
                                                        data-email="{{ $parent->email }}"
                                                        data-gender="{{ $parent->gender }}"
                                                        data-mobile="{{ $parent->mobile_number }}"
                                                        data-occupation="{{ $parent->occupation }}"
                                                        data-relation="{{ $parent->relation }}"
                                                        data-photo="{{ $parent->parent_photo ? asset('storage/' . $parent->parent_photo) : asset('assets/images/users/default.png') }}">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-warning-light open-password-modal"
                                                        title="Change Password" data-id="{{ $parent->user?->id }}"
                                                        data-name="{{ $parent->first_name }} {{ $parent->last_name }}">
                                                        <i class="fa-solid fa-key"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('parents.destroy', $parent->id) }}"
                                                        data-title="Delete Parent"
                                                        data-message="Are you sure you want to remove this parent?">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>

                                                    <div class="switch-toggle">
                                                        <p class="onoffswitch2 mb-0">
                                                            <input type="checkbox" id="status{{ $parent->id }}"
                                                                class="onoffswitch2-checkbox globalStatusToggle"
                                                                data-url="{{ route('parents.toggleStatus', $parent->id) }}"
                                                                {{ $parent->status ? 'checked' : '' }}>
                                                            <label class="onoffswitch2-label"
                                                                for="status{{ $parent->id }}"></label>
                                                        </p>
                                                    </div>

                                                    <button title="View Students" class="btn btn-sm btn-primary-light view-children"
                                                        data-id="{{ $parent->id }}"
                                                        data-name="{{ $parent->first_name }} {{ $parent->last_name }}">
                                                        <i class="fe fe-users"></i> 
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No Parents found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $parents->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ================= ADD MODAL ================= --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Parent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('parents.store') }}" class="ajax-form"
                    enctype="multipart/form-data" data-reload="1">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="Enter first name"
                                data-rules="required|max:255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" placeholder="Enter last name"
                                data-rules="required|max:255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="parent@example.com"
                                data-rules="required|email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" class="form-control"
                                placeholder="Enter phone number" data-rules="required">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" data-rules="required">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Relation <span class="text-danger">*</span></label>
                            <select name="relation" class="form-select" data-rules="required">
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Guardian">Guardian</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control" placeholder="e.g. Engineer">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="parent_photo" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Login Password"
                                data-rules="required|min:6">
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Save Parent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================= EDIT MODAL ================= --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Parent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="EditForm" method="POST" class="ajax-form" enctype="multipart/form-data" data-reload="1">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3 text-center">
                            <img id="edit_photo_preview" src="" class="rounded-circle border" width="70" height="70">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Update Profile Photo</label>
                            <input type="file" name="parent_photo" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email (Login ID)</label>
                            <input type="email" name="email" id="edit_email" class="form-control"
                                data-rules="required|email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="mobile_number" id="edit_mobile_number" class="form-control"
                                data-rules="required">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Relation</label>
                            <select name="relation" id="edit_relation" class="form-select">
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Guardian">Guardian</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" id="edit_occupation" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info text-white px-4">Update</button>
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


<div class="modal fade" id="viewChildrenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Students linked to: <span id="display_parent_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body p-0">
                <ul class="list-group list-group-flush" id="children_list">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(document).on('click', '.edit-btn', function () {
        let btn = $(this);
        let id = btn.data('id');

        $('#EditForm').attr('action', '/parents/' + id);

        // Populate fields
        $('#edit_first_name').val(btn.data('first_name'));
        $('#edit_last_name').val(btn.data('last_name'));
        $('#edit_email').val(btn.data('email'));
        $('#edit_mobile_number').val(btn.data('mobile'));
        $('#edit_gender').val(btn.data('gender'));
        $('#edit_relation').val(btn.data('relation'));
        $('#edit_occupation').val(btn.data('occupation'));
        $('#edit_photo_preview').attr('src', btn.data('photo'));

        $('#editModal').modal('show');
    });

    $(document).on('click', '.open-password-modal', function () {
        let userId = $(this).data('id'); // user_id from the button
        let userName = $(this).data('name');

        if (!userId) {
            toastr.error("No user account associated with this parent.");
            return;
        }

        // Set form action and user name
        $('#PasswordForm').attr('action', `/students/${userId}/update-password`);
        $('#pass_user_name_display').text(userName);

        // Reset and Show
        $('#PasswordForm')[0].reset();
        $('#passwordModal').modal('show');
    });

    // 1. Fetch Children for Parent
    $(document).on('click', '.view-children', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        $('#display_parent_name').text(name);
        $('#children_list').html('<li class="list-group-item text-center">Loading...</li>');

        $.get(`/parents/${id}/students`, function (res) {
            let html = '';
            if (res.students.length > 0) {
                res.students.forEach(s => {
                    html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold text-dark">${s.first_name} ${s.last_name}</span>
                        <div class="small text-muted">Roll No: ${s.roll_number} | Class: ${s.class.name}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger unlink-student-btn" 
                        data-id="${s.id}" 
                        title="Unlink Student">
                        <i class="fe fe-x"></i> Remove
                    </button>
                </li>`;
                });
            } else {
                html = '<li class="list-group-item text-center p-4 text-muted">No students assigned to this parent.</li>';
            }
            $('#children_list').html(html);
            $('#viewChildrenModal').modal('show');
        });
    });

    // Handle Unlink Click
    $(document).on('click', '.unlink-student-btn', function () {
        let studentId = $(this).data('id');
        let listItem = $(this).closest('li');

        if (confirm("Are you sure you want to remove this student from this parent?")) {
            $.ajax({
                url: `/students/unlink-parent/${studentId}`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status) {
                        toastr.success(res.message);
                        listItem.fadeOut(300, function () {
                            $(this).remove();
                            if ($('#children_list li').length === 0) {
                                $('#children_list').html('<li class="list-group-item text-center text-muted">No students assigned.</li>');
                            }
                        });
                    }
                }
            });
        }
    });
</script>
@endsection