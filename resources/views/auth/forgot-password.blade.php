@extends('layouts.app')
@section('title', 'Forgot Password')

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
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h1 style="font-size:1.6rem; font-weight:700; color:#f1f5f9; margin:0 0 0.4rem;">Forgot Password?</h1>
                <p style="color:#8b9ab0; font-size:0.9rem; margin:0;">Enter your email and we'll send you a reset link.</p>
            </div>

            {{-- Success --}}
            @if(session('success'))
            <div style="background:#00d4ff11; border:1px solid #00d4ff33; color:#00d4ff; padding:0.75rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
            <div style="background:#ff444411; border:1px solid #ff444433; color:#ff6b6b; padding:0.75rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.25rem;">
                {{ $errors->first() }}
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.forgot.send') }}">
                @csrf
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-size:0.85rem; font-weight:500; color:#cbd5e1; margin-bottom:0.4rem;">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                           class="input-dark" required autofocus>
                </div>

                <button type="submit" class="btn-primary" style="width:100%; padding:0.75rem; font-size:0.95rem;">
                    Send Reset Link
                </button>
            </form>

            <p style="text-align:center; margin-top:1.5rem; font-size:0.875rem; color:#8b9ab0;">
                Remember your password?
                <a href="{{ route('login') }}" style="color:#00d4ff; text-decoration:none; font-weight:500;">Sign in</a>
            </p>

        </div>
    </div>
</div>
@endsection
