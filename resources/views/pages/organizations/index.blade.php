@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center">
                <h1 class="page-title">Organization Management</h1>
                <button class="btn btn-primary btn-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fe fe-plus me-2"></i>Register School
                </button>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom">
                    <h3 class="card-title text-muted">Filter Search</h3>
                </div>
                <div class="card-body">
                    <form id="filterForm" action="{{ route('organizations.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control" placeholder="School Name"
                                    value="{{ request('name') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="email" class="form-control" placeholder="Admin Email"
                                    value="{{ request('email') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control" placeholder="Phone"
                                    value="{{ request('phone') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control select2">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary px-4"><i
                                        class="fe fe-search me-1"></i>Search</button>
                                <a href="{{ route('organizations.index') }}" class="btn btn-light px-4"><i
                                        class="fe fe-refresh-cw me-1"></i>Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive" id="data-table-container">
                        <table class="table table-vcenter text-nowrap border-bottom mb-0">
                            <thead class="bg-light text-uppercase">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>School Info</th>
                                    <th>Subscription</th>
                                    <th class="text-center">Status</th>
                                    <th>Master Admin</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organizations as $index => $org)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($org->logo)
                                            <img src="{{ asset('storage/' . $org->logo) }}"
                                                class="avatar avatar-md bradius me-3">
                                            @else
                                            <div
                                                class="avatar avatar-md bradius bg-primary-transparent text-primary me-3 fw-bold">
                                                {{ substr($org->name, 0, 1) }}
                                            </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $org->name }}</h6>
                                                <small class="text-muted"><i class="fe fe-map-pin me-1"></i>{{
                                                    Str::limit($org->address, 20) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $org->subscription->plan_name ?? 'Trial' }}</div>
                                        <small
                                            class="text-{{ ($org->subscription?->days_remaining ?? 0) < 7 ? 'danger' : 'success' }}">
                                            Expires: {{ formatDate($org->subscription?->end_date) ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="onoffswitch2 d-inline-block">
                                            <input type="checkbox" id="statusToggle{{ $org->id }}"
                                                class="onoffswitch2-checkbox globalStatusToggle"
                                                data-url="{{ route('organizations.toggleStatus', $org->id) }}" {{
                                                $org->status ? 'checked' : '' }}>
                                            <label class="onoffswitch2-label" for="statusToggle{{ $org->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm brround bg-info-transparent text-info me-2">
                                                <i class="fe fe-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold fs-13">{{ $org->user?->name }}</div>
                                                <div class="small text-muted">{{ $org->user?->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-list">
                                            <button class="btn btn-sm btn-icon btn-info-light open-edit-modal"
                                                data-id="{{ $org->id }}" data-name="{{ $org->name }}"
                                                data-email="{{ $org->email }}" data-phone="{{ $org->phone }}"
                                                data-address="{{ $org->address }}"
                                                data-admin_name="{{ $org->user?->name }}"
                                                data-admin_phone="{{ $org->user?->phone }}"
                                                data-admin_email="{{ $org->user?->email }}">
                                                <i class="fe fe-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-primary-light open-password-modal"
                                                data-id="{{ $org->user?->id }}" data-name="{{ $org->user?->name }}">
                                                <i class="fe fe-lock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-danger-light trigger-delete"
                                                data-url="{{ route('organizations.destroy', $org->id) }}">
                                                <i class="fe fe-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">No Records Found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Register New Organization</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form id="CreateForm" class="ajax-form" action="{{ route('organizations.store') }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="fw-bold border-start border-4 border-primary ps-2">School Identity</h6>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label required">School Name</label>
                            <input type="text" name="name" class="form-control" data-rules="required|min:3|max:255">
                        </div>

                        <div class="col-md-12 mt-4 mb-3">
                            <h6 class="fw-bold border-start border-4 border-success ps-2 text-success">Master Admin
                                (Login Details)</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Admin Name</label>
                            <input type="text" name="admin_name" class="form-control" data-rules="required|min:3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Admin Phone</label>
                            <input type="text" name="admin_phone" class="form-control"
                                data-rules="required|digits|min:10|max:10">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Admin Email (Optional)</label>
                            <input type="email" name="admin_email" class="form-control" data-rules="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Password</label>
                            <input type="password" name="admin_password" id="pass" class="form-control"
                                data-rules="required|min:6">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Confirm Password</label>
                            <input type="password" name="admin_password_confirmation" class="form-control"
                                data-rules="required|same:admin_password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4">
                    <button type="button" class="btn btn-light rounded-pill px-5"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Edit Organization Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form method="POST" class="ajax-form">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">School Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                data-rules="required|min:3|max:255">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Account Status</label>
                            <select name="status" id="edit_status" class="form-control" data-rules="required">
                                <option value="1">Active (Allow Access)</option>
                                <option value="0">Inactive (Suspend Access)</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="text-primary fw-bold border-bottom pb-2">Master Admin Details</h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="admin_name" id="edit_admin_name" class="form-control"
                                data-rules="required|min:3">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="admin_phone" id="edit_admin_phone" class="form-control"
                                data-rules="required|digits|min:10|max:10">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="admin_email" id="edit_admin_email" class="form-control"
                                data-rules="email">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4">Update Organization</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Reset Admin Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form id="PasswordForm" class="ajax-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-center">
                    <div class="avatar avatar-xl bg-danger-transparent text-danger mb-3">
                        <i class="fe fe-lock fs-30"></i>
                    </div>
                    <h5 class="mb-1 fw-bold" id="pass_user_name">Admin Name</h5>
                    <p class="text-muted small">Enter a new secure password for this user.</p>

                    <div class="text-start mt-4">
                        <div class="form-group mb-3">
                            <label class="form-label required">New Password</label>
                            <input type="password" name="password" id="new_password" class="form-control"
                                placeholder="Min 6 characters" data-rules="required|min:6">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Repeat password" data-rules="required|same:password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function () {
        // Edit Modal Data Filling
        $(document).on('click', '.open-edit-modal', function () {
            let btn = $(this);
            $('#edit_name').val(btn.data('name'));
            $('#edit_admin_name').val(btn.data('admin_name'));
            $('#edit_admin_phone').val(btn.data('admin_phone'));
            $('#edit_admin_email').val(btn.data('admin_email'));
            $('#editModal form').attr('action', `/organizations/${btn.data('id')}`);
            $('#editModal').modal('show');
        });

        // Toggle globalStatusToggle AJAX is handled by your global JS
    });

    $(document).on('click', '.open-password-modal', function () {
        let btn = $(this);
        let userId = btn.data('id');
        let userName = btn.data('name');

        if (!userId) {
            toastr.error("Admin user not found for this school.");
            return;
        }

        // Setup Modal content
        $('#pass_user_name').text(userName);
        $('#PasswordForm').attr('action', `/users/${userId}/update-password`);

        // Reset Form
        $('#PasswordForm')[0].reset();
        $('#PasswordForm').find('.is-invalid').removeClass('is-invalid');

        $('#passwordModal').modal('show');
    });
</script>
@endsection
@endsection