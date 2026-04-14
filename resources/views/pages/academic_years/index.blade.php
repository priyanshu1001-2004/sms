@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Academic Years.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">




                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Academic Years Management</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Academic Year
                            </button>
                        </div>



                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sr</th>
                                            <th data-orderby="true">Year Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($academicYears as $index => $item)
                                        <tr>
                                            <td>{{ $academicYears->firstItem() + $index }}</td>
                                            <td class="fw-bold text-primary">{{ $item->name }}</td>
                                            <td>{{ formatDate($item->start_date) }}</td>
                                            <td>{{ formatDate($item->end_date) }}</td>
                                            <td>
                                                @if($item->is_active)
                                                <span
                                                    class="badge bg-success-transparent text-success p-2 px-3">Active</span>
                                                @else
                                                <span class="badge bg-light text-muted p-2 px-3">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ formatDate($item->created_at) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info-light edit-btn"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-start_date="{{ $item->start_date }}"
                                                    data-end_date="{{ $item->end_date }}"
                                                    data-is_active="{{ $item->is_active }}" title="Edit Academic Year">
                                                    <i class="fe fe-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No academic years found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $academicYears->links() }}
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
                <h5 class="modal-title">Add Academic Year</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('academic-years.store') }}">
                    @csrf
                    <div class="row">


                        <div class="col-12 mb-3">
                            <label class="form-label" for="name">Year Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. 2025-26"
                                data-rules="required">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date">Start Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                data-rules="required|date">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_date">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                data-rules="required|date">
                        </div>

                        <div class="col-12 mb-3 mx-5">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">Set as Active Year</label>
                            </div>
                            <small class="text-muted">Activating this will automatically deactivate all other years for
                                this organization.</small>
                        </div>
                    </div>

                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Save Year</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
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
                            <label class="form-label" for="edit_name">Year Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                placeholder="e.g. 2025-26" data-rules="required">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_start_date">Start Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control"
                                data-rules="required|date">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="edit_end_date">End Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control"
                                data-rules="required|date">
                        </div>

                        <div class="col-12 mb-3 mx-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                    value="1">
                                <label class="form-check-label" for="edit_is_active">Set as Active Year</label>
                            </div>
                            <small class="text-muted">Note: Making this active will deactivate other years for this
                                organization.</small>
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
    $(document).on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let start = $(this).data('start_date');
        let end = $(this).data('end_date');
        let active = $(this).data('is_active');

        // 1. Set Form Action Dynamically
        $('#EditForm').attr('action', '/academic-years/' + id);

        // 2. Fill the inputs
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_start_date').val(start);
        $('#edit_end_date').val(end);

        // 3. Handle the switch
        if (active == 1) {
            $('#edit_is_active').prop('checked', true);
        } else {
            $('#edit_is_active').prop('checked', false);
        }

        // 4. Open Modal
        $('#editModal').modal('show');
    });
</script>

@endsection