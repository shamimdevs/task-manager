@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div style="max-width:720px; margin:0 auto; padding:2rem 1.5rem;">

    <h1 style="font-size:1.75rem; font-weight:700; color:#e2e8f0; margin:0 0 2rem;">My Profile</h1>

    {{-- Profile card --}}
    <div class="glass-card" style="margin-bottom:1.5rem;">
        <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;" class="p-4">
            {{-- Avatar --}}
            <div
                style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg,#00d4ff,#7c3aed); display:flex; align-items:center; justify-content:center; font-size:1.75rem; font-weight:700; color:#fff; flex-shrink:0; overflow:hidden; border:2px solid #1e2433;">
                @if($user->profile_image)
                <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}"
                    style="width:100%; height:100%; object-fit:cover;">
                @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div style="flex:1; min-width:0;">
                <h2
                    style="font-size:1.2rem; font-weight:700; color:#e2e8f0; margin:0 0 0.2rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->name }}</h2>
                @if($user->designation)
                <p style="color:#00d4ff; font-size:0.875rem; margin:0 0 0.4rem; font-weight:500;">
                    {{ $user->designation }}</p>
                @endif
                <p style="color:#8b9ab0; font-size:0.85rem; margin:0 0 0.5rem;">{{ $user->email }}</p>
                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                    @if($user->isAdmin())
                    <span
                        style="font-size:0.72rem; font-weight:600; color:#7c3aed; background:rgba(124,58,237,0.15); padding:0.2rem 0.6rem; border-radius:6px; border:1px solid rgba(124,58,237,0.3);">Admin</span>
                    @else
                    <span
                        style="font-size:0.72rem; font-weight:600; color:#8b9ab0; background:rgba(139,154,176,0.1); padding:0.2rem 0.6rem; border-radius:6px; border:1px solid rgba(139,154,176,0.2);">User</span>
                    @endif
                    @if($user->isActive())
                    <span
                        style="font-size:0.72rem; font-weight:600; color:#22c55e; background:rgba(34,197,94,0.12); padding:0.2rem 0.6rem; border-radius:6px; border:1px solid rgba(34,197,94,0.25); display:inline-flex; align-items:center; gap:0.3rem;">
                        <span style="width:5px; height:5px; border-radius:50%; background:#22c55e;"></span>Active
                    </span>
                    @endif
                </div>
            </div>
            <div style="text-align:right; color:#8b9ab0; font-size:0.8rem; flex-shrink:0;">
                <p style="margin:0;">Member since</p>
                <p style="margin:0.1rem 0 0; color:#e2e8f0; font-weight:500;">{{ $user->created_at->format('d M Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="glass-card p-4">
        <h3 style="font-size:1rem; font-weight:600; color:#e2e8f0; margin:0 0 1.5rem;">Edit Profile</h3>

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

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div style="display:flex; flex-direction:column; gap:1.25rem;">

                {{-- Photo upload --}}
                <div style="display:flex; align-items:center; gap:1.25rem;">
                    <div
                        style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg,#00d4ff,#7c3aed); display:flex; align-items:center; justify-content:center; font-size:1.4rem; font-weight:700; color:#fff; flex-shrink:0; overflow:hidden; border:2px solid #1e2433;">
                        @if($user->profile_image)
                        <img id="avatarImg" src="{{ Storage::url($user->profile_image) }}" alt=""
                            style="width:100%; height:100%; object-fit:cover;">
                        @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <img id="avatarImg" src="" alt=""
                            style="display:none; width:100%; height:100%; object-fit:cover;">
                        @endif
                    </div>
                    <div>
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
                            Change Photo
                            <input type="file" name="profile_image" accept="image/jpg,image/jpeg,image/png,image/webp"
                                style="display:none;" onchange="previewImage(this)">
                        </label>
                        <p style="color:#8b9ab0; font-size:0.75rem; margin:0.35rem 0 0;">JPG, PNG, WebP — max 2MB</p>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label
                            style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Full
                            Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="input-dark" style="width:100%; box-sizing:border-box;">
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Email
                            Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="input-dark" style="width:100%; box-sizing:border-box;">
                    </div>
                </div>

                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}"
                        class="input-dark" style="width:100%; box-sizing:border-box;"
                        placeholder="e.g. Software Engineer">
                </div>

                <div style="border-top:1px solid #1e2433; padding-top:1.25rem;">
                    <h4 style="font-size:0.9rem; font-weight:600; color:#e2e8f0; margin:0 0 0.5rem;">Change Password
                    </h4>
                    <p style="color:#8b9ab0; font-size:0.8rem; margin:0 0 1rem;">Leave blank to keep your current
                        password.</p>

                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <div>
                            <label
                                style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">Current
                                Password</label>
                            <div style="position:relative;">
                                <input type="password" name="current_password" id="current_password" class="input-dark"
                                    style="width:100%; box-sizing:border-box; padding-right:3rem;"
                                    placeholder="Enter current password">
                                <button type="button" onclick="togglePwd('current_password','eye0')"
                                    style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#8b9ab0; cursor:pointer; padding:0;">
                                    <svg id="eye0" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                            <div>
                                <label
                                    style="display:block; font-size:0.875rem; font-weight:500; color:#8b9ab0; margin-bottom:0.5rem;">New
                                    Password</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="password" class="input-dark"
                                        style="width:100%; box-sizing:border-box; padding-right:3rem;"
                                        placeholder="Min. 6 characters">
                                    <button type="button" onclick="togglePwd('password','eye1')"
                                        style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#8b9ab0; cursor:pointer; padding:0;">
                                        <svg id="eye1" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
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
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="input-dark"
                                        style="width:100%; box-sizing:border-box; padding-right:3rem;"
                                        placeholder="Repeat new password">
                                    <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                                        style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:#8b9ab0; cursor:pointer; padding:0;">
                                        <svg id="eye2" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width:100%; margin-top:0.5rem;">
                    Save Changes
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

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('avatarImg');
            img.src = e.target.result;
            img.style.display = 'block';
            const initial = document.getElementById('avatarInitial');
            if (initial) initial.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection