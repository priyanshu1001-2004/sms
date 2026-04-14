@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Students</h1>
            </div>

            {{-- FILTER SECTION --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('teacher.students') }}" method="GET" class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Class</label>
                            <select name="class_id" class="form-control select2">
                                <option value="">All My Classes</option>
                                @foreach($myClasses as $cls)
                                    <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fe fe-filter me-1"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- STUDENT TABLE --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap border-bottom">
                            <thead class="bg-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Student Name</th>
                                    <th>Roll No</th>
                                    <th>Class</th>
                                    <th>Gender</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ $student->student_photo ? asset('storage/'.$student->student_photo) : asset('assets/images/users/default.png') }}" 
                                             class="avatar avatar-md brround" alt="img">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <small class="text-muted">ID: {{ $student->admission_number }}</small>
                                    </td>
                                    <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info-transparent text-info px-3">{{ $student->class->name }}</span></td>
                                    <td>{{ ucfirst($student->gender) }}</td>
                                    <td>
                                        <div class="btn-list">
                                            <a href="#" class="btn btn-sm btn-primary-light" title="Academic Record">
                                                <i class="fe fe-file-text"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success-light" title="Attendance">
                                                <i class="fe fe-check-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center p-5 text-muted">No students found for your assigned classes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection