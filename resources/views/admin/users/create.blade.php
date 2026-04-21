@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div style="max-width:600px; margin:0 auto; padding:2rem 1.5rem;">

    <div style="margin-bottom:2rem;">
        <a href="{{ route('admin.users.index') }}"
            style="color:#8b9ab0; text-decoration:none; font-size:0.875rem; display:inline-flex; align-items:center; gap:0.4rem; margin-bottom:1rem;">
            ← Back to Users
        </a>
        <h1 style="font-size:1.75rem; font-weight:700; color:#e2e8f0; margin:0;">Create New User</h1>
    </div>

    <div class="glass-card">
        @if($errors->any())
        <div
            style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
            <ul style="margin:0; padding-left:1.25rem; color:#f87171; font-size:0.875rem;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; flex-direction:column; gap:1.25rem;" class="p-4">

                {{-- Profile Image --}}
                <div style="display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
                    <div id="avatarPreview"
                        style="width:88px; height:88px; border-radius:50%; background:linear-gradient(135deg,#00d4ff,#7c3aed); display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; color:#fff; overflow:hidden; border:2px solid #1e2433; flex-shrink:0;">
                        <span id="avatarInitial">?</span>
                        <img id="avatarImg" src="" alt=""
                            style="display:none; width:100%; height:100%; object-fit:cover;">
                    </div>
                    <label
                        style="cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem; font-size:0.8rem; color:#00d4ff; border:1px solid rgba(0,212,255,0.3); padding:0.4rem 0.9rem; border-radius:6px; background:rgba(0,212,255,0.06); transition:all 0.2s;"
                        onmouseover="this.style.background='rgba(0,212,255,0.12)'"
                        onmouseout="this.style.background='rgba(0,212,255,0.06)'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                        Upload Photo
                        <input type="file" name="profile_image" id="profileImageInput"
                            accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;"
                            onchange="previewImage(this)">
                    </label>
                    <p style="color:#8b9ab0; font-size:0.75rem; margin:0;">JPG, PNG, WebP — max 2MB</p>
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Full
                        Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-dark"
                        style="width:100%; box-sizing:border-box;" placeholder="John Doe"
                        oninput="updateInitial(this.value)">
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Email
                        Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input-dark"
                        style="width:100%; box-sizing:border-box;" placeholder="john@example.com">
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" class="input-dark"
                        style="width:100%; box-sizing:border-box;" placeholder="e.g. Software Engineer">
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" required class="input-dark"
                            style="width:100%; box-sizing:border-box; padding-right:3rem;"
                            placeholder="Min. 6 characters">
                        <button type="button" onclick="togglePwd('password','eye1')"
                            style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#8b9ab0; cursor:pointer; padding:0;">
                            <svg id="eye1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Confirm
                        Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="input-dark" style="width:100%; box-sizing:border-box; padding-right:3rem;"
                            placeholder="Repeat password">
                        <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                            style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#8b9ab0; cursor:pointer; padding:0;">
                            <svg id="eye2" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Role + Status --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; align-items:end;">
                    <div>
                        <label
                            style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Role</label>
                        <select name="role" class="input-dark"
                            style="width:100%; box-sizing:border-box; cursor:pointer;">
                            <option value="user" {{ old('role','user') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Status</label>
                        <div style="display:flex; align-items:center; gap:0.75rem; padding:0.65rem 0;">
                            <input type="hidden" name="status" id="statusVal" value="{{ old('status','active') }}">
                            <button type="button" id="statusToggle" onclick="toggleStatus()"
                                style="position:relative; width:48px; height:26px; border-radius:13px; border:none; cursor:pointer; transition:background 0.25s; flex-shrink:0; background:{{ old('status','active') === 'active' ? '#22c55e' : '#4b5563' }};">
                                <span id="statusKnob"
                                    style="position:absolute; top:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left 0.25s; left:{{ old('status','active') === 'active' ? '25px' : '3px' }};"></span>
                            </button>
                            <span id="statusLabel"
                                style="font-size:0.875rem; font-weight:500; color:{{ old('status','active') === 'active' ? '#22c55e' : '#8b9ab0' }};">
                                {{ old('status','active') === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%; margin-top:0.5rem;">
                    Create User
                </button>
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
            document.getElementById('avatarImg').style.display = 'block';
            document.getElementById('avatarInitial').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function updateInitial(name) {
    const img = document.getElementById('avatarImg');
    if (img.style.display === 'none') {
        document.getElementById('avatarInitial').textContent = name ? name.charAt(0).toUpperCase() : '?';
    }
}
</script>
@endpush
@endsection
