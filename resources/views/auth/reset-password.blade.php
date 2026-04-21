@extends('layouts.app')
@section('title', 'Reset Password')

@section('content')
<div style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">

    <div style="position:fixed; top:20%; left:15%; width:350px; height:350px; background:radial-gradient(circle,#00d4ff0d,transparent 70%); pointer-events:none;"></div>
    <div style="position:fixed; bottom:20%; right:15%; width:300px; height:300px; background:radial-gradient(circle,#7c3aed0d,transparent 70%); pointer-events:none;"></div>

    <div style="width:100%; max-width:420px;">
        <div class="glass-card" style="padding:2.5rem;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:2rem;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#00d4ff,#7c3aed); border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <h1 style="font-size:1.6rem; font-weight:700; color:#f1f5f9; margin:0 0 0.4rem;">Set New Password</h1>
                <p style="color:#8b9ab0; font-size:0.9rem; margin:0;">Choose a strong password for your account.</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
            <div style="background:#ff444411; border:1px solid #ff444433; color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.25rem;">
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('error'))
            <div style="background:#ff444411; border:1px solid #ff444433; color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.25rem;">
                {{ session('error') }}
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block; font-size:0.85rem; font-weight:500; color:#cbd5e1; margin-bottom:0.4rem;">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pass1" placeholder="••••••••"
                               class="input-dark" required style="padding-right:2.75rem;">
                        <button type="button" onclick="togglePassword('pass1','eye1')"
                                style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#8b9ab0; padding:0; display:flex; align-items:center;" tabindex="-1">
                            <svg id="eye1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <p style="margin:0.35rem 0 0; font-size:0.78rem; color:#6b7280;">Minimum 6 characters</p>
                </div>

                <div style="margin-bottom:1.75rem;">
                    <label style="display:block; font-size:0.85rem; font-weight:500; color:#cbd5e1; margin-bottom:0.4rem;">Confirm New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="pass2" placeholder="••••••••"
                               class="input-dark" required style="padding-right:2.75rem;">
                        <button type="button" onclick="togglePassword('pass2','eye2')"
                                style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#8b9ab0; padding:0; display:flex; align-items:center;" tabindex="-1">
                            <svg id="eye2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%; padding:0.75rem; font-size:0.95rem;">
                    Reset Password
                </button>
            </form>

            <p style="text-align:center; margin-top:1.5rem; font-size:0.875rem; color:#8b9ab0;">
                <a href="{{ route('login') }}" style="color:#00d4ff; text-decoration:none; font-weight:500;">← Back to Sign In</a>
            </p>

        </div>
    </div>
</div>
@push('scripts')
<script>
function togglePassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    eye.innerHTML = isHidden
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}
</script>
@endpush
@endsection
