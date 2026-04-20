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
    }

    .edit-timetable-btn:hover {
        border: 1px dashed var(--primary-bg-color);
        background: rgba(var(--primary-rgb), 0.05);
    }

    .slot-cell {
        min-width: 180px;
        height: 120px;
        vertical-align: middle !important;
        position: relative;
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            {{-- HEADER: CLASS FILTER --}}
            <div class="card mt-4 custom-card">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title text-uppercase fw-bold">Manage Class Timetable</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-lg-4">
                            <form action="{{ route('class_timetables.index') }}" method="GET" id="classFilterForm">
                                <div class="form-group mb-0">
                                    <label class="form-label text-muted small fw-bold">FILTER BY CLASS</label>
                                    <select name="class_id" class="form-control select2 custom-select"
                                        onchange="$('#classFilterForm').submit()">
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $selectedClass==$class->id ? 'selected' : ''
                                            }}>
                                            {{ $class->name }} ({{ $class->timetableGroup->name ?? 'No Group' }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 col-lg-8 text-md-end mt-3 mt-md-0">
                            @if($selectedClass)
                            <span class="badge bg-primary-transparent text-primary p-2 px-3">
                                <i class="fe fe-layers me-1"></i> Group: {{ $activeGroup->name ?? 'N/A' }}
                            </span>
                            @endif
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
                                <table class="table table-bordered text-center table-vcenter border-top">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 120px;">Day / Time</th>
                                            @foreach($slots as $slot)
                                            <th class="{{ $slot->is_break ? 'bg-light-warning' : '' }}">
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
                                            <td class="bg-light fw-bold text-uppercase small">{{ $day->name }}</td>
                                            @foreach($slots as $slot)
                                            @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp

                                            <td class="slot-cell {{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                                @if($slot->is_break)
                                                <div class="text-warning small fw-bold"><i
                                                        class="fe fe-coffee d-block mb-1"></i> BREAK</div>
                                                @elseif($entry)
                                                {{-- ACTION: DELETE --}}
                                                <button type="button" class="btn btn-sm text-danger force-delete-btn"
                                                    data-url="{{ route('class_timetables.destroy', $entry->id) }}">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>

                                                {{-- CONTENT: EDITABLE BOX --}}
                                                <div class="p-2 rounded bg-primary-transparent edit-timetable-btn h-100 d-flex flex-column justify-content-center"
                                                    data-id="{{ $entry->id }}" data-subject="{{ $entry->subject_id }}"
                                                    data-teacher="{{ $entry->teacher_id }}"
                                                    data-room="{{ $entry->room_number }}" data-day="{{ $day->id }}"
                                                    data-slot="{{ $slot->id }}">
                                                    <div class="fw-bold text-primary text-truncate">{{
                                                        $entry->subject->name }}</div>
                                                    <div class="small text-dark text-truncate">{{
                                                        $entry->teacher->first_name }} {{ $entry->teacher->last_name }}
                                                    </div>
                                                    <div class="mt-1"><span class="badge bg-white text-muted border">Rm:
                                                            {{ $entry->room_number ?? '-' }}</span></div>
                                                </div>
                                                @else
                                                {{-- EMPTY: ASSIGN BUTTON --}}
                                                <button
                                                    class="btn btn-sm btn-outline-light text-primary open-assign-modal"
                                                    data-day="{{ $day->id }}" data-slot="{{ $slot->id }}">
                                                    <i class="fe fe-plus-circle"></i> Assign
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
                <h4>No Time Slots Found!</h4>
                <p>Please assign a Timetable Group to this class and define its slots.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- UNIFIED MODAL --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('class_timetables.store') }}" method="POST" class="ajax-form" id="timetableForm"
                data-reload="1">
                @csrf
                <div id="method_field"></div>
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
                        <label class="form-label fw-bold">Subject </label>
                        <select name="subject_id" id="subject_select" class="form-control select2-modal" required>
                            <option value="">-- Select Subject --</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Teacher </label>
                        <select name="teacher_id" id="teacher_select" class="form-control select2-modal" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($allTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label fw-bold">Room Name (Optional)</label>
                        <input type="text" name="room_number" id="modal_room" class="form-control"
                            placeholder="e.g. Room 101">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Load Subjects based on selected Class
    function loadSubjects(selectedId = null) {
        $.get(`/get-subjects-by-class/{{ $selectedClass }}`, function (res) {
            let html = '<option value="">-- Select Subject --</option>';
            res.data.forEach(item => {
                html += `<option value="${item.subject_id}" ${selectedId == item.subject_id ? 'selected' : ''}>${item.subject.name}</option>`;
            });
            $('#subject_select').html(html).trigger('change');
        });
    }
    // Function to handle Form Reset safely
    function resetTimetableForm() {
        $('#timetableForm')[0].reset();

        $('input[name="class_id"]').val('{{ $selectedClass }}');

        // Method field aur entry_id ko bhi saaf karein
        $('#method_field').html('');
        $('#modal_entry_id').val('');
    }

    // 1. ADD Button Logic
    $(document).on('click', '.open-assign-modal', function () {
        const d = $(this).data();

        resetTimetableForm(); // Safe Reset

        $('#timetableForm').attr('action', "{{ route('class_timetables.store') }}");
        $('#modal_day_id').val(d.day);
        $('#modal_slot_id').val(d.slot);
        $('#modal_title').text('Assign New Subject');

        loadSubjects();
        $('#assignModal').modal('show');
    });

    // 2. EDIT Button Logic
    $(document).on('click', '.edit-timetable-btn', function () {
        const d = $(this).data();

        resetTimetableForm(); // Pehle reset karein phir data bharein

        // URL aur Method update karein
        let updateUrl = "{{ url('class_timetables') }}/" + d.id;
        $('#timetableForm').attr('action', updateUrl);
        $('#method_field').html('@method("PUT")');

        // Data populate karein
        $('#modal_entry_id').val(d.id);
        $('#modal_day_id').val(d.day);
        $('#modal_slot_id').val(d.slot);
        $('#modal_room').val(d.room);
        $('#modal_title').text('Update Assignment');

        loadSubjects(d.subject);
        $('#teacher_select').val(d.teacher).trigger('change');

        $('#assignModal').modal('show');
    });

    // DELETE: Simple AJAX with container refresh
    $(document).on('click', '.force-delete-btn', function (e) {
        e.stopPropagation();
        if (confirm("Are you sure you want to remove this assignment?")) {
            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: $btn.data('url'),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    toastr.success(res.message);
                    $('#data-table-container').load(window.location.href + ' #data-table-container > *');
                },
                error: function () {
                    toastr.error('Delete failed!');
                    $btn.prop('disabled', false).html('<i class="fe fe-trash-2"></i>');
                }
            });
        }
    });
</script>
@endsection