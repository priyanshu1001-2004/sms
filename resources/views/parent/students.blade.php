@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Children</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Student List</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                @forelse($students as $student)
                <div class="col-md-6 col-xl-4">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="{{ $student->student_photo ? asset('storage/'.$student->student_photo) : asset('assets/images/users/default.png') }}"
                                        alt="img" class="avatar avatar-xxl brround">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold">{{ $student->first_name }} {{ $student->last_name }}</h5>
                                    <p class="text-muted mb-0">Roll No: {{ $student->roll_number ?? 'N/A' }}</p>

                                    <span
                                        class="badge bg-primary-transparent text-primary border border-primary-subtle mt-1">
                                        Class: {{ $student->class->name ?? 'N/A' }}
                                    </span>

                                    {{-- NEW: Class Teacher Badge --}}
                                    @if($student->class && $student->class->classTeacher &&
                                    $student->class->classTeacher->teacher)
                                    <span class="badge bg-info-transparent text-info border border-info-subtle mt-1">
                                        <i class="fe fe-user me-1"></i>
                                        Tutor: {{ $student->class->classTeacher->teacher->first_name }} {{
                                        $student->class->classTeacher->teacher->last_name }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <p class="text-muted mb-1 small">Admission No</p>
                                        <h6 class="mb-0 fw-semibold">{{ $student->admission_number }}</h6>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1 small">Gender</p>
                                        <h6 class="mb-0 fw-semibold">{{ ucfirst($student->gender) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between bg-light-transparent">
                            <a href="javascript:void(0)" class="btn btn-sm btn-info-light">
                                <i class="fe fe-eye me-1"></i> Profile
                            </a>
                            <button type="button" class="btn btn-sm btn-primary-light view-subjects-btn"
                                data-id="{{ $student->id }}"
                                data-name="{{ $student->first_name }} {{ $student->last_name }}">
                                <i class="fe fe-book me-1"></i> Subjects
                            </button>
                            <a href="javascript:void(0)" class="btn btn-sm btn-success-light">
                                <i class="fe fe-file-text me-1"></i> Fees
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center mt-5">
                    <div class="card p-5">
                        <h4 class="text-muted">No students linked to your account.</h4>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- SUBJECT MODAL --}}
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Subjects for <span id="student_name_display"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Subject Name</th>
                                <th>Type</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody id="subjects_list_body">
                            {{-- Data via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts') {{-- Make sure this matches your master.blade.php yield --}}
<script>
    $(document).on('click', '.view-subjects-btn', function () {
        let studentId = $(this).data('id');
        let studentName = $(this).data('name');

        // Set UI state
        $('#student_name_display').text(studentName);
        $('#subjects_list_body').html('<tr><td colspan="3" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
        $('#subjectModal').modal('show');

        // Fetch Data
        $.ajax({
            url: `/parents/student-subjects/${studentId}`,
            method: 'GET',
            // Inside your AJAX success function
            success: function (response) {
                let html = '';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(item => {
                        // Check the nested relationship: assigned_teacher -> teacher
                        let teacherName = (item.assigned_teacher && item.assigned_teacher.teacher)
                            ? `${item.assigned_teacher.teacher.first_name} ${item.assigned_teacher.teacher.last_name}`
                            : '<span class="text-muted small"><i>Not Assigned</i></span>';

                        let subjectType = item.subject.type
                            ? `<span class="badge bg-info-transparent text-info">${item.subject.type}</span>`
                            : '<span class="badge bg-light text-dark">Theory</span>';

                        html += `<tr>
                <td>
                    <div class="fw-bold text-dark">${item.subject.name}</div>
                    <small class="text-muted">${item.subject.subject_code ?? ''}</small>
                </td>
                <td>${subjectType}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fe fe-user-check text-success me-2"></i>
                        ${teacherName}
                    </div>
                </td>
            </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center text-muted">No subjects assigned.</td></tr>';
                }
                $('#subjects_list_body').html(html);
            },
            error: function () {
                $('#subjects_list_body').html('<tr><td colspan="3" class="text-center text-danger">Failed to fetch subjects. Please try again.</td></tr>');
            }
        });
    });
</script>
@endsection