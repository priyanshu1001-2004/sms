@extends('layouts.master')

@section('title', 'Timetable Groups')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            {{-- PAGE HEADER --}}
            <div class="page-header">
                <h1 class="page-title">Timetable Groups</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Academic</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Groups</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- LEFT: ADD FORM --}}
                <div class="col-md-4">
                    <div class="card custom-card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Create Schedule Group</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Create groups like 'Junior Wing', 'Senior Wing' to manage different timings.</p>
                            
                            <form action="{{ route('timetable-groups.store') }}" method="POST" class="ajax-form" data-reload="1" data-reset="1">
                                @csrf
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Junior Section" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                    <i class="fe fe-plus-circle me-1"></i> Save Group
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: LIST TABLE --}}
                <div class="col-md-8">
                    <div class="card custom-card">
                        <div class="card-header border-bottom d-flex justify-content-between">
                            <h3 class="card-title">Existing Groups</h3>
                            <span class="badge bg-primary-transparent text-primary rounded-pill">
                                {{ count($groups) }} Total
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-hover border text-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Group Name</th>
                                            <th>Slots Count</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($groups as $group)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="fw-bold text-dark">{{ $group->name }}</span></td>
                                            <td>
                                                <span class="badge bg-info-transparent text-info">
                                                    {{ $group->time_slots_count ?? 0 }} Slots
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $group->status ? 'success' : 'danger' }}-transparent">
                                                    {{ $group->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-danger-light trigger-delete" 
                                                        data-url="{{ route('timetable-groups.destroy', $group->id) }}"
                                                        data-bs-toggle="tooltip" title="Delete Group">
                                                        <i class="fe fe-trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fe fe-alert-circle fs-40 d-block mb-2"></i>
                                                    <p>No timetable groups found.</p>
                                                </div>
                                            </td>
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
@endsection