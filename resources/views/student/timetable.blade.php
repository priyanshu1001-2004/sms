@extends('layouts.master')

@section('content')
<style>
    @media print {
        .app-sidebar, .main-header, .card-footer, .breadcrumb { display: none !important; }
        .main-content { margin-top: 0 !important; padding: 0 !important; }
        .card { border: none !important; shadow: none !important; }
    }
    .slot-item { min-width: 150px; transition: transform 0.2s; }
    .slot-item:hover { transform: translateY(-2px); }
    .bg-light-warning { background-color: #fff8e1 !important; }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            
            <div class="page-header">
                <h1 class="page-title">My Class Timetable</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Schedule</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm custom-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white">
                        <i class="fe fe-calendar me-2"></i> {{ $student->class->name }} Weekly Schedule
                    </h3>
                    <span class="badge bg-white text-primary px-3 py-2 fs-12">Academic Year: 2026-27</span>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-vcenter">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="bg-gray-200" style="width: 100px;">Day</th>
                                    @foreach($slots as $slot)
                                        <th class="{{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                            <div class="fw-bold text-dark">{{ $slot->name }}</div>
                                            <div class="small text-muted">
                                                {{ date('h:i A', strtotime($slot->start_time)) }} - {{ date('h:i A', strtotime($slot->end_time)) }}
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                <tr>
                                    <td class="bg-light fw-bold text-primary text-uppercase fs-12">{{ $day->name }}</td>
                                    @foreach($slots as $slot)
                                        @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp
                                        
                                        <td class="slot-item {{ $slot->is_break ? 'bg-light-warning' : '' }}">
                                            @if($slot->is_break)
                                                <div class="py-3">
                                                    <i class="fe fe-coffee text-warning fs-24"></i>
                                                    <div class="fw-bold text-warning small mt-1">RECESS</div>
                                                </div>
                                            @elseif($entry)
                                                <div class="p-2 border-top border-primary border-3 bg-primary-transparent rounded shadow-none h-100">
                                                    <div class="fw-bold text-primary fs-14 mb-1">{{ $entry->subject->name }}</div>
                                                    <div class="small text-dark mb-2">
                                                        <i class="fe fe-user me-1"></i>{{ $entry->teacher->first_name }} {{ $entry->teacher->last_name }}
                                                    </div>
                                                    <span class="badge bg-white text-primary border rounded-pill">
                                                        <i class="fe fe-map-pin me-1"></i>{{ $entry->room_number ?? 'Room: TBD' }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="py-4 text-muted small italic">- No Session -</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <p class="mb-0 text-muted small"><i class="fe fe-info me-1"></i> If you see any discrepancies, please contact the Academic Coordinator.</p>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fe fe-printer me-2"></i>Download / Print PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection