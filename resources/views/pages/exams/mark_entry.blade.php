@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Bulk Mark Entry</h1>
            </div>

            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('exam-results.index') }}" class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Select Exam</label>
                            <select name="exam_id" class="form-control form-select" required>
                                <option value="">-- Choose Exam --</option>
                                @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id')==$exam->id ? 'selected' : '' }}>{{
                                    $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Select Class</label>
                            <select name="class_id" class="form-control form-select" required>
                                <option value="">-- Choose Class --</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id')==$class->id ? 'selected' : ''
                                    }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Select Subject</label>
                            <select name="subject_id" class="form-control form-select" required>
                                <option value="">-- Choose Subject --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id')==$subject->id ? 'selected' :
                                    '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fe fe-filter me-2"></i>Filter Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(count($students) > 0)
            <div class="card custom-card">
                <form action="{{ route('exam-results.store-bulk') }}" method="POST" class="ajax-form">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th>Roll No</th>
                                        <th class="text-start">Student Name</th>
                                        <th style="width: 150px;">Marks Obtained</th>
                                        <th style="width: 150px;">Attendance</th>
                                        <th>Teacher Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    {{-- Get the specific mark for the selected subject/exam --}}
                                    @php
                                    $existing = $student->examResults->first();
                                    @endphp

                                    <tr>
                                        <td>{{ $student->roll_number }}</td>
                                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td>
                                            <input type="number" name="marks[{{ $student->id }}][score]"
                                                class="form-control text-center fw-bold"
                                                value="{{ $existing ? $existing->marks_obtained : '' }}"
                                                placeholder="0.00">
                                        </td>
                                        <td>
                                            <select name="marks[{{ $student->id }}][attendance]" class="form-select">
                                                <option value="P" {{ ($existing->attendance ?? '') == 'P' ? 'selected' :
                                                    '' }}>Present</option>
                                                <option value="A" {{ ($existing->attendance ?? '') == 'A' ? 'selected' :
                                                    '' }}>Absent</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <textarea name="marks[{{ $student->id }}][{{ $subject->id }}][remarks]"
                                                class="form-control" rows="1"
                                                placeholder="Enter comments...">{{ $existing->teacher_remarks ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success px-5 shadow-sm">
                            <i class="fe fe-check-circle me-2"></i>Submit All Marks
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection