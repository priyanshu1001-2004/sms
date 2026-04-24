@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
    /* Premium UX Styles */
    .timepicker-input {
        background-color: #fff !important;
        cursor: pointer;
        font-weight: 600;
        color: #334155;
    }

    .duration-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 50px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
    }

    .shift-label {
        font-size: 0.65rem;
        letter-spacing: 0.5px;
        font-weight: 800;
    }

    /* Visual lock for disabled inputs */
    .timepicker-input:disabled {
        background-color: #f1f5f9 !important;
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header">
                <h1 class="page-title">Time Slot Management</h1>
            </div>

            <div class="row">
                {{-- REGISTRATION FORM --}}
                <div class="col-xl-4">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="fe fe-plus-circle me-2 text-primary"></i>Register New Slot
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('time_slots.store') }}" method="POST" class="ajax-form"
                                id="timeSlotForm" data-reload="1" data-reset="1">
                                @csrf

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold text-dark">1. Select Shift Profile</label>
                                    <select name="timetable_group_id" id="group_selector" class="form-control select2"
                                        required>
                                        <option value="">-- Choose Profile --</option>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->id }}" data-type="{{ $group->type }}">
                                            {{ $group->name }} ({{ ucfirst($group->type) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold text-dark">2. Slot Designation</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. 1st Period"
                                        required>
                                </div>

                                <label class="form-label fw-bold text-dark">3. Set Time Duration</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted fw-bold">START TIME</label>
                                            <input type="text" id="start_time_picker" name="start_time"
                                                class="form-control timepicker-input" placeholder="--:-- --" readonly
                                                required disabled>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted fw-bold">END TIME</label>
                                            <input type="text" id="end_time_picker" name="end_time"
                                                class="form-control timepicker-input" placeholder="--:-- --" readonly
                                                required disabled>
                                        </div>
                                    </div>
                                </div>

                                <div id="live-calc" class="mt-3 text-center" style="display:none;">
                                    <span class="duration-badge"><i class="fe fe-clock me-1"></i> <span
                                            id="dur-val">0</span> Minutes Session</span>
                                </div>

                                <div class="form-group mt-4 mb-4">
                                    <label class="custom-control custom-checkbox-md">
                                        <input type="checkbox" class="custom-control-input" name="is_break" value="1">
                                        <span class="custom-control-label fw-semibold text-warning">This slot is a
                                            Break/Recess</span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-bold">
                                    <i class="fe fe-save me-2"></i> Save Configuration
                                </button>
                            </form>
                        </div>
                    </div>
                </div>



                {{-- DATA TABLE --}}
                <div class="col-xl-8">
                    <div class="card custom-card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Existing Schedule</h3>
                        </div>

                        <div class=" ">
                            <div class="card-body pb-0">
                                <form id="filterForm" action="{{ route('time_slots.index') }}" method="GET">
                                    <div class="row g-3">
                                        {{-- Search by Slot Name --}}
                                        <div class="col-md-3">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search Slot Name..." value="{{ request('search') }}">
                                        </div>

                                        {{-- Filter by Timetable Group (Shift Profile) --}}
                                        <div class="col-md-3">
                                            <select name="group_id" class="form-control select2">
                                                <option value="">All Profiles (Shifts)</option>
                                                @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ request('group_id')==$group->id ?
                                                    'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Filter by Category (Lecture/Break) --}}
                                        <div class="col-md-2">
                                            <select name="is_break" class="form-control select2">
                                                <option value="">All Categories</option>
                                                <option value="0" {{ request('is_break')==='0' ? 'selected' : '' }}>
                                                    Lecture Only</option>
                                                <option value="1" {{ request('is_break')==='1' ? 'selected' : '' }}>
                                                    Break Only</option>
                                            </select>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fe fe-search me-1"></i>Filter
                                            </button>
                                            <a href="{{ route('time_slots.index') }}" class="btn btn-light px-4 btn-reset">
                                                <i class="fe fe-refresh-cw me-1"></i>Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap border">
                                    <thead class="bg-light text-uppercase">
                                        <tr>
                                            <th>Profile</th>
                                            <th>Slot Name</th>
                                            <th>Type</th> {{-- Added Column --}}
                                            <th>Timeline</th>
                                            <th>Duration</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($slots as $slot)
                                        @php
                                        $start = \Carbon\Carbon::parse($slot->start_time);
                                        $end = \Carbon\Carbon::parse($slot->end_time);
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $slot->group->name }}</span><br>
                                                <span class="shift-label badge bg-primary-transparent text-primary">{{
                                                    strtoupper($slot->group->type) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $slot->name }}</span>
                                            </td>
                                            {{-- NEW TYPE COLUMN --}}
                                            <td>
                                                @if($slot->is_break)
                                                <span
                                                    class="badge bg-warning-transparent text-warning rounded-pill px-3">
                                                    <i class="fe fe-coffee me-1"></i> Interval
                                                </span>
                                                @else
                                                <span
                                                    class="badge bg-success-transparent text-success rounded-pill px-3">
                                                    <i class="fe fe-book-open me-1"></i> Lecture
                                                </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-dark fw-bold">{{ $start->format('h:i A') }} - {{
                                                    $end->format('h:i A') }}</span>
                                            </td>
                                            <td><span class="text-muted fw-bold">{{ $start->diffInMinutes($end) }}
                                                    Mins</span></td>
                                            <td class="text-end">
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-info-light edit-slot-btn"
                                                        data-id="{{ $slot->id }}" data-name="{{ $slot->name }}"
                                                        data-group="{{ $slot->timetable_group_id }}"
                                                        data-start="{{ $slot->start_time }}"
                                                        data-end="{{ $slot->end_time }}"
                                                        data-break="{{ $slot->is_break }}">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('time_slots.destroy', $slot->id) }}">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No slots configured for
                                                this organization.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class=" ">
                                {{   $slots->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- EDIT TIME SLOT MODAL --}}
<div class="modal fade" id="editSlotModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Modify Time Slot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form id="editSlotForm" method="POST" class="ajax-form" data-reload="1">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Shift Profile</label>
                        <select name="timetable_group_id" id="edit_group_id" class="form-control select2-modal"
                            required>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Slot Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="small fw-bold">START TIME</label>
                                <input type="text" name="start_time" id="edit_start_picker"
                                    class="form-control timepicker-input" readonly required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="small fw-bold">END TIME</label>
                                <input type="text" name="end_time" id="edit_end_picker"
                                    class="form-control timepicker-input" readonly required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="custom-control custom-checkbox-md">
                            <input type="checkbox" class="custom-control-input" name="is_break" id="edit_is_break"
                                value="1">
                            <span class="custom-control-label fw-semibold text-warning">Mark as Break</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-5">Update Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function () {
        // 1. Initialize Pickers
        const pickerConfig = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K",
            allowInput: false, // COMPLETE LOCK ON TYPING
            time_24hr: false,
            minuteIncrement: 5,
            onClose: function () { updateLiveDuration(); }
        };

        const startPicker = flatpickr("#start_time_picker", pickerConfig);
        const endPicker = flatpickr("#end_time_picker", pickerConfig);

        // 2. Real-time Duration Calculator
        function updateLiveDuration() {
            const s = startPicker.selectedDates[0];
            const e = endPicker.selectedDates[0];
            if (s && e) {
                let diff = (e - s) / 60000;
                if (diff < 0) diff += 1440;
                $('#dur-val').text(diff);
                $('#live-calc').show();
            }
        }

        // 3. The Logic Engine: Group Selector
        $('#group_selector').on('change', function () {
            const type = $(this).find(':selected').data('type');

            // State Management
            if (type) {
                $('.timepicker-input').prop('disabled', false);
            } else {
                $('.timepicker-input').prop('disabled', true);
            }

            // Forced Reset
            startPicker.clear();
            endPicker.clear();
            $('#live-calc').hide();

            // FORCE AM/PM POSITIONS
            if (type === 'morning') {
                // Morning Logic: Starts at 8:00 AM
                startPicker.set('minTime', '06:00');
                startPicker.set('maxTime', '13:00');
                startPicker.setDate("08:00", false); // Forces the clock into AM position

                endPicker.set('minTime', '06:05');
                endPicker.set('maxTime', '14:00');
                endPicker.setDate("08:45", false);

            } else if (type === 'afternoon') {
                // Afternoon Logic: Starts at 1:00 PM
                startPicker.set('minTime', '12:00');
                startPicker.set('maxTime', '19:00');
                startPicker.setDate("13:00", false); // Forces the clock into PM position (13:00)

                endPicker.set('minTime', '12:05');
                endPicker.set('maxTime', '20:00');
                endPicker.setDate("13:45", false);

            } else if (type === 'evening') {
                // Evening Logic: Starts at 5:00 PM
                startPicker.set('minTime', '16:00');
                startPicker.set('maxTime', '22:00');
                startPicker.setDate("17:00", false);
            }
        });

        // 4. Form Lifecycle
        $(document).on('ajaxFormSuccess', function () {
            startPicker.clear();
            endPicker.clear();
            $('#live-calc').hide();
            $('.timepicker-input').prop('disabled', true);
        });

        // Initialize Edit Pickers
        const editStartPicker = flatpickr("#edit_start_picker", pickerConfig);
        const editEndPicker = flatpickr("#edit_end_picker", pickerConfig);

        $(document).on('click', '.edit-slot-btn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const group = $(this).data('group');
            const start = $(this).data('start');
            const end = $(this).data('end');
            const isBreak = $(this).data('break');

            // Set Form Action
            let url = "{{ route('time_slots.update', ':id') }}";
            $('#editSlotForm').attr('action', url.replace(':id', id));

            // Fill Basic Inputs
            $('#edit_name').val(name);
            $('#edit_group_id').val(group).trigger('change');
            $('#edit_is_break').prop('checked', isBreak == 1);

            // Fill Pickers (Force 12h display)
            editStartPicker.setDate(start);
            editEndPicker.setDate(end);

            $('#editSlotModal').modal('show');
        });
    });
</script>
@endsection