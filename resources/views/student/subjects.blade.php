@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Curriculum</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Subjects</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Assigned Subjects for Class: <span class="text-primary">{{
                                    $student->class->name ?? 'N/A' }}</span></h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="wd-5p">#</th>
                                            <th>Subject Name</th>
                                            <th>Subject Type</th>
                                            <th>Assigned Teacher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subjects as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span
                                                            class="avatar avatar-sm bg-primary-transparent text-primary brround">
                                                            {{ substr($item->subject->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div class="fw-bold">{{ $item->subject->name }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-transparent text-info">
                                                    {{ $item->subject->type ?? 'Theory' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->assignedTeacher && $item->assignedTeacher->teacher)
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar avatar-xs brround bg-secondary-transparent text-secondary me-2">
                                                        <i class="fe fe-user"></i>
                                                    </div>
                                                    <div class="fw-semibold">
                                                        {{ $item->assignedTeacher->teacher->first_name }}
                                                        {{ $item->assignedTeacher->teacher->last_name }}
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted small"><i>Not assigned yet</i></span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-5">
                                                <img src="{{ asset('assets/images/no-data.png') }}" alt="No Data"
                                                    style="width: 80px;" class="mb-3 d-block mx-auto">
                                                <p class="text-muted">No subjects have been assigned to your class yet.
                                                </p>
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