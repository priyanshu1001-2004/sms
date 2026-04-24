@extends('layouts.master')

@section('title', 'Schedule Profiles')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            {{-- PAGE HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Schedule Profiles</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Schedule Profiles</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- LEFT: CONFIGURATION FORM --}}
                <div class="col-md-4">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Define New Profile</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('timetable-groups.store') }}" method="POST" class="ajax-form"
                                data-reload="1" data-reset="1">
                                @csrf
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Profile Designation</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Junior Wing"
                                        data-rules="required">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Shift Category</label>
                                    <select name="type" class="form-control select2" data-rules="required">
                                        <option value="">Select Shift</option>
                                        <option value="morning">Morning Shift</option>
                                        <option value="afternoon">Afternoon Shift</option>
                                        <option value="evening">Evening Shift</option>
                                        <option value="full_day">Full Day</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                    <i class="fe fe-save me-2"></i> Register Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: MANAGEMENT TABLE --}}
                <div class="col-md-8">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Active Schedule Profiles</h3>
                            <span class="badge bg-primary-transparent text-primary rounded-pill px-3">
                                {{ count($groups) }} Profiles
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-hover border text-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Profile Designation</th>
                                            <th>Shift</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groups as $group)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="fw-bold text-dark">{{ $group->name }}</span></td>
                                            <td>
                                                <span class="badge bg-primary-transparent text-primary">
                                                    <i class="fe fe-sun me-1"></i> {{ ucfirst($group->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php $statusKey = $group->status ? 'active' : 'inactive'; @endphp
                                                <span
                                                    class="badge bg-{{ $group->status ? 'success' : 'danger' }}-transparent text-{{ $group->status ? 'success' : 'danger' }}">
                                                    {{ ucfirst($statusKey) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-info-light edit-profile-btn"
                                                        data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                        data-status="{{ $statusKey }}" data-bs-toggle="tooltip"
                                                        data-type="{{ $group->type }}"
                                                        title="Modify Configuration">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('timetable-groups.destroy', $group->id) }}">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No profiles found.</td>
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
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gray text-white">
                <h5 class="modal-title">Modify Schedule Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form id="editProfileForm" method="POST" class="ajax-form" data-reload="1">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold">Profile Designation</label>
                        <input type="text" name="name" id="edit_profile_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold">Operational Status</label>
                        <select name="status" id="edit_profile_status" class="form-control select2-no-search">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold">Shift Category</label>
                        <select name="type" id="edit_type" class="form-control " data-rules="required">
                            <option value="morning">Morning Shift</option>
                            <option value="afternoon">Afternoon Shift</option>
                            <option value="evening">Evening Shift</option>
                            <option value="full_day">Full Day</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-gray" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-5">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $(document).on('click', '.edit-profile-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const type = $(this).data('type');
            const status = $(this).data('status'); // Will be "active" or "inactive"

            // Update URL
            let updateUrl = "{{ route('timetable-groups.update', ':id') }}";
            $('#editProfileForm').attr('action', updateUrl.replace(':id', id));

            // Fill Inputs
            $('#edit_profile_name').val(name);
            $('#edit_type').val(type);

            // SET STATUS
            // Using strings "active"/"inactive" ensures the selection works perfectly
            $('#edit_profile_status').val(status).trigger('change');

            $('#editProfileModal').modal('show');
        });
    });
</script>
@endsection