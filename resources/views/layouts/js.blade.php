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

        // 1. Frontend Validation Check
        if (typeof validateForm === "function" && !validateForm(form)) {
            form.find('.is-invalid:first').focus();
            toastr.warning("Please correct the highlighted errors.");
            return;
        }

        showBtnLoader(submitBtn);

        let requestType = 'POST';
        let formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: requestType,
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                resetBtnLoader(submitBtn);
                toastr.success(res.message || 'Data saved successfully');

                // --- Modal Handling ---
                let modal = form.closest('.modal');
                if (modal.length) {
                    // Modal hide karne se pehle reset
                    if (form.data('reset') != 0) {
                        form[0].reset();
                        form.find('input[type="hidden"]').not('[name="_token"], [name="_method"]').val('');
                        if ($.isFunction($.fn.select2)) {
                            form.find('.select2').val(null).trigger('change');
                        }
                    }
                    // Bootstrap modal close
                    let modalInstance = bootstrap.Modal.getInstance(modal[0]);
                    if (modalInstance) modalInstance.hide();
                } else {
                    // Page form reset logic
                    if (form.data('reset') != 0) {
                        form[0].reset();
                    }
                }

                // --- Table Auto-Reload ---
                if (form.data('reload') != 0) {
                    $('#data-table-container').load(window.location.href + ' #data-table-container > *', function (response, status, xhr) {
                        if (status == "error") {
                            console.log("Error reloading table: " + xhr.status);
                        }
                        // Re-init plugins after content load
                        if (typeof initSelect2 === "function") initSelect2();
                        if ($.isFunction($.fn.tooltip)) $('[data-bs-toggle="tooltip"]').tooltip();
                    });
                }

                $(document).trigger('ajaxFormSuccess', [res, form]);
            },
            error: function (xhr) {
                resetBtnLoader(submitBtn);
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();

                if (xhr.status === 422) {
                    let res = xhr.responseJSON;
                    let errors = res.errors;

                    if (!errors && res.message) {
                        toastr.error(res.message);
                        return;
                    }

                    $.each(errors, function (key, value) {
                        // Array field names fix (e.g. user.name -> user_name)
                        let input = form.find(`[name="${key}"], [name="${key}[]"], [id="${key}"]`);

                        if (input.length) {
                            input.addClass('is-invalid');
                            // Select2 Error placement fix
                            if (input.hasClass('select2-hidden-accessible')) {
                                input.next('.select2-container').after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            } else {
                                input.after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            }
                        }
                    });
                    toastr.error(res.message || "Please fix validation errors.");
                } else {
                    // Detailed console error for debugging
                    console.error("AJAX Error:", xhr.responseText);
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
        let checkboxId = checkbox.attr('id') || '';

        // ✅ prevent double click + UI glitch
        checkbox.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                status: isChecked
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                toastr.success(res.message || 'Status updated');

                // Find the badge in the same row
                let badge = checkbox.closest('tr').find('.status-cell span');

                if (res.new_status !== undefined) {
                    let statusVal = res.new_status == 1;

                    // 1. Check if this is an Exam Publish toggle
                    if (checkboxId.includes('publishToggle')) {
                        if (statusVal) {
                            badge.attr('class', 'badge bg-success-transparent text-success').text('Published');
                        } else {
                            badge.attr('class', 'badge bg-warning-transparent text-warning').text('In Progress');
                        }
                    }
                    // 2. Default logic for Teachers, Students, etc.
                    else {
                        if (statusVal) {
                            badge.attr('class', 'badge bg-success-transparent text-success').text('Active');
                        } else {
                            badge.attr('class', 'badge bg-danger-transparent text-danger').text('Inactive');
                        }
                    }
                }
            },
            error: function (xhr) {
                console.error('ERROR:', xhr.responseText);
                toastr.error('Failed to update status');
                // Revert checkbox state on error
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

        $('#filterForm').find('input, select').val('');

        if ($('.select2').length > 0) {
            $('.select2').val('').trigger('change');
        }

        window.history.pushState({}, '', url);

        $('#data-table-container').load(url + ' #data-table-container > *', function () {
            toastr.success('Filters cleared and data reset');
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