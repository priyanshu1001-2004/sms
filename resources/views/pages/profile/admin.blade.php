@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Administrator Profile</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Account Settings</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-preview mb-3">
                                    <img src="{{ asset('assets/images/users/default.png') }}" 
                                         alt="Admin Photo" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px;">
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $profile->name }}</h4>
                                <p class="text-muted">System Administrator</p>
                                <div class="badge bg-primary-transparent text-primary border border-primary-subtle">
                                    Org ID: {{ $profile->organization_id }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    {{-- PERSONAL DETAILS CARD --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Edit Administrative Details</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update.admin') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row" id="data-table-container">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ $profile->name }}" data-rules="required">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email (Read-Only)</label>
                                        <input type="email" class="form-control bg-light" value="{{ $profile->email }}" readonly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $profile->phone }}" data-rules="required">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Description / Bio</label>
                                        <textarea name="description" class="form-control" rows="4" placeholder="Brief about your role...">{{ $profile->description }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5">Update Admin Profile</button>
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
                                    <button type="submit" class="btn btn-danger px-5">Change Password</button>
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