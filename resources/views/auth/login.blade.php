<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .portal-logo {
            width: 56px;
            height: 56px;
            background: #4f46e5;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .portal-logo svg {
            width: 28px;
            height: 28px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 5px;
        }

        .input-wrap .fi {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            display: flex;
        }

        .input-wrap input {
            padding-left: 44px !important;
            height: 50px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            font-size: 15px;
            color: #111827;
            width: 100%;
            transition: all .2s;
        }

        .input-wrap input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            background: #fff;
            outline: none;
        }

        .input-wrap input.is-invalid {
            border-color: #dc3545;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .btn-portal {
            width: 100%;
            height: 50px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            margin-top: 1rem;
            transition: all .2s;
            cursor: pointer;
        }

        .btn-portal:hover {
            background: #4338ca;
        }

        .btn-portal:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .invalid-feedback {
            font-size: 12px;
            margin-bottom: 10px;
            display: block;
            color: #dc3545;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="portal-logo">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
        </div>

        <h2 class="text-center fw-bold mb-1" style="font-size: 22px; color: #111827;">System Access</h2>
        <p class="text-center mb-4" style="font-size: 14px; color: #6b7280;">Secure Identity Portal</p>

        <form method="POST" action="{{ route('login') }}" class="ajax-form" novalidate>
            @csrf
            <div class="mb-3">
                <label class="form-label">Identity ID</label>
                <div class="input-wrap">
                    <span class="fi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                        </svg>
                    </span>
                    <input type="text" name="login_id" id="login_id" placeholder="Email / Phone / Admission No"
                        data-rules="required">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <span class="fi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" placeholder="••••••••"
                        data-rules="required|min:6">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-wrap">
                    <span class="fi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Repeat password" data-rules="required|same:password">
                </div>
            </div>

            <button type="submit" class="btn-portal" id="submitBtn">
                <span class="btn-text">Authenticate & Login</span>
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {

            // 1. Global Ajax Setup for CSRF Protection
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /** * 2. The Validation Engine 
             * Handles frontend rules before sending to server
             */
            window.validateForm = function (form) {
                let isValid = true;

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();

                form.find('[data-rules]').each(function () {
                    let input = $(this);
                    let rules = input.data('rules').split('|');
                    let value = input.val() ? input.val().trim() : '';
                    let error = "";

                    rules.forEach(rule => {
                        // Required Check
                        if (rule === 'required' && !value) {
                            error = "This field is required";
                        }

                        // Email Format Check
                        if (rule === 'email' && value && !/^\S+@\S+\.\S+$/.test(value)) {
                            error = "Invalid email format";
                        }

                        // Minimum Character Check
                        if (rule.startsWith('min:')) {
                            let min = parseInt(rule.split(':')[1]);
                            if (value.length < min) {
                                error = `Minimum ${min} characters required`;
                            }
                        }

                        // Password Match Check
                        if (rule.startsWith('same:')) {
                            let targetName = rule.split(':')[1];
                            let targetVal = form.find(`[name="${targetName}"]`).val();
                            if (value !== targetVal) {
                                error = "Passwords do not match";
                            }
                        }
                    });

                    if (error) {
                        isValid = false;
                        input.addClass('is-invalid');
                        // Placement: Put error message outside the .input-wrap container
                        input.closest('.input-wrap').after(`<div class="invalid-feedback d-block">${error}</div>`);
                    }
                });
                return isValid;
            };

            /** * 3. Unified Form Submission Logic (AJAX) 
             */
            $(document).on('submit', '.ajax-form', function (e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = form.find('#submitBtn');
                let btnTextSpan = submitBtn.find('.btn-text');
                let originalBtnText = btnTextSpan.text();

                // Run Frontend Validation
                if (!validateForm(form)) {
                    form.find('.is-invalid:first').focus();
                    toastr.warning("Please correct the highlighted errors.");
                    return false;
                }

                // Start Loading State (Disable button + Spinner)
                submitBtn.prop('disabled', true);
                btnTextSpan.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(), // Sends all inputs + CSRF
                    dataType: 'json',
                    success: function (response) {
                        toastr.success(response.message || 'Login successful! Redirecting...');

                        // Short delay to allow the user to see the success toast
                        setTimeout(function () {
                            window.location.href = response.redirect || '/dashboard';
                        }, 1000);
                    },
                    error: function (xhr) {
                        // Reset Button State
                        submitBtn.prop('disabled', false);
                        btnTextSpan.text(originalBtnText);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Clear existing manual validation errors
                            form.find('.is-invalid').removeClass('is-invalid');
                            form.find('.invalid-feedback').remove();

                            // Loop through Laravel backend errors
                            $.each(errors, function (key, value) {
                                let input = form.find(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.closest('.input-wrap').after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            });
                            toastr.error("Invalid login credentials.");
                        } else {
                            toastr.error(xhr.responseJSON?.message || "An unexpected error occurred. Please try again.");
                        }
                    }
                });
            });

            
            $(document).on('keyup change', 'input', function () {
                let input = $(this);
                if (input.hasClass('is-invalid')) {
                    input.removeClass('is-invalid');
                    input.closest('.input-wrap').next('.invalid-feedback').remove();
                }
            });
        });
    </script>
</body>

</html>