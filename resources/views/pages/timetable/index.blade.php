@extends('layouts.master')

@section('content')

<style>
    .trigger-delete {
        z-index: 9999 !important;
        pointer-events: all !important;
        background: rgba(255, 255, 255, 0.7);
        /* Adds slight background to see it clearly */
        border-radius: 4px;
    }

    .trigger-delete:hover {
        background: #fff;
        color: #ff0000 !important;
    }

    .edit-timetable-btn {
        transition: all 0.2s;
    }

    .edit-timetable-btn:hover {
        background: rgba(var(--primary-rgb), 0.2);
        border-color: var(--primary-bg-color);
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header">
                <h1 class="page-title">Manage Class Timetable</h1>
                <div class="ms-auto">
                    <form action="{{ route('class_timetables.index') }}" method="GET" id="classFilterForm">
                        <select name="class_id" class="form-control select2" onchange="$('#classFilterForm').submit()">
                            <option value="">-- Select Class to Manage --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass==$class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if($selectedClass)
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-header border-bottom d-flex justify-content-between">
                            <h3 class="card-title">Weekly Schedule Grid</h3>
                            <span class="text-muted small align-self-center">Click any slot to edit or add</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-bordered text-center table-vcenter border-top">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 150px;">Day / Time</th>
                                            @foreach($slots as $slot)
                                            <th>
                                                <div class="fw-bold">{{ $slot->name }}</div>
                                                <small class="text-muted">{{ date('h:i A', strtotime($slot->start_time))
                                                    }}</small>
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($days as $day)
                                        <tr>
                                            <td class="bg-light fw-bold text-uppercase">{{ $day->name }}</td>
                                            @foreach($slots as $slot)
                                            @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp

                                            <td class="{{ $slot->is_break ? 'bg-light-warning' : '' }}"
                                                style="height: 110px; min-width: 200px; vertical-align: middle; position: relative;">
                                                {{-- Added relative here --}}

                                                @if($slot->is_break)
                                                <span class="text-warning fw-bold"><i class="fe fe-coffee"></i>
                                                    BREAK</span>
                                                @elseif($entry)
                                                {{-- DELETE BUTTON: Moved to top layer --}}
                                                <button type="button" class="btn btn-sm text-danger force-delete-btn"
                                                    style="position: absolute; top: 5px; right: 5px; z-index: 9999; background: white; border: 1px solid #ddd;"
                                                    data-url="{{ route('class_timetables.destroy', $entry->id) }}">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>

                                                {{-- CLICKABLE EDIT BOX --}}
                                                <div class="p-2 border rounded bg-primary-transparent edit-timetable-btn"
                                                    style="cursor: pointer; width: 100%; height: 100%;"
                                                    data-id="{{ $entry->id }}" data-subject="{{ $entry->subject_id }}"
                                                    data-teacher="{{ $entry->teacher_id }}"
                                                    data-room="{{ $entry->room_number }}" data-day="{{ $day->id }}"
                                                    data-slot="{{ $slot->id }}">

                                                    <div class="fw-bold text-primary">{{ $entry->subject->name }}</div>
                                                    <div class="small text-dark">{{ $entry->teacher->first_name }} {{
                                                        $entry->teacher->last_name }}</div>
                                                    <div class="badge bg-white text-primary border mt-1 small">Room: {{
                                                        $entry->room_number ?? 'N/A' }}</div>
                                                </div>
                                                @else
                                                {{-- EMPTY SLOT --}}
                                                <button
                                                    class="btn btn-sm btn-outline-light text-muted open-assign-modal"
                                                    data-day="{{ $day->id }}" data-slot="{{ $slot->id }}">
                                                    <i class="fe fe-plus"></i> Assign
                                                </button>
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info text-center py-5">
                <i class="fe fe-info fs-30 d-block mb-2"></i>
                <p>Please select a class from the dropdown above to view and manage the timetable.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- UNIFIED ASSIGNMENT MODAL (ADD & EDIT) --}}
<div class="modal fade" id="assignModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('class_timetables.store') }}" method="POST" class="ajax-form" data-reload="1">
                @csrf
                <input type="hidden" name="entry_id" id="modal_entry_id">
                <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                <input type="hidden" name="week_day_id" id="modal_day_id">
                <input type="hidden" name="time_slot_id" id="modal_slot_id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modal_title">Assign Subject</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Subject</label>
                        <select name="subject_id" id="subject_select" class="form-control select2-modal" required>
                            <option value="">-- Select Subject --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Teacher</label>
                        <select name="teacher_id" id="teacher_select" class="form-control select2-modal" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($allTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-primary" id="teacher_suggest_msg" style="display:none;">
                            <i class="fe fe-info me-1"></i> Suggested default teacher for this subject.
                        </small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Room Number (Optional)</label>
                        <input type="text" name="room_number" id="modal_room" class="form-control"
                            placeholder="e.g. Room 101">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
    // INITIALIZE SELECT2 FOR MODAL
    function initModalSelect2() {
        $('.select2-modal').select2({
            dropdownParent: $('#assignModal'),
            width: '100%'
        });
    }

    // NEW ASSIGNMENT CLICK
    $(document).on('click', '.open-assign-modal', function () {
        $('#modal_entry_id').val('');
        $('#modal_title').text('Assign New Subject');
        $('#modal_day_id').val($(this).data('day'));
        $('#modal_slot_id').val($(this).data('slot'));
        $('#modal_room').val('');
        $('#teacher_suggest_msg').hide();

        loadSubjects();
        $('#assignModal').modal('show');
        initModalSelect2();
    });

    // EDIT EXISTING ASSIGNMENT
    $(document).on('click', '.edit-timetable-btn', function () {
        let data = $(this).data();
        $('#modal_entry_id').val(data.id);
        $('#modal_title').text('Update Assignment');
        $('#modal_day_id').val(data.day);
        $('#modal_slot_id').val(data.slot);
        $('#modal_room').val(data.room);

        loadSubjects(data.subject); // Load and select existing subject
        $('#teacher_select').val(data.teacher).trigger('change');

        $('#assignModal').modal('show');
        initModalSelect2();
    });

    function loadSubjects(selectedSubjectId = null) {
        let classId = '{{ $selectedClass }}';
        $.get(`/get-subjects-by-class/${classId}`, function (response) {
            let options = '<option value="">-- Select Subject --</option>';
            response.data.forEach(item => {
                let selected = (selectedSubjectId == item.subject_id) ? 'selected' : '';
                options += `<option value="${item.subject_id}" ${selected}>${item.subject.name}</option>`;
            });
            $('#subject_select').html(options).trigger('change');
        });
    }

    // AUTO-SUGGEST TEACHER
    $('#subject_select').on('change', function () {
        let subjectId = $(this).val();
        let classId = '{{ $selectedClass }}';
        let teacherSelect = $('#teacher_select');

        if (!subjectId || $('#modal_entry_id').val() != '') return; // Don't auto-suggest if editing

        $.ajax({
            url: `/get-teacher-by-subject-class/${subjectId}/${classId}`,
            type: 'GET',
            success: function (response) {
                if (response.data && response.data.teacher) {
                    teacherSelect.val(response.data.teacher.id).trigger('change');
                    $('#teacher_suggest_msg').fadeIn();
                } else {
                    $('#teacher_suggest_msg').hide();
                }
            }
        });
    });

    $(document).ready(function () {
        $(document).on('click', '.force-delete-btn', function (e) {
            // 1. Kill all other events immediately
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            console.log("Delete button clicked successfully!");

            let url = $(this).data('url');
            let $btn = $(this);

            if (confirm("Are you sure you want to remove this assignment?")) {
                // Optional: Show loading on the button
                $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.status) {
                            toastr.success(response.message);
                            // Refresh the container
                            $('#data-table-container').load(window.location.href + ' #data-table-container > *');
                        } else {
                            toastr.error('Could not delete.');
                            $btn.html('<i class="fe fe-trash-2"></i>').prop('disabled', false);
                        }
                    },
                    error: function () {
                        toastr.error('Connection error.');
                        $btn.html('<i class="fe fe-trash-2"></i>').prop('disabled', false);
                    }
                });
            }
        });
    });
</script>
@endsection