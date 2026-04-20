@extends('layouts.master')

@section('content')
<style>
    /* Modern variables for cleaner look */
    :root {
        --card-radius: 12px;
        --accent-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .main-content {
        background-color: #f4f7fb;
    }

    /* Modern Card Styling */
    .custom-card {
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        background: #fff;
    }

    /* Improved Slot Card UI */
    .teacher-slot-card {
        border: 1px solid #edf2f7;
        border-left: 4px solid #667eea;
        background: #fff;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .teacher-slot-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        background-color: #f8faff;
    }

    /* Table Improvements */
    .table-vcenter thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #4a5568;
        border-top: none;
        padding: 15px;
    }

    .day-cell {
        background-color: #fdfdfd !important;
        color: #2d3748;
        font-weight: 700;
        vertical-align: middle !important;
        border-right: 2px solid #edf2f7 !important;
    }

    /* Modern Badges */
    .badge-soft-primary {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;
        font-weight: 600;
        border-radius: 6px;
        padding: 5px 10px;
    }

    .break-box {
        background-color: #fffaf0;
        border: 1px dashed #fbd38d;
        border-radius: 8px;
        color: #c05621;
        padding: 15px;
    }

    .empty-slot {
        font-size: 0.8rem;
        color: #a0aec0;
        font-style: italic;
    }

    @media print {
        .app-sidebar, .main-header, .page-header, .btn-print, .card-header { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; background: #fff; }
        .custom-card { box-shadow: none; border: 1px solid #eee; }
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            
            <div class="page-header d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="page-title mb-1">Teaching Schedule</h1>
                    <p class="text-muted small">View and manage your weekly academic commitments.</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary btn-print shadow-sm px-4">
                    <i class="fe fe-printer me-2"></i>Export Schedule
                </button>
            </div>

            <div class="card custom-card">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md brround bg-primary-transparent text-primary me-3">
                            {{ substr($teacher->first_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="card-title fw-bold mb-0">Weekly Planner</h3>
                            <small class="text-muted">Instructor: {{ $teacher->first_name }} {{ $teacher->last_name }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter text-center mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Day</th>
                                    @foreach($slots as $slot)
                                        <th>
                                            <span class="d-block fw-bold">{{ $slot->name }}</span>
                                            <span class="text-muted fw-normal small">
                                                <i class="fe fe-clock me-1"></i>{{ date('h:i A', strtotime($slot->start_time)) }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                <tr>
                                    <td class="day-cell text-uppercase small">{{ $day->name }}</td>
                                    @foreach($slots as $slot)
                                        @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp
                                        
                                        <td style="min-width: 220px; padding: 12px; height: 140px;">
                                            @if($slot->is_break)
                                                <div class="break-box h-100 d-flex flex-column justify-content-center align-items-center">
                                                    <i class="fe fe-coffee fs-20 mb-1"></i>
                                                    <span class="fw-bold fs-11 text-uppercase">Interval</span>
                                                </div>
                                            @elseif($entry)
                                                <div class="teacher-slot-card text-start">
                                                    <div>
                                                        <div class="fw-extrabold text-dark fs-14 mb-1">{{ $entry->subject->name }}</div>
                                                        <div class="mb-2">
                                                            <span class="badge-soft-primary small">
                                                                <i class="fe fe-book-open me-1"></i>Class: {{ $entry->class->name }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                                        <span class="small text-muted">
                                                            <i class="fe fe-map-pin me-1"></i>{{ $entry->room_number ?? 'Lab TBD' }}
                                                        </span>
                                                        <i class="fe fe-arrow-right text-light fs-12"></i>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="h-100 d-flex align-items-center justify-content-center">
                                                    <span class="empty-slot">Available</span>
                                                </div>
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

            <div class="mt-4 alert alert-light border-0 shadow-sm br-10 d-flex align-items-center">
                <i class="fe fe-info text-primary fs-20 me-3"></i>
                <span class="text-muted small">
                    <strong>Note:</strong> Timings are subject to change by the academic department. Please check daily for temporary substitution updates.
                </span>
            </div>
        </div>
    </div>
</div>
@endsection