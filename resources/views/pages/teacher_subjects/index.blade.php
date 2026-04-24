@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">
            @if(auth()->check() && auth()->user()->hasRole('super_admin') && !session('impersonator_id'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Teacher Assignments.</p>
            </div>
            @else
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Teacher Subject Mapping</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Assign Teacher
                            </button>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table">
                                    <thead>
                                        <tr class="bg-primary text-white">
                                            <th class="text-white" style="width: 5%">#</th>
                                            <th class="text-white">Teacher Details</th>
                                            <th class="text-white">Class</th>
                                            <th class="text-white">Subjects Qualified to Teach</th>
                                            <th class="text-white text-center" style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Grouping prevents duplicate teacher rows --}}
                                        @forelse($teacher_assignments->groupBy(['teacher_id', 'class_id']) as $teacherId
                                        => $assignedClasses)
                                        @foreach($assignedClasses as $classId => $group)
                                        @php $firstItem = $group->first(); @endphp
                                        <tr>
                                            <td>{{ $loop->parent->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar avatar-sm bg-info-transparent text-info rounded-circle me-2">
                                                        <i class="fe fe-user"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold fs-14">{{ $firstItem->teacher->name ??
                                                            'N/A' }}</span>
                                                        <div class="small text-muted">{{ $firstItem->teacher->username
                                                            ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-transparent text-secondary px-3 py-2">
                                                    <i class="fe fe-layers me-1"></i> {{ $firstItem->class->name ??
                                                    'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($group as $item)
                                                    <span
                                                        class="badge bg-primary-transparent text-primary border border-primary-20 px-2 py-2">
                                                        <i class="fe fe-book-open me-1 small"></i> {{
                                                        $item->subject->name ?? 'N/A' }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-info edit-assign-btn"
                                                        data-teacher-id="{{ $teacherId }}"
                                                        data-class-id="{{ $classId }}">
                                                        <i class="fe fe-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger trigger-delete mx-2"
                                                        data-url="{{ route('teacher-subjects.destroy', $firstItem->id) }}">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No assignments found.
                                                Please map teachers to subjects.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                {{ $teacher_assignments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ASSIGN TEACHER MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">Map Teacher to Subjects</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('teacher-subjects.store') }}"
                    data-reload="1">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="teacher_id" class="form-select select2" data-rules="required">
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="class_id" class="form-select select2" data-rules="required">
                                <option value="">Select Class</option>
                                {{-- Changed variable name to $classList to prevent loop conflict --}}
                                @foreach($classList as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Subjects <span class="text-danger">*</span></label>
                            <div id="subject-checkbox-list">
                                {{-- Initial list from Controller --}}
                                @foreach($subjectList as $s)
                                <div class="form-check mb-2">
                                    <input class="form-check-input subject-checkbox" type="checkbox"
                                        value="{{ $s->id }}" name="subject_id[]" id="sub_{{ $s->id }}">
                                    <label class="form-check-label" for="sub_{{ $s->id }}">{{ $s->name }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 mb-3" id="statusFieldBlock">
                            <label class="form-label">Assignment Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">Save Mapping</button>
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
        // AJAX: Sync subjects when Class/Teacher changes
        $(document).on('change', '#class_id, #teacher_id', function () {
            let classId = $('#class_id').val();
            let teacherId = $('#teacher_id').val();
            let container = $('#subject-checkbox-list');

            if (!classId) {
                container.html('<p class="text-muted small mb-0">Select a class first.</p>');
                return;
            }

            // Show a small loader inside the checkbox area
            container.html('<div class="text-center py-3"><span class="spinner-border spinner-border-sm text-primary"></span> Loading Class Subjects...</div>');

            $.ajax({
                url: "{{ route('get-class-subjects-for-teacher') }}",
                method: "GET",
                data: { class_id: classId, teacher_id: teacherId },
                success: function (res) {
                    container.empty();

                    if (res.subjects && res.subjects.length > 0) {
                        res.subjects.forEach(sub => {
                            // Check if teacher already teaches this
                            let isChecked = res.assigned_ids.includes(sub.id) ? 'checked' : '';

                            container.append(`
                        <div class="form-check mb-2">
                            <input class="form-check-input subject-checkbox" type="checkbox" 
                                value="${sub.id}" name="subject_id[]" id="sub_${sub.id}" ${isChecked}>
                            <label class="form-check-label" for="sub_${sub.id}">
                                ${sub.name}
                            </label>
                        </div>
                    `);
                        });
                    } else {
                        container.html('<p class="text-danger small mb-0"><i class="fe fe-alert-circle"></i> No subjects assigned to this class. Go to "Assign Subjects" first.</p>');
                    }
                },
                error: function () {
                    container.html('<p class="text-danger small mb-0">Error loading subjects.</p>');
                }
            });
        });

        // Edit Button Logic
        $(document).on('click', '.edit-assign-btn', function () {
            let tId = $(this).data('teacher-id');
            let cId = $(this).data('class-id');

            $('#modalTitle').text('Edit Teacher Assignment');
            $('#submitBtn').text('Update Assignment').removeClass('btn-success').addClass('btn-info');
            $('.modal-header').removeClass('bg-success').addClass('bg-info');
            $('#statusFieldBlock').hide();

            $('#teacher_id').val(tId).trigger('change');
            $('#class_id').val(cId).trigger('change');

            $('#addModal').modal('show');
        });

        // Reset on Modal Close
        $('#addModal').on('hidden.bs.modal', function () {
            $('#CreateForm')[0].reset();
            $('.select2').val('').trigger('change');
            $('#modalTitle').text('Map Teacher to Subjects');
            $('#submitBtn').text('Save Mapping').removeClass('btn-info').addClass('btn-success');
            $('.modal-header').removeClass('bg-info').addClass('bg-success');
            $('#statusFieldBlock').show();
        });
    });
</script>
@endsection