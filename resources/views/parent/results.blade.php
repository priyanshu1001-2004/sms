@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            
            <div class="page-header">
                <h1 class="page-title">Children Examination Results</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Parent</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Results</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <label class="form-label fw-bold mb-3">Select a Student:</label>
                </div>
                @foreach($children as $child)
                <div class="col-md-4 col-xl-3">
                    <a href="{{ route('parent.results', ['student_id' => $child->id]) }}" 
                       class="card custom-card transition-all {{ request('student_id') == $child->id ? 'border-primary shadow-sm' : '' }}"
                       style="transition: transform 0.2s;">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xxl brround mb-3 {{ request('student_id') == $child->id ? 'bg-primary-gradient' : 'bg-light text-muted' }}">
                                <span class="fw-bold">{{ strtoupper(substr($child->first_name, 0, 1)) }}</span>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $child->first_name }} {{ $child->last_name }}</h5>
                            <span class="badge bg-primary-transparent text-danger rounded-pill px-3">
                                {{ $child->class->name }}
                            </span>
                            <div class="mt-2 text-muted small">
                                Roll Number: <span class="fw-bold">{{ $child->roll_number }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            @if($selectedStudent)
            <div class="card custom-card border-top border-primary">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('parent.results') }}" class="row align-items-end g-3">
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Select Examination for {{ $selectedStudent->first_name }}</label>
                            <div class="input-group">
                                <select name="exam_id" class="form-control form-select select2" required>
                                    <option value="">-- Click to Choose Exam --</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary  w-100 shadow-sm">
                                <i class="fe fe-search me-2"></i> Marksheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if(count($results) > 0)
            <div class="card custom-card overflow-hidden">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white">
                        <i class="fe fe-award me-2"></i> {{ $results->first()->exam->name }} - Marksheet
                    </h3>
                    <button class="btn btn-sm btn-white text-primary" onclick="window.print()">
                        <i class="fe fe-printer me-2"></i>Print
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border-0 text-nowrap mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Subject Name</th>
                                    <th class="text-center">Attendance</th>
                                    <th class="text-center">Marks Obtained</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Grade Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $res)
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">{{ $res->subject->name }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $res->attendance == 'P' ? 'bg-success-transparent text-success' : 'bg-danger-transparent text-danger' }} rounded-pill px-3">
                                            {{ $res->attendance == 'P' ? 'Present' : 'Absent' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <h6 class="mb-0 fw-bold">{{ $res->marks_obtained }}</h6>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-gradient px-3 fw-bold">{{ $res->grade_name }}</span>
                                    </td>
                                    <td class="text-center fw-semibold text-muted">
                                        {{ number_format($res->grade_point, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-primary-transparent border-top-2">
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end ps-4">Result Summary:</td>
                                    <td class="text-center text-primary fs-16">{{ $results->sum('marks_obtained') }}</td>
                                    <td class="text-center">GPA:</td>
                                    <td class="text-center fs-16 text-primary">{{ number_format($results->avg('grade_point'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection