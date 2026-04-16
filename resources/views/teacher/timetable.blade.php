@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Teaching Schedule</h1>
                <div>
                    <button onclick="window.print()" class="btn btn-primary-light shadow-sm">
                        <i class="fe fe-printer me-2"></i>Print My Week
                    </button>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">Weekly Planner: {{ auth()->user()->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center table-vcenter">
                            <thead class="bg-primary-transparent">
                                <tr>
                                    <th style="width: 120px;">Day</th>
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
                                    <td class="bg-light fw-bold text-uppercase">{{ $day->name }}</td>
                                    @foreach($slots as $slot)
                                        @php $entry = $timetableData[$day->id][$slot->id] ?? null; @endphp
                                        
                                        <td class="{{ $slot->is_break ? 'bg-light-warning' : '' }}" style="height: 110px; min-width: 180px;">
                                            @if($slot->is_break)
                                                <div class="text-warning small fw-bold">
                                                    <i class="fe fe-coffee fs-16"></i><br>BREAK
                                                </div>
                                            @elseif($entry)
                                                <div class="p-2 border-start border-primary border-3 bg-primary-transparent rounded">
                                                    <div class="fw-bold text-primary">{{ $entry->subject->name }}</div>
                                                    <div class="badge bg-primary text-white my-1">Class: {{ $entry->class->name }}</div>
                                                    <div class="small text-muted"><i class="fe fe-map-pin me-1"></i>{{ $entry->room_number ?? 'N/A' }}</div>
                                                </div>
                                            @else
                                                <span class="text-muted fs-11">Free Period</span>
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
</div>
@endsection