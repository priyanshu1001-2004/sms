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



                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sr</th>
                                            <th data-orderby="true">Class Name</th>
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
                                                    data-status="{{ $class->status ? 1 : 0 }}" title="Edit Class">
                                                    <i class="fe fe-edit"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger-light mx-2 trigger-delete"
                                                    data-url="{{ route('classes.destroy', $class->id) }}"
                                                    data-title="Delete Class"
                                                    data-message="This will delete the class Proceed?">
                                                    <span class="fe fe-trash-2 fs-14"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No Classes found.</td>
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


<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Class</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('classes.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="name">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Grade 10"
                                data-rules="required">
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs">
        <div class="modal-content">
            <div class="modal-header bg-gray text-white">
                <h5 class="modal-title">Edit Academic Year</h5>
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

                        <div class="col-md-12 mb-3">
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gray text-white px-4">Update Year</button>
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

            // Fill fields
            $('#edit_id').val(id);
            $('#edit_name').val(name);

            // Ensure status is handled as a string/number correctly for the select
            $('#edit_status').val(status).trigger('change');

            // Update action URL
            $('#editModal form').attr('action', `/classes/${id}`);

            // Show modal
            $('#editModal').modal('show');
        });
    });

</script>

@endsection