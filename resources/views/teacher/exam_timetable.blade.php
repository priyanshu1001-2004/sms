@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            
            <div class="page-header">
                <h1 class="page-title">Examination Timetable</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Exam Schedule</li>
                    </ol>
                </div>
            </div>

            @forelse($schedules as $examName => $items)
                <div class="card custom-card overflow-hidden shadow-sm mb-5">
                    <div class="card-header bg-primary-gradient d-flex justify-content-between align-items-center">
                        <h3 class="card-title text-white fw-bold">
                            <i class="fe fe-layers me-2"></i> {{ $examName }}
                        </h3>
                        <span class="badge bg-white-transparent text-white px-3 py-2 rounded-pill">
                            {{ $items->count() }} Subjects Scheduled
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter text-nowrap mb-0 border-0">
                                <thead class="bg-light text-uppercase">
                                    <tr>
                                        <th class="ps-4" style="width: 200px;">Date & Day</th>
                                        <th>Subject & Class</th>
                                        <th class="text-center">Timing</th>
                                        <th class="text-center">Venue</th>
                                        <th class="text-center">Marking Rule</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $schedule)
                                        <tr class="transition-all">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary-transparent text-primary text-center rounded p-2 me-3" style="min-width: 60px;">
                                                        <h4 class="mb-0 fw-bold">{{ \Carbon\Carbon::parse($schedule->exam_date)->format('d') }}</h4>
                                                        <small class="text-uppercase fw-semibold">{{ \Carbon\Carbon::parse($schedule->exam_date)->format('M') }}</small>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-bold text-dark">{{ \Carbon\Carbon::parse($schedule->exam_date)->format('Y') }}</p>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($schedule->exam_date)->format('l') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <h6 class="mb-1 fw-bold text-primary">{{ $schedule->subject->name }}</h6>
                                                <span class="badge bg-info-transparent rounded-pill px-2">
                                                    <i class="fe fe-tag me-1"></i> Class: {{ $schedule->class->name }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                                    <span class="btn btn-light pe-none fw-semibold">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                                    </span>
                                                    <span class="btn btn-primary pe-none px-2 border-0">to</span>
                                                    <span class="btn btn-light pe-none fw-semibold">
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-block">
                                                    <i class="fe fe-map-pin text-danger me-1"></i>
                                                    <span class="fw-semibold text-dark">{{ $schedule->room_number ?? 'Block A-101' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="px-2 border-end">
                                                        <small class="text-muted d-block">Full</small>
                                                        <span class="fw-bold text-success">{{ (int)$schedule->full_marks }}</span>
                                                    </div>
                                                    <div class="px-2">
                                                        <small class="text-muted d-block">Pass</small>
                                                        <span class="fw-bold text-danger">{{ (int)$schedule->pass_marks }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light-transparent py-3">
                        <div class="d-flex align-items-center text-muted small italic">
                            <i class="fe fe-info me-2 text-info"></i>
                            Students are advised to reach the examination hall 15 minutes before the start time.
                        </div>
                    </div>
                </div>
            @empty
                <div class="card custom-card">
                    <div class="card-body text-center p-5">
                        <div class="avatar avatar-xxl bg-light brround mb-4">
                            <i class="fe fe-calendar text-muted fs-40"></i>
                        </div>
                        <h4 class="fw-bold">No Exam Schedules Found</h4>
                        <p class="text-muted">There are no upcoming exams scheduled for your classes at the moment.</p>
                        <a href="{{ url()->previous() }}" class="btn btn-primary-light px-5 rounded-pill">Go Back</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .transition-all:hover {
        background-color: rgba(var(--primary-rgb), 0.03) !important;
        transform: scale(1.002);
    }
    .bg-primary-gradient {
        background: linear-gradient(to right, #4454c3 0%, #7c8bef 100%) !important;
    }
    .table-vcenter td {
        vertical-align: middle !important;
    }
</style>
@endsection