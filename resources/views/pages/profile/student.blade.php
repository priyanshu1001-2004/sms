@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header">
                <h1 class="page-title">My Profile</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile Details</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- Left Sidebar: Academic Info --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-preview mb-3">
                                    <img src="{{ $profile->student_photo ? asset('storage/' . $profile->student_photo) : asset('assets/images/users/default.png') }}" 
                                         alt="Profile Photo" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $profile->first_name }} {{ $profile->last_name }}</h4>
                                <p class="text-muted">Student | ID: {{ $profile->admission_number }}</p>
                                <span class="badge {{ $profile->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $profile->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <hr>
                            <div class="academic-summary">
                                <h6 class="fw-bold"><i class="fe fe-book-open me-2"></i>Academic Summary</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Class:</span> <strong>{{ $profile->class->name ?? 'N/A' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Roll No:</span> <strong>{{ $profile->roll_number ?? 'N/A' }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>Joined On:</span> <strong>{{ \Carbon\Carbon::parse($profile->admission_date)->format('d M, Y') }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Forms --}}
                <div class="col-xl-8 col-lg-7">
                    
                    {{-- PERSONAL DETAILS CARD --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Edit Personal Details</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update.student') }}" class="ajax-form" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row"  id="data-table-container">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">First Name</label>
                                        <input type="text" class="form-control" value="{{ $profile->first_name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Last Name</label>
                                        <input type="text" class="form-control" value="{{ $profile->last_name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <input type="email" class="form-control" value="{{ $profile->email }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Mobile Number</label>
                                        <input type="text" name="mobile_number" class="form-control" value="{{ $profile->mobile_number }}" data-rules="required">
                                    </div>
                                    
                                    <div class="col-12 mt-3 mb-2 border-bottom pb-1">
                                        <h6 class="text-primary">Personal & Health Info</h6>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Gender</label>
                                        <input type="text" class="form-control" value="{{ ucfirst($profile->gender) }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($profile->date_of_birth)->format('d-m-Y') }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Blood Group</label>
                                        <select name="blood_group" class="form-select">
                                            <option value="">Select</option>
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                                <option value="{{ $bg }}" {{ $profile->blood_group == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Religion</label>
                                        <input type="text" name="religion" class="form-control" value="{{ $profile->religion }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Caste</label>
                                        <input type="text" name="caste" class="form-control" value="{{ $profile->caste }}">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Update Profile Photo</label>
                                        <input type="file" name="student_photo" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="card-footer px-0 pb-0 text-end border-0">
                                    <button type="submit" class="btn btn-primary px-5">Save Profile Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- SECURITY & PASSWORD CARD --}}
                    <div class="card mt-4">
                        <div class="card-header border-bottom bg-danger-transparent">
                            <h3 class="card-title text-danger">Security & Password</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update.password') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">New Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" data-rules="required|min:6">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" data-rules="required|same:password">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger px-5">Change My Password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection