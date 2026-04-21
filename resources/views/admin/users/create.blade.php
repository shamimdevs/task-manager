@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-300 transition-colors no-underline mb-3">
            ← Back to Users
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-100">Create New User</h1>
    </div>

    <div class="glass-card p-4 sm:p-6">
        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-400 space-y-1 pl-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col gap-5">

                {{-- Profile Image --}}
                <div class="flex flex-col items-center gap-3">
                    <div id="avatarPreview"
                        class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold text-white overflow-hidden flex-shrink-0 border-2 border-slate-700"
                        style="background:linear-gradient(135deg,#00d4ff,#7c3aed);">
                        <span id="avatarInitial">?</span>
                        <img id="avatarImg" src="" alt="" class="hidden w-full h-full object-cover">
                    </div>
                    <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs text-cyan-400 border border-cyan-400/30 px-3 py-1.5 rounded-lg bg-cyan-400/6 hover:bg-cyan-400/12 transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        Upload Photo
                        <input type="file" name="profile_image" id="profileImageInput"
                            accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden"
                            onchange="previewImage(this)">
                    </label>
                    <p class="text-xs text-slate-500">JPG, PNG, WebP — max 2MB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-dark w-full"
                        placeholder="John Doe" oninput="updateInitial(this.value)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input-dark w-full"
                        placeholder="john@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="input-dark w-full"
                        placeholder="e.g. Software Engineer">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="input-dark w-full pr-10"
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
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="input-dark w-full pr-10" placeholder="Repeat password">
                        <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 bg-transparent border-none cursor-pointer p-0 flex items-center">
                            <svg id="eye2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Role + Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Role</label>
                        <select name="role" class="input-dark w-full cursor-pointer">
                            <option value="user" {{ old('role','user') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Status</label>
                        <div class="flex items-center gap-3 py-2.5">
                            <input type="hidden" name="status" id="statusVal" value="{{ old('status','active') }}">
                            <button type="button" id="statusToggle" onclick="toggleStatus()"
                                class="relative w-12 h-6 rounded-full border-none cursor-pointer transition-colors duration-200 flex-shrink-0"
                                style="background:{{ old('status','active') === 'active' ? '#22c55e' : '#4b5563' }};">
                                <span id="statusKnob"
                                    class="absolute top-0.5 w-5 h-5 rounded-full bg-white transition-all duration-200"
                                    style="left:{{ old('status','active') === 'active' ? '25px' : '3px' }};"></span>
                            </button>
                            <span id="statusLabel" class="text-sm font-medium"
                                style="color:{{ old('status','active') === 'active' ? '#22c55e' : '#8b9ab0' }};">
                                {{ old('status','active') === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-1">Create User</button>
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

function toggleStatus() {
    const val = document.getElementById('statusVal');
    const knob = document.getElementById('statusKnob');
    const toggle = document.getElementById('statusToggle');
    const label = document.getElementById('statusLabel');
    const isActive = val.value === 'active';
    val.value = isActive ? 'inactive' : 'active';
    toggle.style.background = isActive ? '#4b5563' : '#22c55e';
    knob.style.left = isActive ? '3px' : '25px';
    label.textContent = isActive ? 'Inactive' : 'Active';
    label.style.color = isActive ? '#8b9ab0' : '#22c55e';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatarImg').src = e.target.result;
            document.getElementById('avatarImg').classList.remove('hidden');
            document.getElementById('avatarInitial').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateInitial(name) {
    const img = document.getElementById('avatarImg');
    if (img.classList.contains('hidden')) {
        document.getElementById('avatarInitial').textContent = name ? name.charAt(0).toUpperCase() : '?';
    }
}
</script>
@endpush
@endsection
