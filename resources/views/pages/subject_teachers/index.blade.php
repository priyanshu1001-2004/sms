@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header">
                <h1 class="page-title">Subject Teacher Assignment</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Staff Assignment</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- ASSIGNMENT FORM CARD --}}
                <div class="col-xl-12 col-md-12">
                    <div class="card custom-card shadow-sm">
                        <div class="card-header border-bottom">
                            <h3 class="card-title"><i class="fe fe-user-plus me-2 text-primary"></i>Assign Staff to Subject</h3>
                        </div>
                        <div class="card-body">
                            {{-- Added data-reset="1" and data-reload="1" --}}
                            <form action="{{ route('subject_teachers.store') }}" class="ajax-form" id="assignTeacherForm" method="POST" data-reload="1" data-reset="1">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-muted small">STEP 1: SELECT CLASS</label>
                                        <select id="class_selector" class="form-control select2" data-placeholder="Choose Class">
                                            <option value=""></option>
                                            @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-muted small">STEP 2: SELECT SUBJECT</label>
                                        <select name="class_subject_id" id="subject_selector" class="form-control select2" disabled data-placeholder="Pick Class First">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small">STEP 3: ASSIGN TEACHER</label>
                                        <select name="teacher_id" id="teacher_selector" class="form-control select2" data-placeholder="Search Teacher...">
                                            <option value=""></option>
                                            @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }} ({{ $teacher->designation }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                            <i class="fe fe-check-circle me-1"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- DATA LISTING --}}
                <div class="col-xl-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Current Staff Assignments</h3>
                        </div>
                        <div class="card-body" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="basic-datatable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Assigned Teacher</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignments as $item)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary-transparent text-primary px-3 py-2">
                                                    {{ $item->classSubject->class->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                {{ $item->classSubject->subject->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm brround bg-info-transparent text-info me-2">
                                                        {{ substr($item->teacher->first_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->teacher->first_name }} {{ $item->teacher->last_name }}</div>
                                                        <small class="text-muted">{{ $item->teacher->designation }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger-light trigger-delete"
                                                    data-url="{{ route('subject_teachers.destroy', $item->id) }}"
                                                    data-bs-toggle="tooltip" title="Remove Assignment">
                                                    <i class="fe fe-trash-2"></i>
                                                </button>
                                            </td>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Handle Class Change to load Subjects
        $('#class_selector').on('change', function () {
            let classId = $(this).val();
            let subjectDropdown = $('#subject_selector');

            if (!classId) {
                subjectDropdown.prop('disabled', true).html('<option value=""></option>').trigger('change');
                return;
            }

            subjectDropdown.html('<option value="">Loading subjects...</option>').trigger('change');

            $.ajax({
                url: `/get-subjects-by-class/${classId}`,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    let options = '<option value="">-- Choose Assigned Subject --</option>';

                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, item) {
                            let subName = item.subject ? item.subject.name : 'Unknown';
                            let subCode = item.subject ? item.subject.code : '';
                            options += `<option value="${item.id}">${subName} (${subCode})</option>`;
                        });
                        subjectDropdown.prop('disabled', false).html(options).trigger('change');
                    } else {
                        subjectDropdown.html('<option value="">No subjects assigned</option>').trigger('change').prop('disabled', true);
                    }
                }
            });
        });

        /**
         * SPECIAL FIX FOR SELECT2 RESET:
         * Your global ajax-form handler likely calls form.reset().
         * For Select2, we need to listen for that and trigger 'change'.
         */
        $(document).on('ajaxFormSuccess', function(e, form) {
            if ($(form).attr('id') === 'assignTeacherForm') {
                // Manually reset Select2 visual state
                $('.select2').val(null).trigger('change');
                // Specifically disable subject dropdown again
                $('#subject_selector').prop('disabled', true);
            }
        });
    });
</script>
@endsection