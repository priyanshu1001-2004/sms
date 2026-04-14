@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Assignments</h1>
            </div>

            <div class="row">
                {{-- CLASS HEAD CARD --}}
                {{-- CLASS HEAD SECTION --}}
                <div class="col-md-12 col-xl-4">
                    <div class="card custom-card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Classes Managed by You</h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($headClasses as $hc)
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <div class="me-3">
                                    <span class="avatar avatar-md brround bg-primary-transparent text-primary">
                                        <i class="fe fe-home"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-bold">{{ $hc->schoolClass->name }}</h5>
                                    <p class="text-muted mb-0 small">
                                        <i class="fe fe-users me-1"></i> {{ $hc->schoolClass->students_count }} Students
                                    </p>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-icon btn-sm btn-primary-light view-students"
                                        data-id="{{ $hc->class_id }}" data-name="{{ $hc->schoolClass->name }}">
                                        <i class="fe fe-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="p-5 text-center">
                                <p class="text-muted mb-0">No primary class assigned.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- SUBJECT TABLE --}}
                <div class="col-md-12 col-xl-8">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Subject Assignments</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class</th>
                                            <th>Subject</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjectAssignments as $item)
                                        <tr>
                                            <td><span class="badge bg-primary-transparent text-primary px-3">{{
                                                    $item->classSubject->class->name }}</span></td>
                                            <td class="fw-bold text-dark">{{ $item->classSubject->subject->name }}</td>
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
    </div>
</div>

{{-- STUDENT LIST MODAL --}}
<div class="modal fade" id="studentListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student List - <span id="modal_class_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody id="student_list_body">
                            {{-- AJAX Load --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.view-students', function () {
        let classId = $(this).data('id');
        let className = $(this).data('name');

        $('#modal_class_name').text(className);
        $('#student_list_body').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        $('#studentListModal').modal('show');

        $.get(`/teacher/get-class-students/${classId}`, function (response) {
            let html = '';
            if (response.data.length > 0) {
                response.data.forEach(student => {
                    html += `<tr>
                        <td>${student.roll_number ?? 'N/A'}</td>
                        <td class="fw-bold">${student.first_name} ${student.last_name}</td>
                        <td>${student.gender}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="3" class="text-center">No students found in this class.</td></tr>';
            }
            $('#student_list_body').html(html);
        });
    });
</script>
@endsection