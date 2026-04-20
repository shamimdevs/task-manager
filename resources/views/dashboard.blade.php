@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width:1280px; margin:0 auto; padding:2.5rem 1.5rem;">

    {{-- Page header --}}
    <div style="margin-bottom:2rem;">
        <h1 style="font-size:1.75rem; font-weight:700; color:#f1f5f9; margin:0 0 0.4rem;">
            @if($user->isAdmin())
                Admin Dashboard
            @else
                My Dashboard
            @endif
        </h1>
        <p style="color:#8b9ab0; font-size:0.95rem; margin:0;">
            Welcome back, <span style="color:#00d4ff; font-weight:500;">{{ $user->name }}</span>
            —
            <span style="background:{{ $user->isAdmin() ? '#7c3aed22' : '#00d4ff11' }}; border:1px solid {{ $user->isAdmin() ? '#7c3aed55' : '#00d4ff33' }}; color:{{ $user->isAdmin() ? '#a78bfa' : '#00d4ff' }}; font-size:0.75rem; font-weight:600; padding:0.2rem 0.6rem; border-radius:999px; letter-spacing:0.05em;">
                {{ strtoupper($user->role) }}
            </span>
        </p>
    </div>

    {{-- Stats grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1.25rem; margin-bottom:2.5rem;">

        @php
        $stats = $user->isAdmin()
            ? [
                ['label' => 'Total Tasks',    'value' => '0', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0-1 2', 'color' => '#00d4ff'],
                ['label' => 'In Progress',    'value' => '0', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => '#f59e0b'],
                ['label' => 'Completed',      'value' => '0', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => '#10b981'],
                ['label' => 'Total Users',    'value' => '0', 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75', 'color' => '#7c3aed'],
              ]
            : [
                ['label' => 'My Tasks',       'value' => '0', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0-1 2', 'color' => '#00d4ff'],
                ['label' => 'In Progress',    'value' => '0', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => '#f59e0b'],
                ['label' => 'Completed',      'value' => '0', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => '#10b981'],
                ['label' => 'Pending',        'value' => '0', 'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', 'color' => '#ef4444'],
              ];
        @endphp

        @foreach($stats as $stat)
        <div class="glass-card" style="padding:1.5rem; display:flex; align-items:center; gap:1rem; transition:transform 0.2s, border-color 0.2s; cursor:default;"
             onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='{{ $stat['color'] }}44';"
             onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#1e2433';">
            <div style="width:44px; height:44px; background:{{ $stat['color'] }}15; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $stat['color'] }}" stroke-width="1.8">
                    <path d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div style="font-size:1.6rem; font-weight:700; color:#f1f5f9; line-height:1;">{{ $stat['value'] }}</div>
                <div style="font-size:0.8rem; color:#8b9ab0; margin-top:0.2rem;">{{ $stat['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Coming soon placeholder --}}
    <div class="glass-card" style="padding:3rem; text-align:center;">
        <div style="width:56px; height:56px; background:linear-gradient(135deg,#00d4ff15,#7c3aed15); border:1px solid #1e2433; border-radius:14px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8b9ab0" stroke-width="1.8">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0-1 2"/>
            </svg>
        </div>
        <h3 style="color:#cbd5e1; font-size:1.1rem; font-weight:600; margin:0 0 0.5rem;">Tasks coming in Phase 3</h3>
        <p style="color:#8b9ab0; font-size:0.875rem; margin:0;">Task management features will be built in the next phase.</p>
    </div>

</div>
@endsection
