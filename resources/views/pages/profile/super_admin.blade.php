@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">System Owner Profile</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Super Admin</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-preview mb-3">
                                    <img src="{{ asset('assets/images/users/default.png') }}" 
                                         alt="Super Admin" class="rounded-circle img-thumbnail" style="width: 130px; height: 130px;">
                                </div>
                                <h4 class="mb-0 fw-bold">{{ auth()->user()->name }}</h4>
                                <p class="text-danger fw-bold">SUPER ADMINISTRATOR</p>
                                <p class="small text-muted">Platform Master Access</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    {{-- PERSONAL ACCOUNT CARD --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Manage My Credentials</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update.super') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" data-rules="required">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Login Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" data-rules="required|email">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Contact Phone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5">Update My Account</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- SECURITY & PASSWORD CARD --}}
                    <div class="card mt-4">
                        <div class="card-header border-bottom bg-danger-transparent">
                            <h3 class="card-title text-danger">Master Password Reset</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update.password') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">New Master Password</label>
                                        <input type="password" name="password" class="form-control" data-rules="required|min:8">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" data-rules="required|same:password">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger px-5">Securely Update Password</button>
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