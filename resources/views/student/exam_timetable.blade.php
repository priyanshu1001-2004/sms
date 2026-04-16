@extends('layouts.master')

@section('content')
<style>
    .exam-card {
        transition: all 0.3s ease;
        border: none !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        height: 100%;
    }

    .exam-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .date-badge {
        min-width: 65px;
        height: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .time-capsule {
        background: #f0f2f8;
        color: #3e5ad2;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.82rem;
    }

    .subject-title {
        font-size: 1.1rem;
        letter-spacing: -0.3px;
    }

    .bg-primary-gradient {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%) !important;
    }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">My Exam Schedule</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Portal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Timetable</li>
                    </ol>
                </div>
                <div class="d-none d-md-block">
                    <button class="btn btn-white btn-icon-text border shadow-sm" onclick="window.print()">
                        <i class="fe fe-printer me-2"></i> Print Timetable
                    </button>
                </div>
            </div>

            @if(isset($message) && $schedules->isEmpty())
            <div class="row justify-content-center">
                <div class="col-md-6 text-center py-8">
                    <div class="bg-light d-inline-block rounded-circle p-4 mb-4">
                        <i class="fe fe-calendar text-muted fs-50"></i>
                    </div>
                    <h3 class="fw-bold">No Exams Scheduled</h3>
                    <p class="text-muted mb-4">{{ $message }}</p>
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary px-5 btn-pill shadow">Return to Home</a>
                </div>
            </div>
            @else
            @foreach($schedules as $examName => $items)
            <div class="d-flex align-items-center mb-4 mt-2">
                <h4 class="mb-0 fw-bold text-dark pe-3">{{ $examName }}</h4>
                <div class="flex-grow-1 border-top opacity-50"></div>
                <span class="ms-3 badge bg-primary-transparent rounded-pill px-3">{{ $items->count() }} Papers</span>
            </div>

            <div class="row">
                @foreach($items as $schedule)
                @php
                // Logic placed inside the loop to avoid "Undefined variable" errors
                $start = \Carbon\Carbon::parse($schedule->start_time);
                $end = \Carbon\Carbon::parse($schedule->end_time);
                $totalMinutes = $start->diffInMinutes($end);
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                @endphp
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card exam-card">
                        <div class="card-body px-4 pb-0">
                            <div class="d-flex align-items-start">
                                <div class="date-badge bg-primary text-white rounded shadow-sm me-3">
                                    <span class="fs-20 fw-bold">{{ $start->format('d') }}</span>
                                    <small class="text-uppercase fw-semibold" style="font-size: 10px;">{{
                                        $start->format('M') }}</small>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-bold">{{ $start->format('l') }}</span>
                                        <span class="time-capsule shadow-none">
                                            <i class="fe fe-clock me-1 text-primary"></i>
                                            {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}
                                        </span>
                                    </div>

                                    <h5 class="subject-title fw-bold mt-2 text-dark mb-3">{{ $schedule->subject->name }}
                                    </h5>

                                    <div
                                        class="d-flex align-items-center gap-4 text-muted small mt-4">
                                        <div>
                                            <i class="fe fe-map-pin text-danger me-1"></i>
                                            {{ $schedule->room_number ?? 'Room N/A' }}
                                        </div>
                                        <div>
                                            <i class="fe fe-award text-success me-1"></i>
                                            Marks: <span class="fw-bold text-dark">{{ (int)$schedule->full_marks
                                                }}</span>
                                        </div>
                                        <div >

                                            <i class="fe fe-watch me-1 text-primary"></i> Duration
                                            <span class="fw-bold text-danger">

                                                {{ $hours > 0 ? $hours . 'h' : '' }} {{ $minutes > 0 ? $minutes . 'm' :
                                                '' }}
                                            </span>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="progress h-1 mb-0 rounded-0">
                            <div class="progress-bar bg-primary-gradient w-100" role="progressbar"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
            @endif

        </div>
    </div>
</div>
@endsection