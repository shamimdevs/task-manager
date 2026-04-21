@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <h1 class="text-xl sm:text-2xl font-bold text-slate-100 mb-6">My Profile</h1>

    {{-- Profile card --}}
    <div class="glass-card p-4 sm:p-6 mb-4">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full flex items-center justify-center text-2xl sm:text-3xl font-bold text-white flex-shrink-0 overflow-hidden border-2 border-slate-700"
                style="background:linear-gradient(135deg,#00d4ff,#7c3aed);">
                @if($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-base sm:text-lg font-bold text-slate-100 truncate">{{ $user->name }}</h2>
                @if($user->designation)
                    <p class="text-cyan-400 text-sm font-medium mt-0.5">{{ $user->designation }}</p>
                @endif
                <p class="text-slate-500 text-sm truncate">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    @if($user->isAdmin())
                        <span class="text-xs font-semibold text-violet-400 bg-violet-400/15 border border-violet-400/30 px-2.5 py-0.5 rounded-full">Admin</span>
                    @else
                        <span class="text-xs font-semibold text-slate-400 bg-slate-400/10 border border-slate-400/20 px-2.5 py-0.5 rounded-full">User</span>
                    @endif
                    @if($user->isActive())
                        <span class="text-xs font-semibold text-emerald-400 bg-emerald-400/12 border border-emerald-400/25 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Active
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right text-slate-500 text-xs flex-shrink-0">
                <p>Member since</p>
                <p class="text-slate-200 font-medium mt-0.5">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="glass-card p-4 sm:p-6">
        <h3 class="text-sm font-semibold text-slate-200 mb-5">Edit Profile</h3>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-5">
            <ul class="text-sm text-red-400 space-y-1 pl-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="flex flex-col gap-5">

                {{-- Photo upload --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold text-white flex-shrink-0 overflow-hidden border-2 border-slate-700"
                        style="background:linear-gradient(135deg,#00d4ff,#7c3aed);">
                        @if($user->profile_image)
                            <img id="avatarImg" src="{{ Storage::url($user->profile_image) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <img id="avatarImg" src="" alt="" class="hidden w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs text-cyan-400 border border-cyan-400/30 px-3 py-1.5 rounded-lg bg-cyan-400/6 hover:bg-cyan-400/12 transition-all">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            Change Photo
                            <input type="file" name="profile_image" accept="image/jpg,image/jpeg,image/png,image/webp"
                                class="hidden" onchange="previewImage(this)">
                        </label>
                        <p class="text-xs text-slate-500 mt-1.5">JPG, PNG, WebP — max 2MB</p>
                    </div>
                </div>

                {{-- Name + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-dark w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-dark w-full">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}"
                        class="input-dark w-full" placeholder="e.g. Software Engineer">
                </div>

                <div class="border-t border-slate-800 pt-5">
                    <h4 class="text-sm font-semibold text-slate-200 mb-1">Change Password</h4>
                    <p class="text-xs text-slate-500 mb-4">Leave blank to keep your current password.</p>

                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1.5">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" class="input-dark w-full pr-10"
                                    placeholder="Enter current password">
                                <button type="button" onclick="togglePwd('current_password','eye0')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 bg-transparent border-none cursor-pointer p-0 flex items-center">
                                    <svg id="eye0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1.5">New Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" class="input-dark w-full pr-10"
                                        placeholder="Min. 6 characters">
                                    <button type="button" onclick="togglePwd('password','eye1')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 bg-transparent border-none cursor-pointer p-0 flex items-center">
                                        <svg id="eye1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1.5">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="input-dark w-full pr-10" placeholder="Repeat new password">
                                    <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 bg-transparent border-none cursor-pointer p-0 flex items-center">
                                        <svg id="eye2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-1">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    document.getElementById(iconId).style.opacity = isHidden ? '0.5' : '1';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatarImg');
            img.src = e.target.result;
            img.classList.remove('hidden');
            const initial = document.getElementById('avatarInitial');
            if (initial) initial.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
