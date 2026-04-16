@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Examination Results</h1>
            </div>

            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.results') }}" class="row align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Select Examination</label>
                            <select name="exam_id" class="form-control form-select select2" required>
                                <option value="">-- Choose Exam --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ \Carbon\Carbon::parse($exam->start_date)->format('M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fe fe-eye me-2"></i>View Marksheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($selectedExam)
            <div class="card custom-card overflow-hidden">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title text-white">{{ $selectedExam->name }} - Official Marksheet</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-5 border-bottom pb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Student Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                            <p class="mb-1"><strong>Roll Number:</strong> {{ $student->roll_number }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1"><strong>Class:</strong> {{ $student->class->name }}</p>
                            <p class="mb-1"><strong>Date of Issue:</strong> {{ now()->format('d M, Y') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap border-bottom">
                            <thead class="bg-light">
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center">Attendance</th>
                                    <th class="text-center">Marks Obtained</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Grade Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                <tr>
                                    <td>{{ $result->subject->name }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $result->attendance == 'P' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $result->attendance == 'P' ? 'Present' : 'Absent' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-primary">{{ $result->marks_obtained }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $result->grade_name }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($result->grade_point, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-primary-transparent">
                                <tr>
                                    <th colspan="2" class="text-end">Calculation Summary:</th>
                                    <th class="text-center text-primary">{{ $results->sum('marks_obtained') }}</th>
                                    <th class="text-center">Avg: {{ $results->avg('grade_point') >= 1 ? 'PASS' : 'FAIL' }}</th>
                                    <th class="text-center">{{ number_format($results->avg('grade_point'), 2) }} (GPA)</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-outline-secondary me-2" onclick="window.print()">
                        <i class="fe fe-printer me-2"></i>Print Marksheet
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection