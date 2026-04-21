@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

{{-- Hero Section --}}
<section style="min-height:calc(100vh - 64px); display:flex; align-items:center; justify-content:center; padding:3rem 1.5rem; position:relative; overflow:hidden;">

    {{-- Background glow blobs --}}
    <div style="position:absolute; top:10%; left:20%; width:400px; height:400px; background:radial-gradient(circle,#00d4ff15,transparent 70%); pointer-events:none;"></div>
    <div style="position:absolute; bottom:10%; right:20%; width:300px; height:300px; background:radial-gradient(circle,#7c3aed15,transparent 70%); pointer-events:none;"></div>

    <div style="max-width:700px; text-align:center; position:relative; z-index:1;">

        {{-- Badge --}}
        <div style="display:inline-flex; align-items:center; gap:0.5rem; background:#00d4ff11; border:1px solid #00d4ff33; color:#00d4ff; padding:0.3rem 1rem; border-radius:999px; font-size:0.8rem; font-weight:600; margin-bottom:2rem; letter-spacing:0.05em;">
            <span style="width:6px;height:6px;background:#00d4ff;border-radius:50%;display:inline-block;"></span>
            TASK MANAGEMENT SYSTEM
        </div>

        {{-- Headline --}}
        <h1 style="font-size:clamp(2.5rem,6vw,4rem); font-weight:800; line-height:1.1; margin-bottom:1.5rem; color:#f1f5f9;">
            Manage Tasks
            <span style="display:block; background:linear-gradient(135deg,#00d4ff,#7c3aed); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">
                With Precision
            </span>
        </h1>

        {{-- Subtitle --}}
        <p style="font-size:1.1rem; color:#8b9ab0; max-width:520px; margin:0 auto 2.5rem; line-height:1.7;">
            A role-based task manager for teams. Admins assign work, users track progress — all in real-time with a clean modern interface.
        </p>

        {{-- CTA Buttons --}}
        @guest
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="{{ url('/register') }}" class="btn-primary" style="text-decoration:none; font-size:1rem; padding:0.75rem 2rem;">
                Get Started Free
            </a>
            <a href="{{ url('/login') }}" class="btn-secondary" style="text-decoration:none; font-size:1rem; padding:0.75rem 2rem;">
                Sign In
            </a>
        </div>
        @endguest
        @auth
        <a href="{{ url('/dashboard') }}" class="btn-primary" style="text-decoration:none; font-size:1rem; padding:0.75rem 2rem;">
            Go to Dashboard →
        </a>
        @endauth

        {{-- Feature pills --}}
        <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap; margin-top:3rem;">
            @foreach(['Role-Based Access', 'AJAX Operations', 'Real-Time Updates', 'Admin Dashboard'] as $feat)
            <span style="background:#0d1117; border:1px solid #1e2433; color:#8b9ab0; font-size:0.8rem; padding:0.35rem 0.85rem; border-radius:999px;">
                {{ $feat }}
            </span>
            @endforeach
        </div>

    </div>
</section>

@endsection
