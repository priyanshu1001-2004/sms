@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Parent Account Settings</h1>
            </div>

            <div class="row">
                <div class="col-xl-8 col-lg-8">
                    {{-- Personal Details Card --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Edit Personal Information</h3>
                        </div>
                        <div class="card-body"  id="data-table-container">
                            <form action="{{ route('profile.update.parent') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row" id="data-table-container">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <input type="text" class="form-control bg-light" value="{{ $profile->first_name }} {{ $profile->last_name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email (Login ID)</label>
                                        <input type="text" class="form-control bg-light" value="{{ $profile->email }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Mobile Number</label>
                                        <input type="text" name="mobile_number" class="form-control" value="{{ $profile->mobile_number }}" data-rules="required">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Occupation</label>
                                        <input type="text" name="occupation" class="form-control" value="{{ $profile->occupation }}">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Address</label>
                                        <textarea name="address" class="form-control" rows="3">{{ $profile->address }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>

                    {{-- PASSWORD CHANGE SECTION (Common for all roles) --}}
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
                                <button type="submit" class="btn btn-danger">Change My Password</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Left Side: Children Info --}}
                <div class="col-xl-4 col-lg-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">My Children</h3></div>
                        <div class="card-body">
                            @php $children = \App\Models\Student::where('parent_id', $profile->id)->get(); @endphp
                            @forelse($children as $child)
                                <div class="d-flex align-items-center mb-3 border p-2 rounded">
                                    <img src="{{ $child->student_photo ? asset('storage/'.$child->student_photo) : asset('assets/images/users/default.png') }}" class="avatar avatar-md brround me-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $child->first_name }}</h6>
                                        <small class="text-muted">Class: {{ $child->class->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">No students linked yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection