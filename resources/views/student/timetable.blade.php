@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Class Timetable</h1>
                <div>
                    <span class="badge bg-primary px-3 py-2">Class: {{ $student->class->name }}</span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary-transparent">
                    <h3 class="card-title text-primary"><i class="fe fe-calendar me-2"></i>Weekly Learning Schedule</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-vcenter">
                            <thead class="bg-light">
                                <tr>
                                    <th style="min-width: 120px;">Day</th>
                                    @foreach($slots as $slot)
                                        <th>
                                            <div class="fw-bold">{{ $slot->name }}</div>
                                            <small class="text-muted">{{ date('h:i A', strtotime($slot->start_time)) }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                <tr>
                                    <td class="bg-light fw-bold text-primary">{{ $day->name }}</td>
                                    @foreach($slots as $slot)
                                        @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp
                                        
                                        <td class="{{ $slot->is_break ? 'bg-light-warning' : '' }}" style="height: 100px; min-width: 160px;">
                                            @if($slot->is_break)
                                                <div class="text-warning">
                                                    <i class="fe fe-coffee fs-20"></i><br>
                                                    <span class="fw-bold">RECESS</span>
                                                </div>
                                            @elseif($entry)
                                                <div class="p-2 border-top border-primary border-3 bg-primary-transparent rounded shadow-sm">
                                                    <div class="fw-bold text-dark fs-14">{{ $entry->subject->name }}</div>
                                                    <div class="text-muted small">Prof. {{ $entry->teacher->last_name }}</div>
                                                    <div class="mt-2">
                                                        <span class="badge bg-white text-primary border rounded-pill">Room: {{ $entry->room_number ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">- No Class -</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button onclick="window.print()" class="btn btn-info-light"><i class="fe fe-printer me-2"></i>Print Timetable</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection