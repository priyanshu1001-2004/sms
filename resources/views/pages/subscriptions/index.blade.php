@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">

        <div class="main-container container-fluid">


            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Basic Datatable</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add Subscription
                            </button>
                        </div>
                        <!-- // fillter form  -->
                        <div class="px-5 py-5 border-bottom">
                            <form id="filterForm" action="{{ route('subscriptions.index') }}" method="GET">
                                <div class="row g-3 align-items-end">

                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Organization</label>
                                        <select name="organization_id" class="form-control form-select ">
                                            <option value="">All Organizations</option>
                                            @foreach($organizations as $org)
                                            <option value="{{ $org->id }}" {{ request('organization_id')==$org->id ?
                                                'selected' : '' }}>
                                                {{ $org->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Plan Name</label>
                                        <select name="plan_name" class="form-control form-select ">
                                            <option value="">All Plans</option>
                                            <option value="basic" {{ request('plan_name')=='basic' ? 'selected' : '' }}>
                                                Basic</option>
                                            <option value="premium" {{ request('plan_name')=='premium' ? 'selected' : ''
                                                }}>Premium</option>
                                            <option value="enterprise" {{ request('plan_name')=='enterprise'
                                                ? 'selected' : '' }}>Enterprise</option>
                                        </select>
                                    </div>

                                    <!-- <div class="col-md-2">
                                        <label class="form-label small fw-bold">Status</label>
                                        <select name="status" class="form-control form-select ">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="expired" {{ request('status')=='expired' ? 'selected' : ''
                                                }}>Expired</option>
                                            <option value="trial" {{ request('status')=='trial' ? 'selected' : '' }}>
                                                Trial</option>
                                            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected'
                                                : '' }}>Cancelled</option>
                                        </select>
                                    </div> -->

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary shadow-sm">
                                            <i class="fe fe-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('subscriptions.index') }}"
                                            class="btn btn-secondary btn-reset">
                                            <i class="fe fe-refresh-cw"></i> Reset
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sr</th>
                                            <th data-orderby="true">Origination</th>
                                            <th>Plan</th>
                                            <th data-orderby="true">Amount</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <!-- <th>Status</th> -->
                                            <th data-orderby="true">Exp In</th>
                                            <th>Payment Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subscriptions as $index => $subscription)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $subscription->organization->name ?? 'N/A' }}</td>
                                            <td>{{ $subscription->plan_name }}</td>
                                            <td>{{ $subscription->amount }}</td>
                                            <td>{{ formatDate($subscription->start_date) }}</td>
                                            <td>{{ formatDate($subscription->end_date) }}</td>
                                            <!-- <td>{{ $subscription->status }}</td> -->
                                            <td>
                                                @if($subscription->days_remaining > 0)
                                                <span class="badge bg-success">
                                                    {{ $subscription->days_remaining }} Days Left
                                                </span>
                                                @else
                                                <span class="badge bg-danger">Expired</span>
                                                @endif
                                            </td>
                                            <td>{{ $subscription->payment_reference ?? 'N/A' }}</td>
                                        </tr>
                                        @empty

                                        <tr>
                                            <td colspan="9" class="text-center">No subscriptions found.</td>
                                        </tr>

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $subscriptions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->


            <!-- Create Subscription Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">

                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addModalLabel">Add New Subscription</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close">x</button>
                        </div>

                        <div class="modal-body">
                            <form id="CreateForm" class="ajax-form" method="POST"
                                action="{{ route('subscriptions.store') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="organization_id">Organization <span
                                                class="text-danger">*</span></label>
                                        <select name="organization_id" id="organization_id" class="form-control"
                                            data-rules="required">
                                            <option value="">Select Organization</option>
                                            @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="plan_name">Plan Name <span
                                                class="text-danger">*</span></label>
                                        <select name="plan_name" id="plan_name" class="form-control"
                                            data-rules="required">
                                            <option value="">Select Plan</option>
                                            <option value="basic">Basic</option>
                                            <option value="premium">Premium</option>
                                            <option value="enterprise">Enterprise</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="amount">Amount <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="amount" id="amount" step="0.01"
                                                class="form-control" placeholder="0.00" data-rules="required|numeric">
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-6 mb-3">
                                        <label class="form-label" for="status">Status <span
                                                class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control" data-rules="required">
                                            <option value="">Select Status</option>
                                            <option value="active">Active</option>
                                            <option value="trial">Trial</option>
                                            <option value="expired">Expired</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div> -->

                                    <div class="col-6 mb-3">
                                        <label class="form-label" for="payment_reference">Payment Reference</label>
                                        <input type="text" name="payment_reference" id="payment_reference"
                                            class="form-control" placeholder="TXN ID, Receipt No, etc.">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="start_date">
                                            Start Date <span class="text-danger">*</span>
                                        </label>

                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                            data-rules="required|date" min="{{ now()->toDateString() }}"
                                            value="{{ old('start_date', now()->toDateString()) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="end_date">End Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" min="{{ now()->toDateString() }}"
                                            data-rules="required|date">
                                    </div>


                                </div>

                                <div class="modal-footer px-0 pb-0 pt-3 border-top">
                                    <button type="reset" class="btn btn-light" id="clearaddBtn">Clear</button>
                                    <button type="submit" class="btn btn-success px-4">Save Subscription</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>





        </div>
    </div>
</div>



@section('scripts')


@endsection

@endsection