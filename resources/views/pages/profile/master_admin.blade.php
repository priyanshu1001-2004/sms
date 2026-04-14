@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Organization Account (Master Admin)</h1>
            </div>

            <div class="row">
                {{-- Organization Identity --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="user-preview mb-3">
                                    {{-- Use Organization Logo if available --}}
                                    @php $org = \App\Models\Organization::find(auth()->user()->organization_id); @endphp
                                    <img src="{{ $org->logo ? asset('storage/' . $org->logo) : asset('assets/images/brand/logo-default.png') }}" 
                                         alt="Org Logo" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: contain;">
                                </div>
                                <h4 class="mb-0 fw-bold">{{ $org->name }}</h4>
                                <p class="text-muted">Master Administrator</p>
                            </div>
                            <hr>
                            <div class="org-details">
                                <p><strong>Email:</strong> {{ $org->email ?? 'N/A' }}</p>
                                <p><strong>Phone:</strong> {{ $org->phone ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Address:</strong></p>
                                <small class="text-muted">{{ $org->address ?? 'No address set' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    {{-- PERSONAL DETAILS --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">My Personal Details</h3>
                        </div>
                        <div class="card-body" id="data-table-container">
                            <form action="{{ route('profile.update.master') }}" class="ajax-form" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">My Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" data-rules="required">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Login Email</label>
                                        <input type="text" class="form-control bg-light" value="{{ auth()->user()->email }}" readonly>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">My Phone</label>
                                        <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}" data-rules="required">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Personal Info</button>
                            </form>
                        </div>
                    </div>

                    {{-- PASSWORD CHANGE --}}
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
                                        <input type="password" name="password" class="form-control" data-rules="required|min:6">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" class="form-control" data-rules="required|same:password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection