@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Assign Subjects.</p>
            </div>
            @else
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Assign Subject</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Assign Subject
                            </button>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table">
                                    <thead>
                                        <tr class="bg-primary text-white">
                                            <th class="text-white" style="width: 5%">#</th>
                                            <th class="text-white" style="width: 25%">Class Name</th>
                                            <th class="text-white">Assigned Subjects</th>
                                            <th class="text-white text-center" style="width: 15%">Status</th>
                                            <th class="text-white" style="width: 15%">Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Group the current page's results by Class Name --}}
                                        @forelse($assign_subjects->groupBy('class_id') as $classId => $group)
                                        @php
                                        $firstEntry = $group->first();
                                        @endphp
                                        <tr>
                                            {{-- 1. Row Number --}}
                                            <td>{{ $loop->iteration }}</td>

                                            {{-- 2. Class Name (Shows once per group) --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar avatar-sm bg-primary-transparent text-primary me-2">
                                                        <i class="fe fe-layers"></i>
                                                    </div>
                                                    <span class="fw-bold fs-14">{{ $firstEntry->class->name ?? 'N/A'
                                                        }}</span>
                                                </div>
                                            </td>

                                            {{-- 3. Subjects as Badges --}}
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($group as $item)
                                                    <span
                                                        class="badge bg-primary-transparent text-primary border border-primary-20 px-3 py-2">
                                                        <i class="fe fe-book-open me-1 small"></i> {{
                                                        $item->subject->name ?? 'N/A' }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                            </td>

                                            {{-- 4. Status --}}
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success-transparent text-success rounded-pill px-3">
                                                    Active
                                                </span>
                                            </td>

                                            {{-- 5. Creator --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted small">{{ $firstEntry->creator->name ??
                                                        'System' }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No assignments found for
                                                the current selection.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination Links --}}
                            <div class="mt-4 d-flex justify-content-end">
                                {{ $assign_subjects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ADD MODAL (Bulk Assign) --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">Assign Subject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('class_subjects.store') }}"
                    data-reload="1">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <select name="class_id" id="bulk_class_id" class="form-select select2"
                                data-rules="required">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Subject Names <span class="text-danger">*</span></label>
                            <div class="border p-3" style="max-height: 250px; overflow-y: auto;">
                                @foreach($subjects as $subject)
                                <div class="form-check mb-2">
                                    <input class="form-check-input subject-checkbox" type="checkbox"
                                        value="{{ $subject->id }}" name="subject_id[]" id="subject_{{ $subject->id }}">
                                    <label class="form-check-label" for="subject_{{ $subject->id }}">
                                        {{ $subject->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 mb-3" id="statusFieldBlock"> {{-- Added ID here --}}
                            <label class="form-label">Status</label>
                            <select name="status" id="assign_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">Save Assignment</button>
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

        // 1. When Edit is clicked (Hide Status)
        $(document).on('click', '.edit-assign-btn', function () {
            let classId = $(this).data('class-id');

            // UI Updates
            $('#modalTitle').text('Edit Assigned Subjects');
            $('#submitBtn').text('Update Assignment').removeClass('btn-success').addClass('btn-info');
            $('.modal-header').removeClass('bg-success').addClass('bg-info');

            // HIDE the status field for Edit
            $('#statusFieldBlock').hide();

            // Trigger class selection to load checkboxes
            $('#bulk_class_id').val(classId).trigger('change');

            $('#addModal').modal('show');
        });

        // 2. Automated Subject Checking (Ajax)
        $(document).on('change', '#bulk_class_id', function () {
            let classId = $(this).val();
            $('.subject-checkbox').prop('checked', false);

            if (!classId) return;

            $.ajax({
                url: `/get-assigned-subjects/${classId}`,
                method: "GET",
                success: function (res) {
                    if (res.status && res.assigned_ids) {
                        res.assigned_ids.forEach(id => {
                            $(`#subject_${id}`).prop('checked', true);
                        });
                    }
                }
            });
        });

        // 3. Reset Modal on Close (Show Status again)
        $('#addModal').on('hidden.bs.modal', function () {
            $('#CreateForm')[0].reset();
            $('#bulk_class_id').val('').trigger('change');

            // Revert UI to "Add" state
            $('#modalTitle').text('Assign Subject');
            $('#submitBtn').text('Save Assignment').removeClass('btn-info').addClass('btn-success');
            $('.modal-header').removeClass('bg-info').addClass('bg-success');

            // SHOW the status field again for new assignments
            $('#statusFieldBlock').show();
        });

    });
</script>
@endsection