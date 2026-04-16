@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Individual Mark Entry</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Examination</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student-wise Entry</li>
                </ol>
            </div>

            <div class="card custom-card overflow-hidden">
                <div class="card-header border-bottom">
                    <h3 class="card-title"><i class="fe fe-filter me-2"></i>Selection Criteria</h3>
                </div>
                <div class="card-body bg-light-50">
                    <form method="GET" action="{{ route('exam-results.student-wise') }}">
                        <div class="row align-items-end g-3">
                            <div class="col-xl-3 col-md-6">
                                <label class="form-label fw-semibold">1. Examination</label>
                                <select name="exam_id" class="form-control form-select select2-no-search" required>
                                    <option value="">-- Select Exam --</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <label class="form-label fw-semibold">2. Class</label>
                                <select id="filter_class_id" class="form-control form-select select2-no-search">
                                    <option value="">-- Filter by Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-4 col-md-8">
                                <label class="form-label fw-semibold">3. Student Name / Roll No.</label>
                                <select name="student_id" id="student_select" class="form-control select2" required>
                                    <option value="">-- Select Student --</option>
                                    {{-- Loaded via AJAX --}}
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-4">
                                <button type="submit" class="btn btn-primary w-100 shadow-sm py-2">
                                    <i class="fe fe-search me-1"></i> Load Marksheet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($student))
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-primary-transparent">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md brround me-3 bg-primary text-white">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $student->first_name }} {{ $student->last_name }}</h4>
                            <div class="text-muted small">
                                <span class="badge bg-white text-primary border border-primary-subtle rounded-pill me-2">Roll: {{ $student->roll_number }}</span>
                                <span class="badge bg-white text-secondary border border-secondary-subtle rounded-pill">Class: {{ $student->class->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('exam-results.store-bulk') }}" method="POST" class="ajax-form" data-reload="0">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
                    <input type="hidden" name="class_id" value="{{ $student->class_id }}">
                    
                    <div class="card-body p-0">
                        <div class="table-responsive" id="data-table-container">
                            <table class="table table-vcenter text-nowrap table-bordered border-bottom">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4" style="width: 30%;">Subject</th>
                                        <th class="text-center" style="width: 20%;">Marks Obtained</th>
                                        <th class="text-center" style="width: 20%;">Attendance</th>
                                        <th class="text-center">Teacher Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subjects as $subject)
                                    @php $res = $existingResults[$subject->id] ?? null; @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $subject->name }}</div>
                                            <small class="text-muted">{{ $subject->code ?? 'SUB-'. $subject->id }}</small>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.01" 
                                                name="marks[{{ $student->id }}][{{ $subject->id }}][score]" 
                                                class="form-control form-control-lg text-center fw-bold border-primary-subtle" 
                                                value="{{ $res->marks_obtained ?? '' }}" 
                                                placeholder="0.00"
                                                onfocus="this.select()">
                                        </td>
                                        <td class="p-2">
                                            <select name="marks[{{ $student->id }}][{{ $subject->id }}][attendance]" class="form-select form-select-lg">
                                                <option value="P" {{ ($res->attendance ?? '') == 'P' ? 'selected' : '' }}>Present</option>
                                                <option value="A" {{ ($res->attendance ?? '') == 'A' ? 'selected' : '' }}>Absent</option>
                                                <option value="M" {{ ($res->attendance ?? '') == 'M' ? 'selected' : '' }}>Medical</option>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <textarea name="marks[{{ $student->id }}][{{ $subject->id }}][remarks]" 
                                                class="form-control" rows="1" placeholder="Enter comments...">{{ $res->teacher_remarks ?? '' }}</textarea>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <img src="{{ asset('assets/images/no-data.png') }}" alt="" class="mb-3" style="width: 80px; opacity: 0.5;">
                                            <p class="text-muted">No subjects assigned to this class configuration.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light-50 d-flex justify-content-between align-items-center">
                        <div class="text-muted small italic">
                            <i class="fe fe-info me-1"></i> Grades and GPA will be calculated automatically based on your Grade Scale.
                        </div>
                        <button type="submit" class="btn btn-success px-6 btn-lg shadow">
                            <i class="fe fe-save me-2"></i> Update Student Marksheet
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="card custom-card">
                <div class="card-body text-center py-7">
                    <div class="mb-3">
                        <i class="fe fe-user-check fs-50 text-light"></i>
                    </div>
                    <h4 class="text-muted">Please select an Exam, Class, and Student to begin mark entry.</h4>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for professional feel
        $('.select2').select2({
            placeholder: "-- Search Student --",
            allowClear: true,
            width: '100%'
        });

        $('#filter_class_id').on('change', function() {
            let classId = $(this).val();
            let studentSelect = $('#student_select');
            
            studentSelect.html('<option value="">Loading students...</option>');

            if(classId) {
                $.ajax({
                    url: "{{ url('get-students-by-class') }}/" + classId,
                    type: "GET",
                    success: function(data) {
                        let options = '<option value="">-- Select Student --</option>';
                        if(data.length > 0) {
                            data.forEach(student => {
                                // Dynamic string concat for first/last name
                                let fullName = student.first_name + ' ' + student.last_name;
                                options += `<option value="${student.id}">${fullName} (Roll: ${student.roll_number})</option>`;
                            });
                        } else {
                            options = '<option value="">No students found</option>';
                        }
                        studentSelect.html(options).trigger('change');
                    },
                    error: function() {
                        studentSelect.html('<option value="">Error loading data</option>').trigger('change');
                    }
                });
            } else {
                studentSelect.html('<option value="">-- Select Student --</option>').trigger('change');
            }
        });
    });
</script>
@endsection