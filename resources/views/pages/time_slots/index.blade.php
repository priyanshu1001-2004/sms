@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            {{-- PAGE HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Time Slot Management</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Timetable</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Time Slots</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- LEFT SIDE: ADD FORM --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card custom-card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="fe fe-plus-circle me-2 text-primary"></i>Create New Slot</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Assign slots to specific groups like Junior or Senior wings.</p>

                            <form action="{{ route('time_slots.store') }}" method="POST" class="ajax-form" data-reload="1" data-reset="1">
                                @csrf
                                
                                {{-- Timetable Group Selection --}}
                                <div class="form-group mb-3">
                                    <label class="form-label fw-semibold">Timetable Group <span class="text-danger">*</span></label>
                                    <select name="timetable_group_id" class="form-control select2" data-rules="required">
                                        <option value="">-- Select Group --</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-semibold">Slot Title <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fe fe-tag"></i></span>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. Period 1, Recess" data-rules="required">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-semibold">Start Time</label>
                                            <input type="time" name="start_time" class="form-control" data-rules="required">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-semibold">End Time</label>
                                            <input type="time" name="end_time" class="form-control" data-rules="required">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <div class="custom-controls-stacked">
                                        <label class="custom-control custom-checkbox-md">
                                            <input type="checkbox" class="custom-control-input" name="is_break" value="1">
                                            <span class="custom-control-label fw-semibold text-warning">Mark as Break/Recess</span>
                                        </label>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Breaks are locked during timetable entries.</small>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                    <i class="fe fe-save me-1"></i> Save Time Slot
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT SIDE: LIST TABLE --}}
                <div class="col-xl-8 col-lg-7">
                    <div class="card custom-card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fe fe-list me-2 text-success"></i>Current School Schedule</h3>
                            <span class="badge bg-primary-transparent text-primary rounded-pill">
                                {{ count($slots) }} Slots Defined
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-hover border text-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Group</th>
                                            <th>Slot Name</th>
                                            <th>Time Range</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($slots as $slot)
                                        @php
                                            $start = \Carbon\Carbon::parse($slot->start_time);
                                            $end = \Carbon\Carbon::parse($slot->end_time);
                                            $diff = $start->diffInMinutes($end);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="text-muted small fw-bold uppercase">
                                                    {{ $slot->group->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td><span class="fw-bold text-dark">{{ $slot->name }}</span></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark">{{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}</span>
                                                    <small class="text-muted font-italic">{{ $diff }} mins</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($slot->is_break)
                                                <span class="badge bg-warning-transparent text-warning px-3 py-2">
                                                    <i class="fe fe-coffee me-1"></i> Break
                                                </span>
                                                @else
                                                <span class="badge bg-success-transparent text-success px-3 py-2">
                                                    <i class="fe fe-book-open me-1"></i> Lecture
                                                </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-danger-light trigger-delete"
                                                        data-url="{{ route('time_slots.destroy', $slot->id) }}"
                                                        data-bs-toggle="tooltip" title="Delete Slot">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fe fe-info fs-40 d-block mb-2"></i>
                                                    <p>No time slots defined yet.</p>
                                                </div>
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
@endsection