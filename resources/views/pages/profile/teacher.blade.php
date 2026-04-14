@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">My Professional Profile</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Teacher Account</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                {{-- Left Side: Quick Info & Identity --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-preview mb-3">
                                    <img src="{{ $profile->teacher_photo ? asset('storage/' . $profile->teacher_photo) : asset('assets/images/users/default.png') }}"
                                        alt="Teacher Photo" class="rounded-circle img-thumbnail"
                                        style="width: 140px; height: 140px; object-fit: cover;">
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $profile->first_name }} {{ $profile->last_name }}</h4>
                                <p class="text-muted">{{ $profile->designation }}</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <span
                                        class="badge bg-primary-transparent text-primary border border-primary-subtle">Joined:
                                        {{ \Carbon\Carbon::parse($profile->date_of_joining)->format('M Y') }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="professional-info">
                                <h6 class="fw-bold text-uppercase small text-muted mb-3">Professional Summary</h6>
                                <p class="mb-1 text-dark"><strong>Qualification:</strong> {{ $profile->qualification }}
                                </p>
                                <p class="mb-1 text-dark"><strong>Experience:</strong> {{ $profile->work_experience ??
                                    'N/A' }}</p>
                                @if($profile->resume_path)
                                <a href="{{ asset('storage/' . $profile->resume_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info w-100 mt-3">
                                    <i class="fe fe-file-text me-2"></i>View My Resume
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Employment Card (Read-Only) --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employment Details</h3>
                        </div>
                        <div class="card-body py-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="fw-bold">EPF Number:</td>
                                        <td>{{ $profile->epf_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">PAN Number:</td>
                                        <td>{{ $profile->pan_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Basic Salary:</td>
                                        <td>{{ number_format($profile->basic_salary, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Editable Settings --}}
                <div class="col-xl-8 col-lg-7">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Personal & Contact Settings</h3>
                        </div>
                        <div class="card-body" id="data-table-container">
                            <form action="{{ route('profile.update.teacher') }}" class="ajax-form" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row" id="data-table-container">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-muted small">Full Name (Read-Only)</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $profile->first_name }} {{ $profile->last_name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-muted small">Email (Login ID)</label>
                                        <input type="email" class="form-control bg-light" value="{{ $profile->email }}"
                                            readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Mobile Number</label>
                                        <input type="text" name="mobile_number" class="form-control"
                                            value="{{ $profile->mobile_number }}" data-rules="required">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Emergency Contact</label>
                                        <input type="text" name="emergency_contact_number" class="form-control"
                                            value="{{ $profile->emergency_contact_number }}">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Marital Status</label>
                                        <select name="marital_status" class="form-select">
                                            <option value="Single" {{ $profile->marital_status == 'Single' ? 'selected'
                                                : '' }}>Single</option>
                                            <option value="Married" {{ $profile->marital_status == 'Married' ?
                                                'selected' : '' }}>Married</option>
                                            <option value="Divorced" {{ $profile->marital_status == 'Divorced' ?
                                                'selected' : '' }}>Divorced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Blood Group</label>
                                        <select name="blood_group" class="form-select">
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                            <option value="{{ $bg }}" {{ $profile->blood_group == $bg ? 'selected' : ''
                                                }}>{{ $bg }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Date of Birth</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ \Carbon\Carbon::parse($profile->date_of_birth)->format('d-m-Y') }}"
                                            readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Current Address</label>
                                        <textarea name="current_address" class="form-control" rows="3"
                                            data-rules="required">{{ $profile->current_address }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Permanent Address</label>
                                        <textarea name="permanent_address" class="form-control"
                                            rows="3">{{ $profile->permanent_address }}</textarea>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Update Profile Photo</label>
                                        <input type="file" name="teacher_photo" class="form-control">
                                    </div>
                                </div>

                                <div class="card-footer px-0 pb-0 text-end">
                                    <button type="submit" class="btn btn-primary px-6">Save Professional
                                        Profile</button>
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
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Min 6 characters" data-rules="required|min:6">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="Repeat password" data-rules="required|same:password">
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