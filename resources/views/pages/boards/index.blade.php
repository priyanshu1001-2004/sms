@extends('layouts.master')

@section('content')

<style>
    .class-card.selected-active { border: 2px solid #6259ca; background-color: #f8f9ff; }
    .section-chip {
        background: #6259ca;
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        margin-bottom: 4px;
    }
    .section-chip .remove-section {
        margin-left: 8px;
        cursor: pointer;
        font-size: 14px;
        line-height: 1;
    }
    .section-chip .remove-section:hover { color: #ff5b5b; }
</style>

<div class="main-content app-content mt-4">
    <div class="side-app">
        <div class="main-container container-fluid">

            @if(auth()->user()->hasRole('super_admin'))
            <div class="alert alert-info text-center">
                <h5><i class="fe fe-info"></i> Action Required</h5>
                <p>Please select an organization from the header to manage Boards.</p>
            </div>
            @else

            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Educational Boards</h3>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fe fe-plus"></i> Add New Board
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
                                            <th>Short Name</th>
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Assigned Classes</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($boards as $index => $board)
                                        <tr>
                                            <td>{{ $boards->firstItem() + $index }}</td>
                                            <td><strong>{{ $board->name }}</strong></td>
                                            <td>{{ $board->short_name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info-transparent">{{ $board->code ?? 'N/A'
                                                    }}</span></td>
                                            <td>
                                                <span class="badge {{ $board->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $board->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-transparent text-primary px-3">
                                                    {{ $board->classes->count() }} Classes
                                                </span>
                                            </td>
                                            <td>{{ $board->description ?? 'N/A' }}</td>
                                            <td>{{ formatDate($board->created_at) }}</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">

                                                    <button class="btn btn-success edit-btn" data-id="{{ $board->id }}"
                                                        data-name="{{ $board->name }}"
                                                        data-short_name="{{ $board->short_name }}"
                                                        data-code="{{ $board->code }}"
                                                        data-description="{{ $board->description }}"
                                                        data-status="{{ $board->status ? 1 : 0 }}">
                                                        <i class="fe fe-edit"></i>
                                                    </button>

                                                    <button class="btn btn-danger mx-2 trigger-delete"
                                                        data-url="{{ route('boards.destroy', $board->id) }}"
                                                        data-title="Delete Board"
                                                        data-message="This will delete the board and all its associated data. Proceed?">
                                                        <span class="fe fe-trash-2 fs-14"></span>
                                                    </button>



                                                    @php
                                                    // Prepare the data: ['LKG' => 'B', 'UKG' => 'A', '10th' => 'A, B']
                                                    $currentSelections =
                                                    $board->classes->groupBy('name')->map(function($group) {
                                                    return $group->pluck('section')->implode(', ');
                                                    })->toArray();
                                                    @endphp

                                                    <button class="manage-classes-btn" data-id="{{ $board->id }}"
                                                        data-selected='@json($currentSelections)'>
                                                        Manage
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No boards found for this organization.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $boards->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @endif

            <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Add New Board</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">×</button>
                        </div>
                        <form id="CreateForm" class="ajax-form" method="POST" action="{{ route('boards.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Board Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            data-rules="required|min:3|max:100" placeholder="e.g. CBSE">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Short Name</label>
                                        <input type="text" name="short_name" class="form-control"
                                            data-rules="nullable|max:20">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" data-rules="required">
                                            <option selected value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="2"
                                            data-rules="nullable|max:255"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="reset" class="btn btn-light" id="clearaddBtn">Clear</button>
                                <button type="submit" class="btn btn-success px-4">Save Board</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header bg-gray text-white">
                            <h5 class="modal-title">Edit Board Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">×</button>
                        </div>
                        <form id="EditForm" class="ajax-form" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Board Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="edit_name" class="form-control"
                                            data-rules="required|min:3">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Short Name</label>
                                        <input type="text" name="short_name" id="edit_short_name" class="form-control"
                                            data-rules="nullable">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" id="edit_status" class="form-control"
                                            data-rules="required">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" id="edit_description" class="form-control"
                                            data-rules="nullable"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-gray text-white">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="manageModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalTitle">Manage Classes</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">×</button>
                        </div>
                        <form id="SyncForm" class="ajax-form" method="POST" action="{{ route('classes.sync') }}">
                            @csrf
                            <input type="hidden" name="board_id" id="target_board_id">
                            <div class="modal-body">
                                <div class="alert alert-light border mb-4">
                                    <small class="text-muted"><i class="fe fe-info me-1"></i> Check the classes you want
                                        to enable for this board. Unchecking a class will remove it from the
                                        board.</small>
                                </div>

                                <div class="p-4 border rounded bg-light">
                                    <div class="row">
                                        @php
                                        $masterList = ['Pre-KG', 'LKG', 'UKG', '1st', '2nd', '3rd', '4th', '5th', '6th',
                                        '7th', '8th', '9th', '10th', '11th', '12th'];
                                        @endphp
                                        @foreach($masterList as $item)
                                        <div class="col-md-4 col-6 mb-4">
                                            <div class="p-3 border rounded shadow-sm h-100 class-card"
                                                id="card_{{ $item }}" style="transition: all 0.3s ease;">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <input type="checkbox" class="form-check-input class-checkbox"
                                                        name="classes[{{ $item }}][checked]" value="1"
                                                        id="cls_{{ $item }}" data-class="{{ $item }}">
                                                    <label class="form-check-label fw-bold fs-15"
                                                        for="cls_{{ $item }}">{{ $item }}</label>
                                                </div>

                                                <div class="section-management-zone" style="display: none;">
                                                    <label class="small text-muted mb-1">Active Sections:</label>
                                                    <div class="d-flex flex-wrap gap-1 mb-3 section-tag-container"
                                                        id="tag_container_{{ $item }}">
                                                    </div>

                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control section-add-input"
                                                            placeholder="New section..." data-class="{{ $item }}">
                                                        <button class="btn btn-primary add-section-trigger"
                                                            type="button" data-class="{{ $item }}">
                                                            <i class="fe fe-plus"></i>
                                                        </button>
                                                    </div>
                                                    {{-- This hidden input carries the final comma-separated string to
                                                    your Controller --}}
                                                    <input type="hidden" name="classes[{{ $item }}][sections]"
                                                        class="final-section-data" value="A">
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // 1. Event Delegation: Works even after AJAX table refreshes
        $(document).on('click', '.edit-btn', function () {
            let btn = $(this);
            let id = btn.data('id');

            // 2. Fill standard text fields
            // .val() handles null/undefined gracefully
            $('#edit_name').val(btn.data('name'));
            $('#edit_short_name').val(btn.data('short_name'));
            $('#edit_description').val(btn.data('description'));

            let statusVal = String(btn.data('status'));

            // Set value and trigger 'change' for UI plugins (like Select2)
            $('#edit_status').val(statusVal).trigger('change');

            // 4. Update Form Action URL
            $('#EditForm').attr('action', `/boards/${id}`);

            // 5. Show the Modal
            $('#editModal').modal('show');
        });

       $(document).ready(function () {
    
    // Core function to update the UI and the hidden input
    function updateSectionUI(className, sectionString) {
        let container = $(`#tag_container_${className}`);
        let hiddenInput = $(`#card_${className}`).find('.final-section-data');
        container.empty();
        
        // Convert string to array and clean it
        let sections = sectionString ? sectionString.split(',').map(s => s.trim()).filter(s => s !== "") : [];
        
        // Render Tags
        sections.forEach(s => {
            container.append(`
                <span class="section-chip">
                    ${s} 
                    <span class="remove-section" data-class="${className}" data-val="${s}">&times;</span>
                </span>
            `);
        });

        // Sync back to hidden input for Laravel Controller
        hiddenInput.val(sections.join(', '));
    }

    // Modal Opening Logic
    $(document).on('click', '.manage-classes-btn', function () {
        let selectedData = $(this).data('selected'); // Object: {"10th": "A, B", "LKG": "A"}
        $('#target_board_id').val($(this).data('id'));

        // Reset UI State
        $('.class-checkbox').prop('checked', false);
        $('.section-management-zone').hide();
        $('.class-card').removeClass('selected-active');
        $('.section-tag-container').empty();

        if (selectedData) {
            Object.keys(selectedData).forEach(function(className) {
                let checkbox = $(`.class-checkbox[data-class="${className}"]`);
                checkbox.prop('checked', true);
                
                let card = $(`#card_${className}`);
                card.addClass('selected-active');
                card.find('.section-management-zone').show();
                
                // Show existing sections from database
                updateSectionUI(className, selectedData[className]);
            });
        }
        $('#manageModal').modal('show');
    });

    // Handle Check/Uncheck
    $(document).on('change', '.class-checkbox', function () {
        let className = $(this).data('class');
        let card = $(`#card_${className}`);
        if ($(this).is(':checked')) {
            card.addClass('selected-active');
            card.find('.section-management-zone').fadeIn();
            updateSectionUI(className, "A"); // Default section
        } else {
            card.removeClass('selected-active');
            card.find('.section-management-zone').fadeOut();
        }
    });

    // Handle Adding New Section
    $(document).on('click', '.add-section-trigger', function() {
        let className = $(this).data('class');
        let inputField = $(`#card_${className}`).find('.section-add-input');
        let newVal = inputField.val().trim().toUpperCase();
        
        if(newVal) {
            let hiddenInput = $(`#card_${className}`).find('.final-section-data');
            let currentVal = hiddenInput.val();
            let updated = currentVal ? currentVal + ', ' + newVal : newVal;
            
            updateSectionUI(className, updated);
            inputField.val(''); // Clear input
        }
    });

    // Handle Deleting Section
    $(document).on('click', '.remove-section', function() {
        let className = $(this).data('class');
        let valToRemove = $(this).data('val');
        let hiddenInput = $(`#card_${className}`).find('.final-section-data');
        
        let sections = hiddenInput.val().split(',').map(s => s.trim());
        let filtered = sections.filter(s => s !== valToRemove);
        
        updateSectionUI(className, filtered.join(', '));
    });
});
    });
</script>
@endsection