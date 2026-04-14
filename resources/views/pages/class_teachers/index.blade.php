@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Class Teacher Assignment</h1>
            </div>

            <div class="row">
                {{-- ASSIGNMENT FORM --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Assign New Class Teacher</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('class_teachers.store') }}" class="ajax-form" method="POST" data-reload="1">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Select Class</label>
                                        <select name="class_id" class="form-control select2" data-rules="required">
                                            <option value="">-- Choose Class --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Select Teacher (Class Head)</label>
                                        <select name="teacher_id" class="form-control select2" data-rules="required">
                                            <option value="">-- Choose Teacher --</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fe fe-plus me-1"></i> Assign Teacher
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ASSIGNMENT LIST --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Current Class Teachers</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="data-table-container">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Assigned Teacher</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assignments as $row)
                                        <tr>
                                            <td class="fw-bold">{{ $row->schoolClass->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md brround me-3" style="background-image: url({{ $row->teacher->teacher_photo ? asset('storage/'.$row->teacher->teacher_photo) : asset('assets/images/users/default.png') }})"></span>
                                                    <div>
                                                        <div class="fw-semibold">{{ $row->teacher->first_name }} {{ $row->teacher->last_name }}</div>
                                                        <small class="text-muted">{{ $row->teacher->mobile_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success-transparent text-success">Primary Head</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-danger-light trigger-delete" 
                                                        data-url="{{ route('class_teachers.destroy', $row->id) }}">
                                                    <i class="fe fe-trash-2"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">No teachers assigned to classes yet.</td>
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