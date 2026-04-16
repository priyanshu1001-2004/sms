@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Examination Management</h1>
                <div>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addExamModal">
                        <i class="fe fe-plus me-2"></i>Create New Exam
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header border-bottom d-flex justify-content-between">
                            <h3 class="card-title">Exam List</h3>
                        </div>
                        <div class="card-body">
                            {{-- Wrap this ID for your global ajax-form reload logic --}}
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-bordered text-nowrap border-bottom table-hover">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th class="text-start">Exam Name & Term</th>
                                            <th>Academic Session</th>
                                            <th>Date Duration</th>
                                            <th>Subjects Set</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exams as $exam)
                                        <tr>
                                            <td>
                                                <div class="fw-bold fs-14 text-dark">{{ $exam->name }}</div>
                                                <small class="text-muted">{{ $exam->term_name ?? 'Full Term' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-info-transparent text-info border border-info-subtle rounded-pill">
                                                    {{ $exam->academicYear->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-muted">
                                                    <i class="fe fe-calendar me-1"></i> {{ $exam->start_date }}
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="fe fe-arrow-right me-1"></i> {{ $exam->end_date }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary-transparent text-secondary">
                                                    {{ $exam->schedules_count }} Subjects
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($exam->is_published)
                                                <span class="badge bg-success-transparent text-success">Published</span>
                                                @else
                                                <span class="badge bg-warning-transparent text-warning">In
                                                    Progress</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-list justify-content-center d-flex">
                                                    <a href="{{ route('exams.schedule.manage', $exam->id) }}"
                                                        class="btn btn-sm btn-primary-light manage-schedule-btn"
                                                        data-id="{{ $exam->id }}" title="Manage Schedule">
                                                        <i class="fe fe-calendar"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-info-light edit-exam-btn"
                                                        data-id="{{ $exam->id }}" data-name="{{ $exam->name }}"
                                                        data-term="{{ $exam->term_name }}"
                                                        data-academic="{{ $exam->academic_year_id }}"
                                                        data-start="{{ $exam->start_date }}"
                                                        data-end="{{ $exam->end_date }}"
                                                        data-published="{{ $exam->is_published }}" title="Edit Exam">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger-light delete-btn"
                                                        data-url="{{ route('exams.destroy', $exam->id) }}">
                                                        <i class="fe fe-trash"></i>
                                                    </button>

                                                    <div class="me-3 mt-2" title="Publish Results/Timetable">
                                                        <div class="main-toggle-group-demo">
                                                            <div class="onoffswitch2">
                                                                <input type="checkbox" name="onoffswitch2"
                                                                    id="publishToggle{{ $exam->id }}"
                                                                    class="onoffswitch2-checkbox globalStatusToggle"
                                                                    data-url="{{ route('exams.toggle-publish', $exam->id) }}"
                                                                    {{ $exam->is_published ? 'checked' : '' }}>
                                                                <label for="publishToggle{{ $exam->id }}"
                                                                    class="onoffswitch2-label"></label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No examinations found.
                                            </td>
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

{{-- Modal: Add Exam --}}
<div class="modal fade" id="addExamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-plus-circle me-2"></i>Create New Examination</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('exams.store') }}" method="POST" class="ajax-form" data-reload="1">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" data-rules="required|min:3">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" class="form-control form-select" data-rules="required">
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>{{
                                        $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Term Name</label>
                                <input type="text" name="term_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    data-rules="required">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" data-rules="required">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5">Save Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editExamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fe fe-edit me-2"></i>Edit Examination</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            {{-- Action will be set dynamically via JS --}}
            <form action="" method="POST" class="ajax-form" id="editExamForm" data-reload="1">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control"
                                    data-rules="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="edit_academic_year_id"
                                    class="form-control form-select" data-rules="required">
                                    @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Term Name</label>
                                <input type="text" name="term_name" id="edit_term_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="edit_start_date" class="form-control"
                                    data-rules="required">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="edit_end_date" class="form-control"
                                    data-rules="required">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info px-5">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Manage Schedule --}}
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-calendar me-2"></i>Exam Schedule & Marking Scheme</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('exams.schedule.store') }}" method="POST" class="ajax-form" data-reload="0">
                @csrf
                <input type="hidden" name="exam_id" id="modal_exam_id">
                <div class="modal-body">
                    {{-- Professional SMS Addition: Global Class Selector to fix PostgreSQL Error --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Apply to Class <span class="text-danger">*</span></label>
                            <select name="global_class_id" id="global_class_id" class="form-control form-select"
                                required>
                                <option value="">-- Select Class --</option>
                                {{-- Assuming $classes is passed from controller --}}
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 25%;">Subject</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Full Marks</th>
                                    <th>Pass Marks</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-container"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.manage-schedule-btn', function (e) {
        e.preventDefault();

        let url = $(this).attr('href');
        let examId = $(this).data('id');

        $('#modal_exam_id').val(examId);

        // --- ADD THIS LINE HERE ---
        let container = $('#schedule-container');
        // --------------------------

        container.html('<tr><td colspan="6" class="text-center py-5"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

        var modalElement = document.getElementById('scheduleModal');
        var modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();

        $.get(url, function (res) {
            if (res.status) {
                let html = '';
                res.subjects.forEach((subject, index) => {
                    let existing = res.existingSchedules.find(s => s.subject_id == subject.id) || {};

                    html += `
                <tr>
                    <td class="align-middle">
                       <input type="hidden" name="schedules[${index}][subject_id]" value="${subject.id}">
                       <input type="hidden" name="schedules[${index}][class_id]" class="row-class-id" value="${existing.class_id || ''}">
                        <span class="fw-bold text-dark">${subject.name}</span>
                        <div class="small text-muted">${subject.code || ''}</div>
                    </td>
                    <td><input type="date" name="schedules[${index}][exam_date]" class="form-control" value="${existing.exam_date || ''}"></td>
                    <td><input type="time" name="schedules[${index}][start_time]" class="form-control" value="${existing.start_time || '09:00'}"></td>
                    <td><input type="time" name="schedules[${index}][end_time]" class="form-control" value="${existing.end_time || '12:00'}"></td>
                    <td><input type="number" name="schedules[${index}][full_marks]" class="form-control text-center" value="${existing.full_marks || '100'}"></td>
                    <td><input type="number" name="schedules[${index}][pass_marks]" class="form-control text-center" value="${existing.pass_marks || '33'}"></td>
                </tr>`;
                });
                // Now 'container' will be recognized here!
                container.html(html);
            }
        });
    });

    $(document).on('click', '.edit-exam-btn', function () {
        // 1. Get data from button attributes
        let id = $(this).data('id');
        let name = $(this).data('name');
        let term = $(this).data('term');
        let academic = $(this).data('academic');
        let start = $(this).data('start');
        let end = $(this).data('end');
        let published = $(this).data('published');

        // 2. Set Form Action
        $('#editExamForm').attr('action', `/exams/${id}`);

        // 3. Prefill Fields
        $('#edit_name').val(name);
        $('#edit_term_name').val(term);
        $('#edit_academic_year_id').val(academic);
        $('#edit_start_date').val(start);
        $('#edit_end_date').val(end);

        // Handle Switch (Published status)
        $('#edit_is_published').prop('checked', published == 1);

        // 4. Open Modal
        var modalElement = document.getElementById('editExamModal');
        var modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
    });

    // Automatically update the class_id hidden input in all rows when the top dropdown changes
    $(document).on('change', '#global_class_id', function () {
        $('.row-class-id').val($(this).val());
    });
</script>
@endsection