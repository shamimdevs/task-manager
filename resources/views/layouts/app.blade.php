<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <meta name="theme-color" content="#00d4ff">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color:#060810; color:#e2e8f0; font-family:'Inter',sans-serif;">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 border-b border-slate-800/80 backdrop-blur-xl" style="background:rgba(13,17,23,0.92);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2 no-underline flex-shrink-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#00d4ff,#7c3aed)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </div>
                    <span class="font-bold text-slate-100 text-base hidden xs:block sm:block">{{ config('app.name') }}</span>
                </a>

                {{-- Desktop nav --}}
                @auth
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ url('/dashboard') }}"
                       class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Dashboard</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.tasks.index') }}"
                           class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Tasks</a>
                        <a href="{{ route('admin.users.index') }}"
                           class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Users</a>
                        <a href="{{ route('admin.report') }}"
                           class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Report</a>
                    @else
                        <a href="{{ route('my-tasks.index') }}"
                           class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">My Tasks</a>
                        <a href="{{ route('report') }}"
                           class="px-3 py-2 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Report</a>
                    @endif
                </div>

                {{-- Desktop profile dropdown --}}
                <div class="hidden md:flex items-center gap-3">
                    <div class="relative" id="profileMenu">
                        <button onclick="document.getElementById('profileDropdown').classList.toggle('hidden')"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-white/5 transition-all border-none cursor-pointer"
                                style="background:none; color:#e2e8f0;">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0 overflow-hidden"
                                 style="background:linear-gradient(135deg,#00d4ff,#7c3aed)">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ Storage::url(auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="text-sm max-w-24 truncate">{{ auth()->user()->name }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div id="profileDropdown" class="hidden absolute right-0 top-full mt-2 w-44 rounded-xl border border-slate-800 shadow-2xl z-50 overflow-hidden"
                             style="background:#0d1117;">
                            <a href="{{ route('profile') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                My Profile
                            </a>
                            <div class="h-px bg-slate-800"></div>
                            <form method="POST" action="{{ url('/logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-400 hover:bg-red-400/8 transition-all text-left cursor-pointer border-none"
                                        style="background:none;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Mobile right section (auth users) --}}
                @auth
                <div class="flex md:hidden items-center gap-2">
                    {{-- Mobile profile avatar --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs overflow-hidden flex-shrink-0"
                         style="background:linear-gradient(135deg,#00d4ff,#7c3aed)">
                        @if(auth()->user()->profile_image)
                            <img src="{{ Storage::url(auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>

                    {{-- Hamburger --}}
                    <button onclick="toggleMobileMenu()" id="hamburger"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all border-none cursor-pointer"
                            style="background:none;">
                        <svg id="hamburgerIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                        <svg id="closeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                @endauth

                {{-- Guest: desktop links + mobile hamburger --}}
                @guest
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ url('/login') }}" class="text-sm text-slate-400 hover:text-slate-100 no-underline transition-colors">Login</a>
                    <a href="{{ url('/register') }}" class="btn-primary text-sm no-underline">Get Started</a>
                </div>
                <button onclick="toggleMobileMenu()" id="hamburger"
                        class="md:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all border-none cursor-pointer"
                        style="background:none;">
                    <svg id="hamburgerIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg id="closeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
                @endguest

            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-800/80" style="background:rgba(13,17,23,0.97);">
            <div class="px-4 py-3 space-y-1">
                @auth
                    {{-- User info --}}
                    <div class="flex items-center gap-3 px-3 py-3 mb-2 rounded-xl bg-white/[0.03] border border-slate-800">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden flex-shrink-0"
                             style="background:linear-gradient(135deg,#00d4ff,#7c3aed)">
                            @if(auth()->user()->profile_image)
                                <img src="{{ Storage::url(auth()->user()->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-100 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <a href="{{ url('/dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.tasks.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2"/></svg>
                            Tasks
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/></svg>
                            Users
                        </a>
                        <a href="{{ route('admin.report') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                            Report
                        </a>
                    @else
                        <a href="{{ route('my-tasks.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2"/><polyline points="9 11 12 14 15 11"/></svg>
                            My Tasks
                        </a>
                        <a href="{{ route('report') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                            Report
                        </a>
                    @endif

                    <div class="h-px bg-slate-800 my-1"></div>

                    <a href="{{ route('profile') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        My Profile
                    </a>

                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-400/8 transition-all text-left cursor-pointer border-none"
                                style="background:none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                            Logout
                        </button>
                    </form>

                @else
                    <a href="{{ url('/login') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-slate-100 hover:bg-white/5 transition-all no-underline">Login</a>
                    <a href="{{ url('/register') }}"
                       class="flex items-center justify-center px-3 py-2.5 rounded-lg text-sm font-semibold no-underline"
                       style="background:linear-gradient(135deg,#00d4ff,#7c3aed); color:#fff;">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Global Loading Overlay --}}
    <div id="loadingOverlay"
         style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(6,8,16,0.75); backdrop-filter:blur(6px);
                align-items:center; justify-content:center; flex-direction:column; gap:1.25rem;">
        {{-- Spinner --}}
        <div style="position:relative; width:56px; height:56px;">
            <svg style="width:56px; height:56px; animation:spin 1s linear infinite;" viewBox="0 0 56 56" fill="none">
                <circle cx="28" cy="28" r="24" stroke="#1e2433" stroke-width="4"/>
                <path d="M28 4 a24 24 0 0 1 24 24" stroke="url(#lg)" stroke-width="4" stroke-linecap="round"/>
                <defs>
                    <linearGradient id="lg" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#00d4ff"/>
                        <stop offset="100%" stop-color="#7c3aed"/>
                    </linearGradient>
                </defs>
            </svg>
            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="url(#lg2)" stroke-width="2.5">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    <defs>
                        <linearGradient id="lg2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#00d4ff"/>
                            <stop offset="100%" stop-color="#7c3aed"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>
        {{-- Text --}}
        <div style="text-align:center;">
            <p id="loadingText" style="color:#e2e8f0; font-size:0.95rem; font-weight:600; margin:0 0 0.3rem;">Processing…</p>
            <p style="color:#8b9ab0; font-size:0.78rem; margin:0;">Please wait a moment</p>
        </div>
    </div>

    <style>
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Flash messages --}}
    @if(session('error'))
        <div id="flash-msg" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-sm px-4 py-3 rounded-xl border text-sm z-50 backdrop-blur-xl"
             style="background:rgba(239,68,68,0.12); border-color:rgba(239,68,68,0.3); color:#ff6b6b;">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        <script>setTimeout(()=>{const el=document.getElementById('flash-msg');if(el)el.remove();},3500);</script>
    @endif

    @if(session('success'))
        <div id="flash-msg" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-sm px-4 py-3 rounded-xl border text-sm z-50 backdrop-blur-xl"
             style="background:rgba(0,212,255,0.1); border-color:rgba(0,212,255,0.3); color:#00d4ff;">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <script>setTimeout(()=>{const el=document.getElementById('flash-msg');if(el)el.remove();},3500);</script>
    @endif

    <script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const hi   = document.getElementById('hamburgerIcon');
        const ci   = document.getElementById('closeIcon');
        menu.classList.toggle('hidden');
        hi.classList.toggle('hidden');
        ci.classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const dd = document.getElementById('profileDropdown');
        const pm = document.getElementById('profileMenu');
        if (dd && pm && !pm.contains(e.target)) dd.classList.add('hidden');
    });

    // ── Global Form Loading Overlay ──
    (function () {
        const overlay  = document.getElementById('loadingOverlay');
        const loadText = document.getElementById('loadingText');

        function showLoader(msg) {
            loadText.textContent = msg || 'Processing…';
            overlay.style.display = 'flex';
        }

        // Detect form context for custom messages
        function getMessage(form) {
            const action  = (form.action  || '').toLowerCase();
            const method  = (form.querySelector('[name="_method"]')?.value || form.method || '').toLowerCase();
            const btnText = (form.querySelector('[type="submit"]')?.textContent || '').trim().toLowerCase();

            if (action.includes('login'))    return 'Signing in…';
            if (action.includes('register')) return 'Creating account…';
            if (action.includes('logout'))   return 'Signing out…';
            if (action.includes('password')) return 'Sending reset link…';
            if (method === 'delete')         return 'Deleting…';
            if (btnText.includes('update') || method === 'put' || method === 'patch') return 'Saving changes…';
            if (btnText.includes('create') || action.includes('store')) return 'Creating…';
            return 'Processing…';
        }

        document.addEventListener('submit', function (e) {
            const form = e.target;

            // If form has onsubmit confirm() and it was already cancelled, do nothing
            // We hook AFTER the native confirm ran (event reaches here only if not prevented)
            if (e.defaultPrevented) return;

            // Don't show loader for search/filter forms (GET method, no _method spoofing)
            if (form.method.toLowerCase() === 'get') return;

            showLoader(getMessage(form));
        }, true); // capture phase so it fires before inline onsubmit

        // Hide loader on browser back (page shown from cache)
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) overlay.style.display = 'none';
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
