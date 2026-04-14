@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">

        <div class="main-container container-fluid">


            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Basic Datatable</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add Organization
                            </button>
                        </div>
                        <!-- // fillter form  -->
                        <div class="px-5 py-5">
                            <form id="filterForm" action="{{ route('organizations.index') }}" method="GET">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <input type="text" name="name" class="form-control" placeholder="Name"
                                            value="{{ request('name') }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-2">
                                        <input type="text" name="email" class="form-control" placeholder="Email"
                                            value="{{ request('email') }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-2">
                                        <input type="text" name="phone" class="form-control" placeholder="Phone"
                                            value="{{ request('phone') }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : ''
                                                }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary shadow-sm">
                                            <i class="fe fe-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('organizations.index') }}"
                                            class="btn btn-secondary btn-reset">
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
                                            <th>Sr</th>
                                            <th>Logo</th>
                                            <th data-orderby="true">Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Admin</th>
                                            <th data-orderby="true" data-order-default="desc">Start Date</th>
                                            <th data-orderby="true">End Date</th>
                                            <th>Exp In</th>
                                            <th>Plan</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($organizations as $index => $organization)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($organization->logo)
                                                <img src="{{ asset('storage/' . $organization->logo) }}"
                                                    class="img-thumbnail" width="50">
                                                @else
                                                <span class="text-muted">No Logo</span>
                                                @endif
                                            </td>
                                            <td>{{ $organization->name }}</td>
                                            <td>{{ $organization->email }}</td>
                                            <td>{{ $organization->phone }}</td>

                                            <td class="status-cell">
                                                <span
                                                    class="badge bg-{{ $organization->status ? 'success' : 'danger' }}">
                                                    {{ $organization->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($organization->user)
                                                <div>
                                                    <strong>{{ $organization->user->name }}</strong><br>
                                                    <small>{{ $organization->user->email }}</small><br>
                                                    <small>{{ $organization->user->phone }}</small>
                                                </div>
                                                @else
                                                <span class="text-muted">No Admin</span>
                                                @endif
                                            </td>
                                            <td>{{ formatDate($organization?->subscription?->start_date) ?? 'N/A' }}</td>
                                            <td>{{ formatDate($organization?->subscription?->end_date) ?? 'N/A' }}</td>
                                            <td>
                                                @if($organization?->subscription?->days_remaining > 0)
                                                <span class="badge bg-success">
                                                    {{ $organization?->subscription?->days_remaining }} Days Left
                                                </span>
                                                @else
                                                <span class="badge bg-danger">Expired</span>
                                                @endif
                                            </td>
                                            <td>{{ $organization?->subscription?->plan_name }}</td>
                                            <td>
                                                <div class="d-flex align-items-center g-2">
                                                    <button class="btn text-primary btn-sm open-edit-modal" title="Edit"
                                                        data-id="{{ $organization->id }}"
                                                        data-name="{{ $organization->name }}"
                                                        data-email="{{ $organization->email }}"
                                                        data-phone="{{ $organization->phone }}"
                                                        data-status="{{ $organization->status ? '1' : '0' }}"
                                                        data-address="{{ $organization->address }}"
                                                        data-admin_name="{{ $organization->user?->name }}"
                                                        data-admin_phone="{{ $organization->user?->phone }}"
                                                        data-admin_email="{{ $organization->user?->email }}"
                                                        data-logo="{{ asset('storage/' . $organization->logo) }}">
                                                        <span class="fe fe-edit fs-14"></span>
                                                    </button>

                                                    <button class="btn btn-sm text-danger trigger-delete"
                                                        data-url="{{ route('organizations.destroy', $organization->id) }}"
                                                        data-title="Delete Organization"
                                                        data-message="This will delete the organization and all its users. Proceed?">
                                                        <span class="fe fe-trash-2 fs-14"></span>
                                                    </button>

                                                    <button class="btn text-danger btn-sm open-password-modal"
                                                        title="Change Password" data-id="{{ $organization->user?->id }}"
                                                        data-name="{{ $organization->user?->name }}">
                                                        <i class="fa-solid fa-key"></i>
                                                    </button>

                                                    <div class="switch-toggle d-flex align-items-center">
                                                        <p class="onoffswitch2 mb-0">
                                                            <input type="checkbox"
                                                                id="statusToggle{{ $organization->id }}" {{-- Unique ID
                                                                per row --}}
                                                                class="onoffswitch2-checkbox globalStatusToggle"
                                                                data-url="{{ route('organizations.toggleStatus', $organization->id) }}"
                                                                {{ $organization->status ? 'checked' : '' }}>

                                                            <label class="onoffswitch2-label"
                                                                for="statusToggle{{ $organization->id }}"></label>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        @empty

                                        <tr>
                                            <td colspan="10" class="text-center">No organizations found.</td>
                                        </tr>

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $organizations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->


            <!-- Create Organization Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Add Organization</h5>
                            <button type="button" class="btn-close text-danger" data-bs-dismiss="modal">x</button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <form id="CreateForm" class="ajax-form" method="POST"
                                action="{{ route('organizations.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row">

                                    <!-- Organization Details -->
                                    <div class="col-12 mb-2">
                                        <h6 class="text-primary">Organization Details</h6>
                                    </div>

                                    <!-- Name -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Name</label>
                                        <input type="text" name="name" class="form-control"
                                            data-rules="required|min:3|max:255">
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                            data-rules="email|max:255">
                                    </div>

                                    <!-- Organization Phone -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Organization Phone</label>
                                        <input type="text" name="phone" class="form-control"
                                            data-rules="digits|min:10|max:15">
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control" data-rules="required">
                                            <option value="1" selected>Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Address -->
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control" rows="2"
                                            data-rules="max:255"></textarea>
                                    </div>

                                    <!-- Logo -->
                                    <div class="form-group col-md-6 mb-3">
                                        <label>Logo</label>
                                        <input type="file" name="logo" class="form-control"
                                            data-rules="mimes:jpg,jpeg,png|max:2048">
                                    </div>

                                    <!-- Admin Details -->
                                    <div class="col-12 mt-3 mb-2">
                                        <h6 class="text-primary">Admin Details</h6>
                                    </div>

                                    <!-- Admin Name -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Admin Name</label>
                                        <input type="text" name="admin_name" class="form-control"
                                            data-rules="required|min:3|max:255">
                                    </div>

                                    <!-- ❗ Admin Phone (IMPORTANT FIX) -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Admin Phone</label>
                                        <input type="text" name="admin_phone" class="form-control"
                                            data-rules="required|digits|min:10|max:15">
                                    </div>

                                    <!-- Admin Email (optional) -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Admin Email</label>
                                        <input type="email" name="admin_email" class="form-control"
                                            data-rules="email|max:255">
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Password</label>
                                        <input type="password" name="admin_password" class="form-control"
                                            data-rules="required|min:6">
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Confirm Password</label>
                                        <input type="password" name="admin_password_confirmation" class="form-control"
                                            data-rules="required|same:admin_password">
                                    </div>

                                </div>

                                <!-- Footer -->
                                <div class="modal-footer border-top mt-3">
                                    <button type="button" class="btn btn-secondary" id="clearaddBtn">
                                        Clear
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        Save
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Edit Modal  -->
            <div class="modal fade" id="editModal">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-gray text-white">
                            <h5 class="modal-title">Edit Organization</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal">x</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" class="ajax-form" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')


                                <div class="row">

                                    <!-- Organization Details -->
                                    <div class="col-12 mb-2">
                                        <h6 class="text-primary">Organization Details</h6>
                                    </div>

                                    <!-- Name -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Name</label>
                                        <input type="text" name="name" id="edit_name" class="form-control"
                                            data-rules="required|min:3|max:255">
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Email</label>
                                        <input type="email" name="email" id="edit_email" class="form-control"
                                            data-rules="email|max:255">
                                    </div>

                                    <!-- Organization Phone -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Organization Phone</label>
                                        <input type="text" name="phone" id="edit_phone" class="form-control"
                                            data-rules="digits|min:10|max:15">
                                    </div>

                                    <!-- Status -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Status</label>
                                        <select name="status" id="edit_status" class="form-control"
                                            data-rules="required">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Address -->
                                    <div class="form-group col-md-12 mb-2">
                                        <label>Address</label>
                                        <textarea name="address" id="edit_address" class="form-control" rows="2"
                                            data-rules="max:255"></textarea>
                                    </div>

                                    <!-- Logo -->
                                    <div class="form-group col-md-12 d-flex mb-3">
                                        <div class="col-6">
                                            <label>Logo</label>
                                            <input type="file" name="logo" id="edit_logo" class="form-control"
                                                accept="image/*">
                                        </div>

                                        <div class="mt-4 col-6">
                                            <img id="logo_preview" src="" alt="Current Logo"
                                                style="max-height: 50px; display: none;">
                                        </div>
                                    </div>

                                    <!-- Admin Details -->
                                    <div class="col-12 mt-3 mb-2">
                                        <h6 class="text-primary">Admin Details</h6>
                                    </div>

                                    <!-- Admin Name -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Admin Name</label>
                                        <input type="text" name="admin_name" id="edit_admin_name" class="form-control"
                                            data-rules="required|min:3|max:255">
                                    </div>

                                    <!-- ❗ Admin Phone (IMPORTANT FIX) -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label class="required">Admin Phone</label>
                                        <input type="text" name="admin_phone" id="edit_admin_phone" class="form-control"
                                            data-rules="required|digits|min:10|max:15">
                                    </div>

                                    <!-- Admin Email (optional) -->
                                    <div class="form-group col-md-6 mb-2">
                                        <label>Admin Email</label>
                                        <input type="email" name="admin_email" id="edit_admin_email"
                                            class="form-control" data-rules="email|max:255">
                                    </div>

                                </div>

                                <!-- Footer -->
                                <div class="modal-footer border-top mt-3">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn bg-gray text-white">
                                        Update
                                    </button>
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

        </div>
    </div>
</div>



@section('scripts')
<script>
    $(document).ready(function () {

        $(document).on('click', '.open-edit-modal', function () {
            let btn = $(this);
            let status = btn.data('status');


            // Populate standard fields
            $('#edit_name').val(btn.data('name'));
            $('#edit_email').val(btn.data('email'));
            $('#edit_phone').val(btn.data('phone'));
            $('#edit_status').val(status);
            $('#edit_address').val(btn.data('address'));
            $('#edit_admin_phone').val(btn.data('admin_phone'));
            $('#edit_admin_name').val(btn.data('admin_name'));
            $('#edit_admin_email').val(btn.data('admin_email'));

            if (btn.data('logo')) {
                $('#logo_preview').attr('src', btn.data('logo')).show();
            } else {
                $('#logo_preview').hide();
            }

            // Set Form Action
            $('#editModal form').attr('action', `/organizations/${btn.data('id')}`);

            // Show Modal
            $('#editModal').modal('show');
        });

        $(document).on('click', '.open-password-modal', function () {
            let btn = $(this);
            let userId = btn.data('id');
            let userName = btn.data('name');

            if (!userId) {
                toastr.error("Admin user not found for this organization.");
                return;
            }

            // Set User Name in Title
            $('#pass_user_name').text(userName);

            // Set Dynamic Action URL (assuming you have a route for this)
            $('#PasswordForm').attr('action', `/users/${userId}/update-password`);

            // Reset form fields
            $('#PasswordForm')[0].reset();
            $('#PasswordForm').find('.is-invalid').removeClass('is-invalid');
            $('#PasswordForm').find('.invalid-feedback').remove();

            $('#passwordModal').modal('show');
        });

    })


</script>

@endsection

@endsection