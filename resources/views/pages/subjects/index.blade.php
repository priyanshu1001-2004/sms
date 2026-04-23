@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Subjects.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">




                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Subjects Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Subject
                            </button>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">

                            <div class="card-body">
                                <form id="filterForm" action="{{ route('subjects.index') }}" method="GET">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Subject Name/Code</label>
                                            <input type="text" name="search" class="form-control form-control"
                                                placeholder="e.g. Mathematics, MATH101" value="{{ request('search') }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label text-muted small">Subject Type</label>
                                            <select name="type" class="form-control form-control-sm select2">
                                                <option value="">All Types</option>
                                                <option value="Theory" {{ request('type')=='Theory' ? 'selected' : ''
                                                    }}>Theory</option>
                                                <option value="Practical" {{ request('type')=='Practical' ? 'selected'
                                                    : '' }}>Practical</option>
                                                <option value="Both" {{ request('type')=='Both' ? 'selected' : '' }}>
                                                    Both (Theory & Practical)</option>
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

                                        <div class="col-md-4">
                                            <label class="form-label d-block">&nbsp;</label> <button type="submit"
                                                class="btn btn-primary  px-4">
                                                <i class="fe fe-search me-1"></i> Search
                                            </button>
                                            <a href="{{ route('subjects.index') }}"
                                                class="btn btn-light px-4 btn-reset">
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
                                            <th data-orderby="true">Subject Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subjects as $index => $subject)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-bold text-primary">{{ $subject->name }}</td>
                                            <td class="fw-bold text-primary">{{ $subject->type }}</td>
                                            <td>
                                                @if($subject->status)
                                                <span
                                                    class="badge bg-success-transparent text-success p-2 px-3">Active</span>
                                                @else
                                                <span class="badge bg-light text-muted p-2 px-3">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info-light edit-btn"
                                                    data-id="{{ $subject->id }}" data-name="{{ $subject->name }}"
                                                    data-status="{{ $subject->status ? 1 : 0 }}"
                                                    data-type="{{ $subject->type }}" title="Edit Subject">
                                                    <i class="fe fe-edit"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger-light mx-2 trigger-delete"
                                                    data-url="{{ route('subjects.destroy', $subject->id) }}"
                                                    data-title="Delete Subject"
                                                    data-message="This will delete the subject. Proceed?">
                                                    <span class="fe fe-trash-2 fs-14"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No subjects found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $subjects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Subject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('subjects.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="name">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Subject Name"
                                data-rules="required">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="practical">Practical</option>
                                <option value="theory">Theory</option>
                                <option value="both">Both</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs">
        <div class="modal-content">
            <div class="modal-header bg-gray text-white">
                <h5 class="modal-title">Edit Subject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="EditForm" class="ajax-form" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="row">

                        <div class="col-12 mb-3">
                            <label class="form-label" for="name">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" placeholder=""
                                data-rules="required">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" id="edit_type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="practical">Practical</option>
                                <option value="theory">Theory</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gray text-white px-4">Update</button>
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
            let status = btn.data('status');
            let type = btn.data('type');

            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_type').val(type);

            $('#edit_status').val(status).trigger('change');

            $('#editModal form').attr('action', `/subjects/${id}`);

            $('#editModal').modal('show');
        });
    });

</script>

@endsection