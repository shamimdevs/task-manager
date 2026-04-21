@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div style="max-width:1280px; margin:0 auto; padding:2rem 1.5rem;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; color:#e2e8f0; margin:0 0 0.25rem;">User Management</h1>
            <p style="color:#8b9ab0; font-size:0.9rem; margin:0;">{{ $users->count() }} total users</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary" style="text-decoration:none; display:inline-block;">
            + Create User
        </a>
    </div>

    {{-- Table --}}
    <div class="glass-card" style="overflow:hidden; padding:0;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #1e2433;">
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">#</th>
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">User</th>
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">Email</th>
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">Role</th>
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                        <th style="padding:1rem 1.25rem; text-align:left; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">Joined</th>
                        <th style="padding:1rem 1.25rem; text-align:right; font-size:0.8rem; font-weight:600; color:#8b9ab0; text-transform:uppercase; letter-spacing:0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom:1px solid #1e2433; transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.02)'"
                        onmouseout="this.style.background='transparent'">

                        <td style="padding:1rem 1.25rem; color:#8b9ab0; font-size:0.85rem;">{{ $loop->iteration }}</td>

                        <td style="padding:1rem 1.25rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                {{-- Avatar --}}
                                <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#00d4ff,#7c3aed); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; color:#fff; flex-shrink:0; overflow:hidden; border:1px solid #1e2433;">
                                    @if($user->profile_image)
                                        <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <span style="color:#e2e8f0; font-size:0.9rem; font-weight:500;">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span style="font-size:0.7rem; color:#00d4ff; background:rgba(0,212,255,0.1); padding:0.1rem 0.45rem; border-radius:4px;">You</span>
                                        @endif
                                    </div>
                                    @if($user->designation)
                                        <p style="color:#8b9ab0; font-size:0.78rem; margin:0.1rem 0 0;">{{ $user->designation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td style="padding:1rem 1.25rem; color:#8b9ab0; font-size:0.875rem;">{{ $user->email }}</td>

                        <td style="padding:1rem 1.25rem;">
                            @if($user->isAdmin())
                                <span style="font-size:0.75rem; font-weight:600; color:#7c3aed; background:rgba(124,58,237,0.15); padding:0.25rem 0.6rem; border-radius:6px; border:1px solid rgba(124,58,237,0.3);">Admin</span>
                            @else
                                <span style="font-size:0.75rem; font-weight:600; color:#8b9ab0; background:rgba(139,154,176,0.1); padding:0.25rem 0.6rem; border-radius:6px; border:1px solid rgba(139,154,176,0.2);">User</span>
                            @endif
                        </td>

                        <td style="padding:1rem 1.25rem;">
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="margin:0; display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $user->isActive() ? 'Click to deactivate' : 'Click to activate' }}"
                                    style="background:none; border:none; cursor:pointer; padding:0; display:inline-flex; align-items:center; gap:0.45rem;">
                                    <div style="position:relative; width:38px; height:21px; border-radius:11px; background:{{ $user->isActive() ? '#22c55e' : '#4b5563' }}; transition:background 0.25s; flex-shrink:0;">
                                        <span style="position:absolute; top:2.5px; width:16px; height:16px; border-radius:50%; background:#fff; left:{{ $user->isActive() ? '19px' : '3px' }}; transition:left 0.25s;"></span>
                                    </div>
                                    <span style="font-size:0.78rem; font-weight:500; color:{{ $user->isActive() ? '#22c55e' : '#8b9ab0' }};">
                                        {{ $user->isActive() ? 'Active' : 'Inactive' }}
                                    </span>
                                </button>
                            </form>
                            @else
                            <div style="display:inline-flex; align-items:center; gap:0.45rem; opacity:0.6;">
                                <div style="position:relative; width:38px; height:21px; border-radius:11px; background:#22c55e; flex-shrink:0;">
                                    <span style="position:absolute; top:2.5px; width:16px; height:16px; border-radius:50%; background:#fff; left:19px;"></span>
                                </div>
                                <span style="font-size:0.78rem; font-weight:500; color:#22c55e;">Active</span>
                            </div>
                            @endif
                        </td>

                        <td style="padding:1rem 1.25rem; color:#8b9ab0; font-size:0.85rem;">{{ $user->created_at->format('d M Y') }}</td>

                        <td style="padding:1rem 1.25rem;">
                            <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.5rem;">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   style="font-size:0.78rem; padding:0.35rem 0.75rem; border-radius:6px; border:1px solid rgba(0,212,255,0.3); background:rgba(0,212,255,0.08); color:#00d4ff; text-decoration:none; transition:all 0.2s;"
                                   onmouseover="this.style.opacity='0.8'"
                                   onmouseout="this.style.opacity='1'">Edit</a>

                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin:0;"
                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="font-size:0.78rem; padding:0.35rem 0.75rem; border-radius:6px; border:1px solid rgba(239,68,68,0.3); background:rgba(239,68,68,0.08); color:#ef4444; cursor:pointer; transition:all 0.2s;"
                                        onmouseover="this.style.opacity='0.8'"
                                        onmouseout="this.style.opacity='1'">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:3rem; text-align:center; color:#8b9ab0; font-size:0.9rem;">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
