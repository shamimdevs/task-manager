<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color:#060810; color:#e2e8f0; font-family:'Inter',sans-serif; min-height:100vh;">

    {{-- Navbar --}}
    <nav style="background:rgba(13,17,23,0.9); border-bottom:1px solid #1e2433; backdrop-filter:blur(12px); position:sticky; top:0; z-index:50;">
        <div style="max-width:1280px; margin:0 auto; padding:0 1.5rem; display:flex; align-items:center; justify-content:space-between; height:64px;">

            {{-- Logo --}}
            <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:0.5rem; text-decoration:none;">
                <div style="width:32px; height:32px; background:linear-gradient(135deg,#00d4ff,#7c3aed); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <span style="font-weight:700; font-size:1.1rem; color:#e2e8f0;">
                    {{ config('app.name') }}
                </span>
            </a>

            {{-- Nav links --}}
            <div style="display:flex; align-items:center; gap:1rem;">
                @auth
                    <a href="{{ url('/dashboard') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">
                        Dashboard
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.tasks.index') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">Tasks</a>
                    <a href="{{ route('admin.users.index') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">Users</a>
                    <a href="{{ route('admin.report') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">Report</a>
                    @else
                    <a href="{{ route('my-tasks.index') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">My Tasks</a>
                    <a href="{{ route('report') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem; transition:color 0.2s;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">Report</a>
                    @endif

                    {{-- Profile dropdown --}}
                    <div style="position:relative;" id="profileMenu">
                        <button onclick="document.getElementById('profileDropdown').classList.toggle('hidden')"
                                style="display:flex; align-items:center; gap:0.5rem; background:none; border:none; cursor:pointer; color:#e2e8f0; font-size:0.9rem; padding:0.25rem 0.5rem; border-radius:8px; transition:background 0.2s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.05)'"
                                onmouseout="this.style.background='none'">
                            <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#00d4ff,#7c3aed); display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; color:#fff; flex-shrink:0; overflow:hidden;">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ Storage::url(auth()->user()->profile_image) }}" alt="" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <span style="max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>

                        <div id="profileDropdown" class="hidden" style="position:absolute; right:0; top:calc(100% + 0.5rem); background:#0d1117; border:1px solid #1e2433; border-radius:10px; min-width:160px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4); z-index:100;">
                            <a href="{{ route('profile') }}" style="display:block; padding:0.65rem 1rem; color:#8b9ab0; text-decoration:none; font-size:0.875rem; transition:all 0.15s;"
                               onmouseover="this.style.background='rgba(255,255,255,0.04)'; this.style.color='#e2e8f0'"
                               onmouseout="this.style.background='transparent'; this.style.color='#8b9ab0'">
                                My Profile
                            </a>
                            <div style="height:1px; background:#1e2433;"></div>
                            <form method="POST" action="{{ url('/logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="display:block; width:100%; padding:0.65rem 1rem; color:#ef4444; background:none; border:none; text-align:left; font-size:0.875rem; cursor:pointer; transition:all 0.15s;"
                                        onmouseover="this.style.background='rgba(239,68,68,0.08)'"
                                        onmouseout="this.style.background='transparent'">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('click', function(e) {
                            const menu = document.getElementById('profileMenu');
                            if (menu && !menu.contains(e.target)) {
                                document.getElementById('profileDropdown').classList.add('hidden');
                            }
                        });
                    </script>
                @else
                    <a href="{{ url('/login') }}" style="color:#8b9ab0; text-decoration:none; font-size:0.9rem;"
                       onmouseover="this.style.color='#00d4ff'" onmouseout="this.style.color='#8b9ab0'">
                        Login
                    </a>
                    <a href="{{ url('/register') }}" class="btn-primary" style="font-size:0.85rem; text-decoration:none; display:inline-block;">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Flash messages --}}
    @if(session('error'))
        <div id="flash-msg" style="position:fixed; bottom:1.5rem; right:1.5rem; background:linear-gradient(135deg,#ff444422,#ff444411); border:1px solid #ff444444; color:#ff6b6b; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; z-index:999; backdrop-filter:blur(12px);">
            {{ session('error') }}
        </div>
        <script>setTimeout(()=>{const el=document.getElementById('flash-msg');if(el)el.remove();},3500);</script>
    @endif

    @if(session('success'))
        <div id="flash-msg" style="position:fixed; bottom:1.5rem; right:1.5rem; background:linear-gradient(135deg,#00d4ff22,#7c3aed22); border:1px solid #00d4ff44; color:#00d4ff; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; z-index:999; backdrop-filter:blur(12px);">
            {{ session('success') }}
        </div>
        <script>setTimeout(()=>{const el=document.getElementById('flash-msg');if(el)el.remove();},3500);</script>
    @endif

    @stack('scripts')
</body>
</html>
