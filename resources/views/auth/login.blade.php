<x-guest-layout>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body { font-family: 'DM Sans', sans-serif; }

.login-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 2rem 1.75rem 1.75rem;
    width: 100%;
    max-width: 420px;
    margin: auto;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
}

.portal-logo {
    width: 48px; height: 48px;
    background: #4f46e5;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
}
.portal-logo svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 2; }

.role-tabs {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
    background: #f3f4f6;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 1.5rem;
}
.role-tab {
    border: none; background: transparent;
    border-radius: 9px;
    padding: 9px 2px;
    font-size: 10px; font-weight: 700; letter-spacing: .05em;
    color: #9ca3af;
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
    line-height: 1;
}
.role-tab.active {
    background: #fff;
    color: #4f46e5;
    border: 1px solid #e0e0e0;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
}

.input-wrap { position: relative; }
.input-wrap .fi {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #9ca3af; pointer-events: none; display: flex;
}
.input-wrap .fi svg { width: 15px; height: 15px; }
.input-wrap input {
    padding-left: 42px !important;
    height: 46px;
    border-radius: 11px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    font-family: inherit; font-size: 14px; color: #111827;
    width: 100%; box-sizing: border-box;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.input-wrap input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    background: #fff;
}
.input-wrap input.is-invalid { border-color: #ef4444 !important; }
.form-label { font-size: 13px; font-weight: 500; color: #6b7280; margin-bottom: 6px; }

.btn-portal {
    width: 100%; height: 48px;
    background: #4f46e5; color: #fff;
    border: none; border-radius: 12px;
    font-family: inherit; font-size: 14px; font-weight: 600;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: opacity .2s, transform .1s;
    cursor: pointer;
}
.btn-portal:hover { opacity: .9; color: #fff; }
.btn-portal:active { transform: scale(.98); }
.btn-portal:disabled { opacity: .7; cursor: not-allowed; }
.btn-portal .spinner-border { width: .95rem; height: .95rem; border-width: 2px; }

/* @media (prefers-color-scheme: dark) {
    .login-card { background: #1a1a2e; border-color: #2d2d4e; }
    .role-tabs { background: #12122a; }
    .role-tab { color: #6b7280; }
    .role-tab.active { background: #1a1a2e; border-color: #3a3a5c; color: #818cf8; }
    .input-wrap input { background: #1f2937; border-color: #374151; color: #f9fafb; }
    .input-wrap input:focus { background: #111827; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.15); }
    .form-label { color: #9ca3af; }
} */
</style>

<div class="rounded-md">
<div class="">

    <div class="portal-logo">
        <svg viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <path d="M2 17l10 5 10-5"/>
            <path d="M2 12l10 5 10-5"/>
        </svg>
    </div>

    <h2 class="text-center fw-bold mb-1" id="main-title"
        style="font-size:19px;letter-spacing:-.3px;color:#111827;cursor:default;">
        Portal Login
    </h2>
    <p class="text-center mb-4" style="font-size:13px;color:#9ca3af;">
        Please select your role
    </p>

    {{-- Role Tabs --}}
    <div class="role-tabs" id="role-selector">
        <button type="button" class="role-tab active" id="btn-student"
            data-role="student" data-label="Admission No" data-rules="required">
            STUDENT
        </button>
        <button type="button" class="role-tab" id="btn-parent"
            data-role="parent" data-label="Phone Number" data-rules="required|numeric|min:10">
            PARENT
        </button>
        <button type="button" class="role-tab" id="btn-teacher"
            data-role="teacher" data-label="Teacher ID" data-rules="required|min:4">
            TEACHER
        </button>
        <button type="button" class="role-tab" id="btn-admin"
            data-role="admin" data-label="Official Email" data-rules="required|email">
            ADMIN
        </button>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="ajax-form" data-reload="0" data-reset="0">
        @csrf
        <input type="hidden" name="role" id="selected_role" value="student">

        {{-- Login ID --}}
        <div class="mb-3">
            <label for="login_id" class="form-label" id="login-label">Admission No</label>
            <div class="input-wrap">
                <span class="fi" id="login-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                </span>
                <input
                    type="text"
                    id="login_id"
                    name="login_id"
                    class="@error('login_id') is-invalid @enderror"
                    value="{{ old('login_id') }}"
                    placeholder="Enter Admission No"
                    data-rules="required"
                     autofocus>
                @error('login_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrap">
                <span class="fi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="@error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    data-rules="required|min:6"
                    >
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-portal">
            <span class="btn-text">Login to Dashboard</span>
        </button>

    </form>

    <p class="text-center mt-3 mb-0" style="font-size:11px;color:#d1d5db;">
        Secured Portal &middot; All rights reserved
    </p>

</div>
</div>

{{-- jQuery first → Bootstrap → Global JS → Page-specific JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/resources/views/layouts/js.blade.php"></script>

<script>
/* ── Role icon SVGs (page-specific, not in global JS) ────────── */
const roleIcons = {
    student:     `<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>`,
    parent:      `<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.4 10.8a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 16a16 16 0 006.09 6.09l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>`,
    teacher:     `<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>`,
    admin:       `<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>`,
    super_admin: `<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>`,
};

const rolePlaceholders = {
    student:     'Enter Admission No',
    parent:      'Enter Phone Number',
    teacher:     'Enter Teacher ID',
    admin:       'Enter Official Email',
    super_admin: 'Enter Master Security Email',
};

function updateUI(role, label, rulesOverride) {
    $('#selected_role').val(role);
    $('#login-label').text(label);
    $('#login_id')
        .attr('placeholder', rolePlaceholders[role] || 'Enter ' + label)
        .attr('data-rules', rulesOverride || 'required')
        .removeClass('is-invalid')
        .siblings('.invalid-feedback').remove();

    $('#login-icon svg').html(roleIcons[role] || roleIcons.student);

    $('.role-tab').removeClass('active');
    $('#btn-' + role).addClass('active');
}

/* ── Tab click ── */
$('#role-selector').on('click', '.role-tab', function () {
    updateUI(
        $(this).data('role'),
        $(this).data('label'),
        $(this).data('rules')
    );
});

/* ── 5-click super admin easter egg ── */
let clickCount = 0;
$('#main-title').on('click', function () {
    clickCount++;
    if (clickCount === 5) {
        updateUI('super_admin', 'Master Security Email', 'required|email');
        alert('Super Admin Mode Activated');
    }
});
</script>

</x-guest-layout>