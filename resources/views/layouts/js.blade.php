<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>


<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/sticky.js') }}"></script>
<script src="{{ asset('assets/plugins/sidemenu/sidemenu.js') }}"></script>
<script src="{{ asset('assets/plugins/sidebar/sidebar.js') }}"></script>
<script src="{{ asset('assets/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/plugins/p-scroll/pscroll.js') }}"></script>

<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/switcher/js/switcher.js') }}"></script>

<script src="../assets/js/custom1.js"></script>
<script src="../assets/js/themeColors.js"></script>
<script src="../assets/plugins/p-scroll/pscroll-1.js"></script>
<script src="../assets/js/typehead.js"></script>
<script src="../assets/plugins/bootstrap5-typehead/autocomplete.js"></script>
<script src="../assets/js/table-data.js"></script>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>

<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>


<script>
    'use strict';

    // Toastr Setup
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "2000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };


    // Ajax CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || (window.AppConfig ? window.AppConfig.csrfToken : '')
        }
    });

    /** ✅ BUTTON LOADER */
    window.showBtnLoader = function (button) {

        button = button instanceof jQuery ? button[0] : button;
        if (!button || button.disabled) return;
        button.setAttribute('data-original-content', button.innerHTML);
        button.style.width = button.offsetWidth + 'px';
        button.style.height = button.offsetHeight + 'px';
        button.disabled = true;

        button.innerHTML = `
        <span class="spinner-border spinner-border-sm"></span>
    `;
    };

    window.resetBtnLoader = function (button) {
        button = button instanceof jQuery ? button[0] : button;
        if (!button) return;
        let originalContent = button.getAttribute('data-original-content');
        if (originalContent) {
            button.innerHTML = originalContent;
        }
        button.style.width = '';
        button.style.height = '';
        button.disabled = false;
    };

    /** ✅ FRONTEND VALIDATION - FIXED numeric & placement */
    window.validateForm = function (form) {
        let isValid = true;
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        form.find('[data-rules]').each(function () {
            let input = $(this);
            let rules = input.data('rules').split('|');
            let value = input.val() ? input.val().trim() : '';
            let error = "";
            let password = form.find('[name="password"]').val();
            let confirm = form.find('[name="password_confirmation"]').val();


            rules.forEach(rule => {

                if (rule === 'required' && !value) {
                    error = "This field is required";
                }

                if (rule === 'email' && value && !/^\S+@\S+\.\S+$/.test(value)) {
                    error = "Invalid email format";
                }

                if (rule === 'digits' && value && !/^\d+$/.test(value)) {
                    error = "Only numbers allowed";
                }

                if (rule === 'url' && value && !/^(https?:\/\/)?([\w\-])+\.{1}([a-zA-Z]{2,63})([\w\-./?%&=]*)?$/.test(value)) {
                    error = "Invalid URL";
                }

                if (rule.startsWith('min:')) {
                    let min = parseInt(rule.split(':')[1]);
                    if (value.length < min) {
                        error = `Minimum ${min} characters required`;
                    }
                }

                if (rule.startsWith('max:')) {
                    let max = parseInt(rule.split(':')[1]);
                    if (value.length > max) {
                        error = `Maximum ${max} characters allowed`;
                    }
                }

                if (rule.startsWith('same:')) {
                    let targetName = rule.split(':')[1];
                    let targetInput = form.find(`[name="${targetName}"]`);
                    let targetValue = targetInput.val() ? targetInput.val().trim() : '';

                    if (value !== targetValue) {
                        error = "Passwords do not match";
                    }
                }
            });

            if (error) {
                isValid = false;
                input.addClass('is-invalid');

                let container = input.parent('.input-group');
                if (container.length) {
                    container.after(`<div class="invalid-feedback d-block">${error}</div>`);
                } else {
                    input.after(`<div class="invalid-feedback d-block">${error}</div>`);
                }
            }
        });

        return isValid;
    };

    /** ✅ GLOBAL AJAX SUBMISSION */
    $(document).on('submit', '.ajax-form', function (e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = form.find('[type="submit"]')[0];

        // Trigger Frontend Validation
        if (!validateForm(form)) {
            if (!isValid) {
                form.find('.is-invalid:first').focus();
            }
            toastr.warning("Please correct the highlighted errors.");
            return;
        }

        showBtnLoader(submitBtn);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method') || 'POST',
            data: new FormData(form[0]),
            contentType: false,
            processData: false,
            headers: {
                'Accept': 'application/json'
            },
            success: function (res) {
                resetBtnLoader(submitBtn);
                toastr.success(res.message || 'Data saved successfully');

                // 1. Reset Form
                if (form.data('reset') != 0) form[0].reset();

                // 2. Hide Modal
                let modal = form.closest('.modal');
                if (modal.length) {
                    bootstrap.Modal.getInstance(modal[0]).hide();
                }

                if (form.data('reload') != 0) {
                    $('#data-table-container').load(window.location.href + ' #data-table-container > *', function () {
                        if (typeof initSelect2 === "function") initSelect2();
                    });
                }
            },
            error: function (xhr) {
                resetBtnLoader(submitBtn);

                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();

                if (xhr.status === 422) {
                    let res = xhr.responseJSON;
                    let errors = res.errors;

                    // If there is a top-level message but no field-specific errors, show the message
                    if (!errors && res.message) {
                        toastr.error(res.message);
                        return;
                    }

                    $.each(errors, function (key, value) {
                        let input = form.find(`[name="${key}"], [name="${key}[]"]`);
                        // ... (keep your existing logic for appending the error message) ...
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                        }
                    });

                    // Use the specific message from the server if available
                    toastr.error(res.message || "Please fix the validation errors.");
                } else {
                    toastr.error("Error: " + (xhr.responseJSON?.message || "Server error occurred"));
                }
            }
        });
    });

    //global clear button 
    $('#clearaddBtn').click(() => $('#CreateForm')[0].reset());

    //global toggle button 
    $(document).off('change', '.globalStatusToggle').on('change', '.globalStatusToggle', function () {
        let checkbox = $(this);
        let url = checkbox.data('url');
        let isChecked = checkbox.is(':checked') ? 1 : 0;

        // ✅ prevent double click + UI glitch
        checkbox.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                status: isChecked // Pass the explicit status to the server
            },
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            success: function (res) {
                toastr.success(res.message || 'Status updated');

                // Find the badge in the same row
                let badge = checkbox.closest('tr').find('.status-cell span');

                // Using the status returned from server to ensure sync
                if (res.new_status !== undefined) {
                    let isActive = res.new_status == 1;
                    badge.attr('class', 'badge ' +
                        (isActive ? 'bg-success' : 'bg-danger text-white')
                    ).text(isActive ? 'Active' : 'Inactive');
                }
            },
            error: function (xhr) {
                console.error('ERROR:', xhr.responseText);
                toastr.error('Failed to update status');

                checkbox.prop('checked', !checkbox.prop('checked'));
            },
            complete: function () {
                checkbox.prop('disabled', false);
            }
        });
    });

    $(document).on('submit', '#filterForm', function (e) {
        e.preventDefault();
        let url = $(this).attr('action') + '?' + $(this).serialize();

        $('#data-table-container').load(url + ' #data-table-container > *');

        window.history.pushState({}, '', url);
    });

    $(document).on('click', '.btn-reset', function (e) {
        e.preventDefault();

        let url = $(this).attr('href');
        $('#filterForm')[0].reset();

        window.history.pushState({}, '', url);

        $('#data-table-container').load(url + ' #data-table-container > *', function () {
            toastr.success('Filters cleared');
        });
    });

    $(document).ready(function () {
        // Generic initialization
        function initSelect2(parent = 'body') {
            if ($.fn.select2) {
                $('.select2').each(function () {
                    $(this).select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        dropdownParent: $(parent) // Specific modal focus fix
                    });
                });
            }
        }

        initSelect2(); // Normal page loading ke liye

        // Modal ke andar focus fix karne ke liye
        $('#addModal, #editModal').on('shown.bs.modal', function () {
            initSelect2($(this));
        });

        // Summernote as it is
        if ($.fn.summernote) {
            $('.summernote').summernote({
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
        }
    });
    
let deleteUrl = "";
let rowToDelete = null;

// 1. Open Modal - Already using delegation, this is GOOD.
$(document).on('click', '.trigger-delete', function () {
    let btn = $(this);
    deleteUrl = btn.data('url');
    rowToDelete = btn.closest('tr');

    $('#modal-title').text(btn.data('title') || 'Are you sure?');
    $('#modal-message').text(btn.data('message') || "You won't be able to revert this!");

    let btnClass = btn.data('btn-class') || 'btn-danger';
    $('#modal-confirm-btn').attr('class', 'btn ' + btnClass);

    $('#globalConfirmModal').modal('show');
});

// 2. Handle the Actual Request - Optimized
$(document).on('click', '#modal-confirm-btn', function (e) {
    e.preventDefault();
    let confirmBtn = $(this);

    confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Deleting...');

    $.ajax({
        url: deleteUrl,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            toastr.success(res.message || 'Deleted successfully');
            $('#globalConfirmModal').modal('hide');

            if ($('#data-table-container').length) {
                // FADE OUT then REFRESH
                rowToDelete.fadeOut(400, function () {
                    // Reload the container
                    $('#data-table-container').load(window.location.href + ' #data-table-container > *', function () {
                        // RE-INITIALIZE DATATABLE IF YOU USE ONE
                        if ($.fn.DataTable.isDataTable('#basic-datatable')) {
                             $('#basic-datatable').DataTable();
                        }
                    });
                });
            } else {
                location.reload();
            }
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON?.message || 'Something went wrong');
        },
        complete: function () {
            confirmBtn.prop('disabled', false).text('Confirm');
            // Reset URL to prevent accidental double deletes
            deleteUrl = ""; 
        }
    });
});





</script>


<script src="{{ asset('assets/js/custom.js') }}"></script>