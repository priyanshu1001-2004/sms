@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <div class="page-header">
                <h1 class="page-title">Account & School Settings</h1>
            </div>

            <div class="row">
                <div class="col-xl-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="nav flex-column nav-pills p-3" id="v-pills-tab" role="tablist">
                                <a class="nav-link active mb-2" data-bs-toggle="pill" href="#personal-tab">
                                    <i class="fe fe-user me-2"></i>My Profile
                                </a>
                                <a class="nav-link mb-2" data-bs-toggle="pill" href="#school-tab">
                                    <i class="fe fe-home me-2"></i>School Identity
                                </a>
                                <a class="nav-link mb-2" data-bs-toggle="pill" href="#bank-tab">
                                    <i class="fe fe-credit-card me-2"></i>Bank Details
                                </a>
                                <a class="nav-link " data-bs-toggle="pill" href="#security-tab">
                                    <i class="fe fe-lock me-2"></i>Change Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5">
                            <div class="tab-content">

                                <div class="tab-pane fade show active" id="personal-tab">
                                    <h5 class="fw-bold mb-4">Personal Information</h5>
                                    <form action="{{ route('profile.update.master') }}" class="ajax-form" method="POST">
                                        @csrf @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $user->name }}" data-rules="required">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ $user->phone }}" data-rules="required|digits">
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary mt-4 rounded-pill px-5 shadow-sm">Save
                                            Profile</button>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="school-tab">
                                    <h5 class="fw-bold mb-4 text-primary">School Identity & Branding</h5>

                                    <form action="{{ route('organization.update.full') }}" class="ajax-form"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="form_tab" value="identity">

                                        <div class="row g-3">
                                            <div class="col-md-12 text-center mb-4">
                                                <img src="{{ $org->logo ? asset('storage/'.$org->logo) : asset('assets/images/brand/logo.png') }}"
                                                    class="avatar avatar-xxl bradius border mb-3 shadow-sm"
                                                    id="school-logo-preview">
                                                <input type="file" name="logo" class="form-control w-50 mx-auto"
                                                    accept="image/*">
                                                <small class="text-muted d-block mt-2">Recommended: 512x512px PNG (Max
                                                    2MB)</small>
                                            </div>

                                            <div class="col-12">
                                                <h6 class="fw-bold text-muted text-uppercase small">Basic Info</h6>
                                                <hr class="mt-1">
                                            </div>

                                            <div class="col-md-5">
                                                <label class="form-label">School Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $org->name }}" data-rules="required">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Short Name</label>
                                                <input type="text" name="short_name" class="form-control"
                                                    value="{{ $org->short_name }}" placeholder="Ex: STPS">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Est. Year</label>
                                                <input type="number" name="established_at" class="form-control"
                                                    value="{{ $org->established_at }}" placeholder="YYYY">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Registration No.</label>
                                                <input type="text" name="registration_number" class="form-control"
                                                    value="{{ $org->registration_number }}"
                                                    placeholder="Official Reg No.">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Principal Name</label>
                                                <input type="text" name="principal_name" class="form-control"
                                                    value="{{ $org->principal_name }}" placeholder="Head of School">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">School Motto</label>
                                                <input type="text" name="motto" class="form-control"
                                                    value="{{ $org->motto }}"
                                                    placeholder="Ex: Education for Excellence">
                                            </div>

                                            <div class="col-12">
                                                <h6 class="fw-bold text-muted text-uppercase small">Contact & Location
                                                </h6>

                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Official Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ $org->email }}">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Official Phone</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ $org->phone }}">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Alternate Phone</label>
                                                <input type="text" name="alternate_phone" class="form-control"
                                                    value="{{ $org->alternate_phone }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Full Address</label>
                                                <textarea name="address" class="form-control" rows="2"
                                                    placeholder="Street, Area, Landmark">{{ $org->address }}</textarea>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">City</label>
                                                <input type="text" name="city" class="form-control"
                                                    value="{{ $org->city }}">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">State</label>
                                                <input type="text" name="state" class="form-control"
                                                    value="{{ $org->state }}">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" name="pincode" class="form-control"
                                                    value="{{ $org->pincode }}">
                                            </div>


                                        </div>

                                        <div class="mt-5 text-end">
                                            <button type="submit" class="btn btn-primary rounded-pill px-6 shadow-sm">
                                                <i class="fe fe-save me-2"></i>Update School Identity
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="bank-tab">
                                    <h5 class="fw-bold mb-4 text-success">Financial Settings</h5>

                                    <form action="{{ route('organization.update.full') }}" class="ajax-form"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="name" value="{{ $org->name }}">
                                        <input type="hidden" name="form_tab" value="bank">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted small fw-bold text-uppercase">Bank
                                                    Name</label>
                                                <input type="text" name="bank_name" class="form-control"
                                                    value="{{ $org->bank_name }}" placeholder="e.g. HDFC Bank">
                                            </div>

                                            <div class="col-md-6">
                                                <label
                                                    class="form-label text-muted small fw-bold text-uppercase">Account
                                                    Holder</label>
                                                <input type="text" name="account_holder" class="form-control"
                                                    value="{{ $org->account_holder }}"
                                                    placeholder="Official School Name">
                                            </div>

                                            <div class="col-md-6">
                                                <label
                                                    class="form-label text-muted small fw-bold text-uppercase">Account
                                                    Number</label>
                                                <input type="text" name="account_number" class="form-control"
                                                    value="{{ $org->account_number }}">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label text-muted small fw-bold text-uppercase">IFSC
                                                    Code</label>
                                                <input type="text" name="ifsc_code" class="form-control"
                                                    value="{{ $org->ifsc_code }}">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label text-muted small fw-bold text-uppercase">Tax ID
                                                    (GST/PAN)</label>
                                                <input type="text" name="tax_id" class="form-control"
                                                    value="{{ $org->tax_id }}">
                                            </div>

                                            <div class="col-md-8">
                                                <label
                                                    class="form-label text-muted small fw-bold text-uppercase text-primary">UPI
                                                    ID</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-primary-transparent"><i
                                                            class="fe fe-trending-up text-primary"></i></span>
                                                    <input type="text" name="upi_id" class="form-control border-primary"
                                                        value="{{ $org->upi_id }}" placeholder="schoolname@bank">
                                                </div>
                                                <small class="text-muted mt-2 d-block">Used to generate automatic fee
                                                    payment QR codes for parents.</small>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="card border shadow-none bg-light mb-0">
                                                    <div class="card-body text-center p-4">
                                                        <h6 class="fw-bold mb-3 text-uppercase small">Official Payment
                                                            QR</h6>

                                                        <div class="bg-white d-inline-block p-3 border shadow-sm mb-3"
                                                            style="border-radius: 15px;">
                                                            @if($org->upi_id)
                                                            @php
                                                            $upiUrl =
                                                            "upi://pay?pa=".$org->upi_id."&pn=".urlencode($org->name)."&mc=5211&cu=INR&mode=02";
                                                            @endphp
                                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data={{ urlencode($upiUrl) }}"
                                                                alt="UPI QR Code" class="img-fluid"
                                                                style="width: 160px; height: 160px;">
                                                            @else
                                                            <div class="py-5 text-muted px-3">
                                                                <i class="fe fe-qr-code d-block fs-30 mb-2"></i>
                                                                <p class="mb-0 small">Enter UPI ID to generate secure QR
                                                                </p>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div
                                                            class="d-flex align-items-center justify-content-center mt-2">
                                                           
                                                            <span
                                                                class="badge bg-success-transparent text-success rounded-pill px-3">
                                                                <i class="fe fe-shield me-1"></i> Verified Merchant
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5 text-end">
                                            <button type="submit" class="btn btn-success rounded-pill px-6 shadow-sm">
                                                <i class="fe fe-check-circle me-2"></i>Save Bank Details
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="security-tab">
                                    <h5 class="fw-bold mb-4 text-danger">Security & Authentication</h5>
                                    <form action="{{ route('profile.update.password') }}" class="ajax-form"
                                        method="POST">
                                        @csrf @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">New Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    data-rules="required|min:6">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm New Password</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    data-rules="required|same:password">
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-danger mt-4 rounded-pill px-5 shadow-sm">Update
                                            Password</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection