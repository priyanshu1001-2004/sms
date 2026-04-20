@extends('layouts.master')

@section('content')

<style>
    #timetableModal .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    #timetableModal .modal-header {
        border-bottom: 1px solid #f1f1f1;
        padding: 1.5rem;
    }

    /* Table Modernizing */
    #timetableModal .table {
        border-collapse: separate;
        border-spacing: 0 8px;
        /* Adds space between rows */
    }

    #timetableModal .table thead th {
        border: none;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 12px;
    }

    #timetableModal .day-cell {
        border: none !important;
        background: #fff !important;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.7rem;
        vertical-align: middle !important;
    }

    /* Slot Card inside Table */
    .tt-slot-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #667eea;
        border-radius: 8px;
        padding: 8px 10px;
        text-align: left;
        transition: all 0.2s;
    }

    .tt-slot-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .tt-subject {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .tt-teacher {
        font-size: 0.75rem;
        color: #64748b;
    }

    .tt-room {
        display: inline-block;
        font-size: 0.65rem;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        color: #475569;
    }

    .tt-recess {
        font-size: 0.7rem;
        font-weight: 800;
        color: #d97706;
        background: #fffbeb;
        padding: 10px;
        border-radius: 8px;
        border: 1px dashed #fcd34d;
    }
</style>

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

                            <button type="button" class="btn btn-sm btn-warning-light view-timetable-btn"
                                data-id="{{ $student->id }}" data-class-id="{{ $student->class_id }}"
                                data-name="{{ $student->first_name }} {{ $student->last_name }}">
                                <i class="fe fe-calendar me-1"></i> Timetable
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



{{-- TIMETABLE MODAL --}}
<div class="modal fade" id="timetableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <div class="avatar avatar-md brround bg-warning-transparent text-warning me-3">
                    <i class="fe fe-calendar"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="tt_student_name_title">Student Schedule</h5>
                    <small class="text-muted">Weekly academic roadmap for <span id="tt_student_name"
                            class="fw-bold"></span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table text-center table-vcenter">
                        <thead id="timetable_header"></thead>
                        <tbody id="timetable_content"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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

<script>
    $(document).on('click', '.view-timetable-btn', function () {
        let studentId = $(this).data('id');
        let studentName = $(this).data('name');

        $('#tt_student_name').text(studentName);
        $('#timetable_header').html('');
        $('#timetable_content').html('<tr><td colspan="10" class="py-5"><div class="spinner-border text-primary spinner-border-sm"></div><br><span class="small text-muted">Fetching Data...</span></td></tr>');
        $('#timetableModal').modal('show');

        $.ajax({
            url: `/parents/student-timetable/${studentId}`,
            method: 'GET',
            success: function (res) {
                // 1. Header
                let headerHtml = '<tr><th class="day-cell">Day</th>';
                res.slots.forEach(slot => {
                    let time = (typeof moment !== "undefined") ? moment(slot.start_time, 'HH:mm:ss').format('hh:mm A') : slot.start_time;
                    headerHtml += `<th><div class="mb-0">${slot.name}</div><div class="small fw-normal text-muted">${time}</div></th>`;
                });
                headerHtml += '</tr>';
                $('#timetable_header').html(headerHtml);

                // 2. Body
                let bodyHtml = '';
                res.days.forEach(day => {
                    bodyHtml += `<tr><td class="day-cell text-uppercase">${day.name}</td>`;

                    res.slots.forEach(slot => {
                        let entry = (res.data[day.id] && res.data[day.id][slot.id]) ? res.data[day.id][slot.id] : null;

                        if (slot.is_break) {
                            bodyHtml += `<td class="align-middle"><div class="tt-recess text-uppercase">Interval</div></td>`;
                        } else if (entry) {
                            bodyHtml += `
                            <td class="align-middle" style="min-width: 180px;">
                                <div class="tt-slot-card">
                                    <div class="tt-subject text-truncate">${entry.subject_name}</div>
                                    <div class="tt-teacher text-truncate">${entry.teacher_name}</div>
                                    <div class="mt-2"><span class="tt-room">Room: ${entry.room}</span></div>
                                </div>
                            </td>`;
                        } else {
                            bodyHtml += `<td class="align-middle"><span class="text-light fs-10">---</span></td>`;
                        }
                    });
                    bodyHtml += `</tr>`;
                });
                $('#timetable_content').html(bodyHtml);
            }
        });
    });
</script>
@endsection