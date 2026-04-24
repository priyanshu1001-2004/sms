@extends('layouts.master')

@section('content')
<style>
    .bg-light-warning {
        background-color: #fffcf0 !important;
    }

    .force-delete-btn {
        opacity: 0.6;
        transition: 0.3s;
        z-index: 20;
        position: absolute;
        top: 5px;
        right: 5px;
        border: none;
        background: transparent;
    }

    .force-delete-btn:hover {
        opacity: 1;
        color: #ff0000 !important;
        transform: scale(1.1);
    }

    .edit-timetable-btn {
        border: 1px dashed transparent;
        transition: 0.2s;
        cursor: pointer;
        min-height: 80px;
    }

    .edit-timetable-btn:hover {
        border: 1px dashed #4f46e5;
        background: rgba(79, 70, 229, 0.05);
    }

    .slot-cell {
        min-width: 180px;
        height: 110px;
        vertical-align: middle !important;
        position: relative;
        padding: 10px !important;
    }

    .select2-container {
        width: 100% !important;
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            {{-- HEADER: CLASS FILTER --}}
            <div class="card mt-4 custom-card">
                <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-uppercase fw-bold">Manage Class Timetable</h3>
                    @if($selectedClass)
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary-transparent text-primary p-2 px-3">
                            <i class="fe fe-layers me-1"></i> Class: {{ $classes->find($selectedClass)->name ?? '' }}
                        </span>
                    </div>
                    @endif
                </div>
                <div class="card-body pt-0">
                    <div class="row align-items-center">
                        <div class="w-50">
                            <form action="{{ route('class_timetables.index') }}" method="GET" id="classFilterForm">
                                <div class="form-group mb-0 w-50">
                                    <label class="form-label text-muted small fw-bold">FILTER BY CLASS</label>
                                    <select name="class_id" class="form-control w-50"
                                        onchange="$('#classFilterForm').submit()">
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                        <option class="w-50" value="{{ $class->id }}" {{ $selectedClass==$class->id ?
                                            'selected' : ''
                                            }}>
                                            {{ $class->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($selectedClass && isset($slots) && $slots->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-bordered text-center table-vcenter border-top saas-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 120px; background: #f8fafc;">Day / Time</th>
                                            @foreach($slots as $slot)
                                            <th class="{{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                                <div class="fw-bold">{{ $slot->name }}</div>
                                                <small class="text-muted">{{ date('h:i A', strtotime($slot->start_time))
                                                    }} - {{ date('h:i A', strtotime($slot->end_time)) }}</small>
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($days as $day)
                                        <tr>
                                            <td class="bg-light fw-bold text-uppercase small"
                                                style="background: #f8fafc !important;">{{ $day->name }}</td>
                                            @foreach($slots as $slot)

                                            @php 
                                            $entry = $timetableData[$day->id][$slot->id] ?? null; 
                                           
                                            @endphp

                                            <td class="slot-cell {{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                                @if($slot->is_break)
                                                <div class="text-warning small fw-bold"><i
                                                        class="fe fe-coffee d-block mb-1 fs-18"></i> BREAK</div>
                                                @elseif($entry)
                                                {{-- ACTION: DELETE --}}
                                                <button type="button" class="force-delete-btn"
                                                    data-url="{{ route('class_timetables.destroy', $entry->id) }}">
                                                    <i class="fe fe-x-circle"></i>
                                                </button>

                                                {{-- CONTENT: EDITABLE BOX --}}
                                                <div class="p-2 bg-primary-transparent edit-timetable-btn h-100 d-flex flex-column justify-content-center"
                                                    data-id="{{ $entry->id }}" data-subject="{{ $entry->subject_id }}"
                                                    data-teacher="{{ $entry->teacher_id }}"
                                                    data-room="{{ $entry->room_number }}" data-day="{{ $day->id }}"
                                                    data-slot="{{ $slot->id }}">
                                                    <div class="fw-bold text-primary text-truncate">{{
                                                        $entry->subject->name }}</div>
                                                    <div class="small text-dark text-truncate">
                                                        {{ $entry->teacher->first_name ?? 'Teacher Not Found' }}
                                                        {{ $entry->teacher->last_name ?? '' }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-white text-muted border py-1">Rm: {{
                                                            $entry->room_number ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                @else
                                                {{-- EMPTY: ASSIGN BUTTON --}}
                                                <button
                                                    class="btn btn-sm btn-outline-light text-primary open-assign-modal w-100 h-100 border-dotted"
                                                    data-day="{{ $day->id }}" data-slot="{{ $slot->id }}">
                                                    <i class="fe fe-plus"></i><br>Assign
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
            @elseif($selectedClass)
            <div class="alert alert-warning text-center py-5">
                <i class="fe fe-alert-triangle fs-40 d-block mb-2 text-warning"></i>
                <h4>Setup Incomplete!</h4>
                <p>Ensure this class has a Timetable Group and Time Slots defined.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- UNIFIED MODAL --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('class_timetables.store') }}" method="POST" class="ajax-form" id="timetableForm"
                data-reload="1">
                @csrf
                <div id="method_field"></div>
                <input type="hidden" name="class_id" value="{{ $selectedClass }}">
                <input type="hidden" name="week_day_id" id="modal_day_id">
                <input type="hidden" name="time_slot_id" id="modal_slot_id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modal_title">Schedule Class</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold small text-muted">SUBJECT</label>
                        <select name="subject_id" id="subject_select" class="form-control select2-modal" required>
                            <option value="">-- Select Subject --</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold small text-muted">ASSIGNED TEACHER</label>
                        <select name="teacher_id" id="teacher_select" class="form-control select2-modal" required
                            disabled>
                            <option value="">-- First Select Subject --</option>
                        </select>
                        <div id="teacher_loading" class="small text-info mt-1" style="display:none;">
                            <span class="spinner-border spinner-border-sm"></span> Finding qualified teachers...
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label fw-bold small text-muted">ROOM / LAB NAME</label>
                        <input type="text" name="room_number" id="modal_room" class="form-control"
                            placeholder="e.g. Science Lab 1">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" id="submitBtn">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {

        // Initialize Select2 for Modal to work correctly inside Bootstrap Modals
        $('.select2-modal').select2({
            dropdownParent: $('#assignModal')
        });

        /**
         * 1. Load Subjects assigned to the specific class
         */
        function loadSubjects(selectedSubjectId = null) {
            $.get(`/get-subjects-by-class/{{ $selectedClass }}`, function (res) {
                let html = '<option value="">-- Select Subject --</option>';
                if (res.data) {
                    res.data.forEach(item => {
                        let isSelected = (selectedSubjectId == item.subject_id) ? 'selected' : '';
                        html += `<option value="${item.subject_id}" ${isSelected}>${item.subject.name}</option>`;
                    });
                }
                $('#subject_select').html(html).trigger('change');
            });
        }

        /**
         * 2. SMART FILTER: Load ONLY teachers mapped to the selected Subject + Class
         */
        $(document).on('change', '#subject_select', function () {
            let subjectId = $(this).val();
            let teacherSelect = $('#teacher_select');

            if (!subjectId) {
                teacherSelect.html('<option value="">-- First Select Subject --</option>').prop('disabled', true);
                return;
            }

            $('#teacher_loading').show();
            teacherSelect.prop('disabled', true);

            $.ajax({
                url: "{{ route('get-qualified-teachers') }}", // Points to the smart filter logic
                method: "GET",
                data: {
                    class_id: "{{ $selectedClass }}",
                    subject_id: subjectId
                },
                success: function (res) {
                    $('#teacher_loading').hide();
                    let html = '<option value="">-- Select Qualified Teacher --</option>';

                    if (res.teachers && res.teachers.length > 0) {
                        res.teachers.forEach(t => {
                            html += `<option value="${t.id}">${t.name}</option>`;
                        });
                        teacherSelect.prop('disabled', false);
                    } else {
                        html = '<option value="">No teacher mapped to this subject!</option>';
                        toastr.warning("Please map a teacher to this subject first in 'Teacher Subject Mapping'.");
                    }

                    teacherSelect.html(html).trigger('change');

                    // Logic for EDIT mode: Auto-select the teacher after the list loads
                    let prevTeacher = teacherSelect.data('prev-val');
                    if (prevTeacher) {
                        teacherSelect.val(prevTeacher).trigger('change');
                        teacherSelect.data('prev-val', null); // Clear it
                    }
                },
                error: function () {
                    $('#teacher_loading').hide();
                    toastr.error("Failed to load qualified teachers.");
                }
            });
        });

        /**
         * 3. ADD PERIOD: Reset form and set Day/Slot IDs
         */
        $(document).on('click', '.open-assign-modal', function () {
            const d = $(this).data();

            // UI Reset
            $('#timetableForm')[0].reset();
            $('#method_field').empty();
            $('#timetableForm').attr('action', "{{ route('class_timetables.store') }}");

            // Set hidden IDs
            $('#modal_day_id').val(d.day);
            $('#modal_slot_id').val(d.slot);
            $('#modal_title').text('Schedule New Period');

            // Reset dropdowns
            $('#teacher_select').val('').trigger('change').prop('disabled', true);

            loadSubjects();
            $('#assignModal').modal('show');
        });

        /**
         * 4. EDIT PERIOD: Populate existing data
         */
        $(document).on('click', '.edit-timetable-btn', function () {
            const d = $(this).data();

            // Set PUT method for Update
            $('#method_field').html('<input type="hidden" name="_method" value="PUT">');
            $('#timetableForm').attr('action', `/class_timetables/${d.id}`);

            // Set IDs and Room
            $('#modal_day_id').val(d.day);
            $('#modal_slot_id').val(d.slot);
            $('#modal_room').val(d.room);
            $('#modal_title').text('Update Period Schedule');

            // Store existing teacher ID in data attribute for the 'change' listener to pick up
            $('#teacher_select').data('prev-val', d.teacher);

            loadSubjects(d.subject); // This triggers the teacher load automatically
            $('#assignModal').modal('show');
        });

        /**
         * 5. DELETE PERIOD: AJAX with confirmation
         */
        $(document).on('click', '.force-delete-btn', function (e) {
            e.stopPropagation(); // Prevents triggering the edit modal
            let url = $(this).data('url');

            if (confirm("Are you sure you want to remove this period from the schedule?")) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        toastr.success(res.message);
                        location.reload();
                    },
                    error: function () {
                        toastr.error("Could not remove the period.");
                    }
                });
            }
        });
    });
</script>
@endsection