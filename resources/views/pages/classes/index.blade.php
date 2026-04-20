@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Classes.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Classes Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Class
                            </button>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                           
                            <div class="card-body">
                                <form id="filterForm" action="{{ route('classes.index') }}" method="GET">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Class Identity</label>
                                            <input type="text" name="search" class="form-control form-control"
                                                placeholder="Name or Code" value="{{ request('search') }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Timetable Group</label>
                                            <select name="group_id" class="form-control form-control select2">
                                                <option value="">All Groups</option>
                                                @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ request('group_id')==$group->id ?
                                                    'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Status</label>
                                            <select name="status" class="form-control form-control select2">
                                                <option value="">All Status</option>
                                                <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 ">
                                            <label class="form-label d-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary  px-3">
                                                <i class="fe fe-search me-1"></i> Search
                                            </button>
                                            <a href="{{ route('classes.index') }}"
                                                class="btn btn-light  px-3 btn-reset">
                                                <i class="fe fe-refresh-cw me-1"></i> Reset
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
                                            <th>Sr</th>
                                            <th data-orderby="true">Class Name</th>
                                            <th>Timetable Group</th> {{-- New Column --}}
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($classes as $index => $class)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold text-primary">{{ $class->name }}</td>
                                            <td>
                                                <span class="badge bg-info-transparent text-info p-2">
                                                    <i class="fe fe-calendar me-1"></i> {{ $class->timetableGroup->name
                                                    ?? 'Not Assigned' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($class->status)
                                                <span
                                                    class="badge bg-success-transparent text-success p-2 px-3">Active</span>
                                                @else
                                                <span class="badge bg-light text-muted p-2 px-3">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info-light edit-btn"
                                                    data-id="{{ $class->id }}" data-name="{{ $class->name }}"
                                                    data-group="{{ $class->timetable_group_id }}" {{-- New Data --}}
                                                    data-status="{{ $class->status ? 1 : 0 }}" title="Edit Class">
                                                    <i class="fe fe-edit"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger-light mx-2 trigger-delete"
                                                    data-url="{{ route('classes.destroy', $class->id) }}"
                                                    data-title="Delete Class"
                                                    data-message="This will delete the class. Proceed?">
                                                    <span class="fe fe-trash-2 fs-14"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No Classes found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $classes->links() }}
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
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Class</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('classes.store') }}"
                    data-reset="1" data-reload="1">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Grade 10"
                                data-rules="required">
                        </div>

                        {{-- Timetable Group Selection --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Timetable Group <span class="text-danger">*</span></label>
                            <select name="timetable_group_id" class="form-select select2-show-search"
                                data-rules="required">
                                <option value="">-- Select Group --</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Save Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Class</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="EditForm" class="ajax-form" method="POST" data-reload="1">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" data-rules="required">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Timetable Group <span class="text-danger">*</span></label>
                            <select name="timetable_group_id" id="edit_group_id" class="form-select select2-show-search"
                                data-rules="required">
                                <option value="">-- Select Group --</option>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4">Update Class</button>
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
            let name = btn.data('name');
            let groupId = btn.data('group');
            let status = btn.data('status');

            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_status').val(status);

            // Set Group and Trigger Select2 update
            $('#edit_group_id').val(groupId).trigger('change');

            $('#EditForm').attr('action', `/classes/${id}`);
            $('#editModal').modal('show');
        });
    });
</script>
@endsection