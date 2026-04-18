@extends('layouts.master')

@section('content')
<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">

            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please manage admin users from this panel.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Admin Management</h3>

                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add Admin
                            </button>
                        </div>

                        <div class="card-body pt-0" id="data-table-container">
                            <div class="table-responsive">

                                <table class="table table-bordered text-nowrap border-bottom saas-table"
                                    id="basic-datatable">

                                    <thead class="table-primary">
                                        <tr>
                                            <th>Sr</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($admins as $index => $admin)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin?->user->phone ?? '' }}</td>

                                            <td>
                                                <span class="badge bg-{{ $admin->status ? 'success' : 'danger' }}">
                                                    {{ $admin->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>

                                            <td>{{ $admin->description }}</td>

                                            <td>
                                                <button class="btn btn-sm btn-info-light edit-btn"
                                                    data-id="{{ $admin->id }}" data-name="{{ $admin->name }}"
                                                    data-email="{{ $admin->email }}" data-phone="{{ $admin->user->phone }}"
                                                    data-status="{{ $admin->status ? 1 : 0 }}"
                                                    data-description="{{ $admin->description }}"
                                                    >
                                                    <i class="fe fe-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No admins found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $admins->links() }}
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ================= ADD MODAL ================= --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>

            <div class="modal-body">

                <form method="POST" action="{{ route('admins.store') }}" class="ajax-form">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-6">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" data-rules="required|min:3">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" data-rules="required|email">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" data-rules="required|min:10|max:15">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Status</label>
                            <select name="status" class="form-control" data-rules="required">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="form-group col-md-6 mb-2">
                            <label class="required">Password</label>
                            <input type="password" name="password" class="form-control" data-rules="required|min:6">
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group col-md-6 mb-2">
                            <label class="required">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                data-rules="required|same:password">
                        </div>


                        <div class="mb-3 col-12">
                            <label>Description</label>
                            <textarea name="description" id="" rows="2" class="form-control"></textarea>
                        </div>

                    </div>


                    <div class="modal-footer px-0 pb-0 pt-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-4">Save Admin</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- ================= EDIT MODAL ================= --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-gray text-white">
                <h5 class="modal-title">Edit Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
            </div>

            <div class="modal-body">

                <form id="EditForm" method="POST" class="ajax-form">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="mb-3 col-6">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                data-rules="required|min:3">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control"
                                data-rules="required|email">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" data-rules="required">
                        </div>

                        <div class="mb-3 col-6">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-control" data-rules="required">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label>Description</label>
                            <textarea name="description" id="edit_description" rows="2" class="form-control"></textarea>
                        </div>

                    </div>



                    <div class="modal-footer px-0 pb-0 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-gray text-white px-4">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).on('click', '.edit-btn', function () {

        let id = $(this).data('id');

        $('#EditForm').attr('action', '/admins/' + id);

        $('#edit_name').val($(this).data('name'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_phone').val($(this).data('phone'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_description').val($(this).data('description'));


        $('#editModal').modal('show');
    });
</script>
@endsection