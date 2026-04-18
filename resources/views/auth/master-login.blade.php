<x-guest-layout>

{{-- ── Dependencies ─────────────────────────────────────────── --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body { font-family: 'DM Sans', sans-serif; }

.sa-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 2.25rem 2rem 1.75rem;
    width: 100%;
    max-width: 400px;
    margin: auto;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
}

/* ── Authority badge ── */
.sa-badge {
    width: 52px; height: 52px;
    background: #111827;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    transform: rotate(-3deg);
}
.sa-badge svg { width: 26px; height: 26px; stroke: #fff; fill: none; stroke-width: 2; }

/* ── Inputs ── */
.input-icon-wrap { position: relative; }
.input-icon-wrap .fi {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #9ca3af; pointer-events: none; display: flex;
}
.input-icon-wrap .fi svg { width: 15px; height: 15px; }
.input-icon-wrap input {
    padding-left: 42px !important;
    height: 46px;
    border-radius: 11px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    font-family: inherit; font-size: 14px; color: #111827;
    transition: border-color .2s, box-shadow .2s;
}
.input-icon-wrap input:focus {
    border-color: #111827;
    box-shadow: 0 0 0 3px rgba(17,24,39,.1);
    background: #fff;
    outline: none;
}
.input-icon-wrap input.is-invalid { border-color: #ef4444 !important; }
.form-label { font-size: 13px; font-weight: 500; color: #6b7280; }

/* ── Submit button ── */
.btn-authority {
    width: 100%; height: 50px;
    background: #111827; color: #fff;
    border: none; border-radius: 12px;
    font-family: inherit; font-size: 14px; font-weight: 600;
    letter-spacing: .03em;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .2s, transform .1s;
}
.btn-authority:hover { background: #000; color: #fff; }
.btn-authority:active { transform: scale(.98); }
.btn-authority svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2.5; transition: transform .2s; }
.btn-authority:hover svg { transform: translateX(3px); }

/* ── Loader spinner inside button ── */
.btn-authority .spinner-border { width: 1rem; height: 1rem; border-width: 2px; }

/* ── Dark mode ── */
/* @media (prefers-color-scheme: dark) {
    .sa-card { background: #111827; border-color: #1f2937; }
    .input-icon-wrap input { background: #1f2937; border-color: #374151; color: #f9fafb; }
    .input-icon-wrap input:focus { background: #111827; border-color: #6b7280; }
    .form-label { color: #9ca3af; }
} */
</style>

{{-- ── Markup ───────────────────────────────────────────────── --}}
<div class="">
<div class="sa-card">

    {{-- Badge --}}
    <div class="sa-badge">
        <svg viewBox="0 0 24 24">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
    </div>

    <h2 class="text-center fw-bold mb-1" style="font-size:19px; letter-spacing:-.3px; color:#111827;">
        System Authority
    </h2>
    <p class="text-center mb-4" style="font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#9ca3af; font-weight:600;">
        Encrypted Master Access
    </p>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="ajax-form" data-reload="1" data-reset="0">
        @csrf
        <input type="hidden" name="role" value="super_admin">

        {{-- Master Phone --}}
        <div class="mb-3">
            <label for="login_id" class="form-label">Master Phone Number</label>
            <div class="input-icon-wrap">
                <span class="fi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.4 10.8a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 16a16 16 0 006.09 6.09l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    id="login_id"
                    name="login_id"
                    class="form-control @error('login_id') is-invalid @enderror"
                    value="{{ old('login_id') }}"
                    placeholder="0000000000"
                    data-rules="required|numeric|min:10"
                    autofocus>
                @error('login_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Security Key --}}
        <div class="mb-3">
            <label for="password" class="form-label">Security Key</label>
            <div class="input-icon-wrap">
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
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••••••"
                    data-rules="required|min:8"
                    >
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Remember --}}
        <div class="d-flex align-items-center mb-4 gap-2">
            <input
                type="checkbox"
                id="remember_me"
                name="remember"
                class="form-check-input m-0"
                style="width:15px;height:15px;accent-color:#111827;">
            <label for="remember_me" class="mb-0" style="font-size:12px;color:#6b7280;">
                Maintain Session
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-authority">
            <span class="btn-text">AUTHORIZE ACCESS</span>
            <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>

    </form>

    <p class="text-center mt-3 mb-0" style="font-size:11px;color:#d1d5db;">
        Secured &middot; Encrypted &middot; Monitored
    </p>

</div>
</div>

{{-- ── Scripts ──────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="/resources/views/layouts/js.blade.php"></script>

</x-guest-layout>