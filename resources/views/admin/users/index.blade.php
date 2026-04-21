@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">User Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $users->total() }} total users</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary text-sm no-underline">+ Create User</a>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
        <div class="flex gap-3">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       class="input-dark w-full pl-9 pr-4" placeholder="Search by name, email or designation…">
            </div>
            @if($search)
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-slate-400 border border-slate-700 hover:text-slate-200 hover:border-slate-500 transition-all no-underline">
                ✕ Clear
            </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="glass-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-white/[0.02] transition-colors">

                        <td class="px-5 py-3.5 text-sm text-slate-500">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center font-bold text-sm text-white flex-shrink-0 overflow-hidden border border-slate-700">
                                    @if($user->profile_image)
                                        <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-medium text-slate-200">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="text-[10px] text-cyan-400 bg-cyan-400/10 px-1.5 py-0.5 rounded">You</span>
                                        @endif
                                    </div>
                                    @if($user->designation)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $user->designation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ $user->email }}</td>

                        <td class="px-5 py-3.5">
                            @if($user->isAdmin())
                                <span class="text-xs font-semibold text-violet-400 bg-violet-400/10 border border-violet-400/30 px-2 py-0.5 rounded-full">Admin</span>
                            @else
                                <span class="text-xs font-semibold text-slate-400 bg-slate-400/10 border border-slate-400/20 px-2 py-0.5 rounded-full">User</span>
                            @endif
                        </td>

                        <td class="px-5 py-3.5">
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="flex items-center gap-2 cursor-pointer bg-transparent border-none p-0">
                                    <div class="relative w-9 h-5 rounded-full transition-colors duration-200 flex-shrink-0"
                                         style="background: {{ $user->isActive() ? '#22c55e' : '#4b5563' }}">
                                        <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white transition-all duration-200"
                                              style="left: {{ $user->isActive() ? '17px' : '2px' }}"></span>
                                    </div>
                                    <span class="text-xs font-medium {{ $user->isActive() ? 'text-emerald-400' : 'text-slate-500' }}">
                                        {{ $user->isActive() ? 'Active' : 'Inactive' }}
                                    </span>
                                </button>
                            </form>
                            @else
                            <div class="flex items-center gap-2 opacity-60">
                                <div class="relative w-9 h-5 rounded-full bg-emerald-500 flex-shrink-0">
                                    <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white" style="left:17px"></span>
                                </div>
                                <span class="text-xs font-medium text-emerald-400">Active</span>
                            </div>
                            @endif
                        </td>

                        <td class="px-5 py-3.5 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>

                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.report', $user) }}"
                                   class="text-xs px-2.5 py-1 rounded-md border border-violet-400/30 bg-violet-400/8 text-violet-400 hover:bg-violet-400/20 transition-all no-underline">
                                    Report
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-xs px-2.5 py-1 rounded-md border border-cyan-400/30 bg-cyan-400/8 text-cyan-400 hover:bg-cyan-400/20 transition-all no-underline">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs px-2.5 py-1 rounded-md border border-red-400/30 bg-red-400/8 text-red-400 hover:bg-red-400/20 transition-all cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center text-slate-500 text-sm">
                            @if($search)
                                No users found for "<span class="text-slate-300">{{ $search }}</span>".
                            @else
                                No users yet.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-slate-800">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
                </p>
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
