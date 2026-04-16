@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-header-title">Grading System</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="fe fe-plus me-2"></i>Add New Grade
                </button>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>Grade Name</th>
                                            <th>Percentage Range (%)</th>
                                            <th>Grade Point (GPA)</th>
                                            <th>Remarks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($grades as $grade)
                                        <tr class="text-center">
                                            <td class="fw-bold text-primary fs-15">{{ $grade->name }}</td>
                                            <td>
                                                <span class="badge bg-primary-transparent text-primary px-3">
                                                    {{ $grade->percent_from }}% - {{ $grade->percent_to }}%
                                                </span>
                                            </td>
                                            <td><span class="fw-semibold">{{ number_format($grade->grade_point, 2)
                                                    }}</span></td>
                                            <td class="text-muted italic small">{{ $grade->remarks ?? '---' }}</td>
                                            <td>
                                                <div class="btn-list justify-content-center">
                                                    <button class="btn btn-sm btn-info-light edit-grade-btn"
                                                        data-id="{{ $grade->id }}" data-name="{{ $grade->name }}"
                                                        data-from="{{ $grade->percent_from }}"
                                                        data-to="{{ $grade->percent_to }}"
                                                        data-point="{{ $grade->grade_point }}"
                                                        data-remarks="{{ $grade->remarks }}">
                                                        <i class="fe fe-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger-light delete-btn"
                                                        data-url="{{ route('grades.destroy', $grade->id) }}">
                                                        <i class="fe fe-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">No grading scale defined yet.</td>
                                        </tr>
                                        @endforelse
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

{{-- Dynamic Modal for Add/Edit --}}
<div class="modal fade" id="addGradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Grading Scale Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('grades.store') }}" method="POST" class="ajax-form" id="gradeForm" data-reload="1">
                @csrf
                <input type="hidden" id="grade_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Grade Name (e.g. A+)</label>
                            <input type="text" name="name" id="g_name" class="form-control" placeholder="A+"
                                data-rules="required">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Min Percentage (%)</label>
                            <input type="number" step="0.01" name="percent_from" id="g_from" class="form-control"
                                placeholder="80" data-rules="required">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Max Percentage (%)</label>
                            <input type="number" step="0.01" name="percent_to" id="g_to" class="form-control"
                                placeholder="100" data-rules="required">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Grade Point (GPA)</label>
                            <input type="number" step="0.01" name="grade_point" id="g_point" class="form-control"
                                placeholder="4.0" data-rules="required">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="remarks" id="g_remarks" class="form-control"
                                placeholder="Excellent">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-5">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.edit-grade-btn', function () {
        let data = $(this).data();

        // Change Form for Update
        $('#gradeForm').attr('action', '/grades/' + data.id);
        $('#grade_method').val('PUT');
        $('.modal-title').text('Edit Grading Scale');

        // Prefill
        $('#g_name').val(data.name);
        $('#g_from').val(data.from);
        $('#g_to').val(data.to);
        $('#g_point').val(data.point);
        $('#g_remarks').val(data.remarks);

        $('#addGradeModal').modal('show');
    });

    // Reset modal when closed (so "Add" works correctly)
    $('#addGradeModal').on('hidden.bs.modal', function () {
        $('#gradeForm').attr('action', "{{ route('grades.store') }}");
        $('#grade_method').val('POST');
        $('#gradeForm')[0].reset();
        $('.modal-title').text('Add New Grade');
    });
</script>
@endsection