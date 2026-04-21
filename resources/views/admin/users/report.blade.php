@extends('layouts.app')
@section('title', 'User Report — ' . $user->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    {{-- Back + header --}}
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-300 transition-colors no-underline mb-3">
            ← Back to Users
        </a>
        <div class="flex flex-wrap items-start justify-between gap-2">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-100">User Report</h1>
            <span class="text-xs text-slate-500 mt-1">Generated {{ now()->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Profile card --}}
    <div class="glass-card p-4 sm:p-6 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center text-3xl font-bold text-white flex-shrink-0 overflow-hidden border-2 border-slate-700">
                @if($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-slate-100">{{ $user->name }}</h2>
                @if($user->designation)
                    <p class="text-cyan-400 text-sm font-medium mt-0.5">{{ $user->designation }}</p>
                @endif
                <p class="text-slate-400 text-sm mt-0.5">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-2 flex-wrap">
                    @if($user->isAdmin())
                        <span class="text-xs font-semibold text-violet-400 bg-violet-400/10 border border-violet-400/30 px-2.5 py-0.5 rounded-full">Admin</span>
                    @else
                        <span class="text-xs font-semibold text-slate-400 bg-slate-400/10 border border-slate-400/20 px-2.5 py-0.5 rounded-full">User</span>
                    @endif
                    @if($user->isActive())
                        <span class="text-xs font-semibold text-emerald-400 bg-emerald-400/10 border border-emerald-400/25 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Active
                        </span>
                    @else
                        <span class="text-xs font-semibold text-red-400 bg-red-400/10 border border-red-400/25 px-2.5 py-0.5 rounded-full">Inactive</span>
                    @endif
                    <span class="text-xs text-slate-500">Joined {{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="text-sm px-4 py-2 rounded-lg border border-cyan-400/30 bg-cyan-400/8 text-cyan-400 hover:bg-cyan-400/20 transition-all no-underline">
                    Edit User
                </a>
            </div>
        </div>
    </div>

    {{-- Task stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php
        $statCards = [
            ['label' => 'Total',       'value' => $taskStats['total'],       'color' => 'text-slate-300',  'bg' => 'bg-slate-400/10',  'border' => 'border-slate-400/20'],
            ['label' => 'Pending',     'value' => $taskStats['pending'],     'color' => 'text-amber-400',  'bg' => 'bg-amber-400/10',  'border' => 'border-amber-400/25'],
            ['label' => 'In Progress', 'value' => $taskStats['in_progress'], 'color' => 'text-cyan-400',   'bg' => 'bg-cyan-400/10',   'border' => 'border-cyan-400/25'],
            ['label' => 'Completed',   'value' => $taskStats['completed'],   'color' => 'text-emerald-400','bg' => 'bg-emerald-400/10','border' => 'border-emerald-400/25'],
            ['label' => 'Cancelled',   'value' => $taskStats['cancelled'],   'color' => 'text-slate-400',  'bg' => 'bg-slate-400/8',   'border' => 'border-slate-400/20'],
            ['label' => 'Overdue',     'value' => $taskStats['overdue'],     'color' => 'text-red-400',    'bg' => 'bg-red-400/10',    'border' => 'border-red-400/25'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="glass-card p-4 text-center {{ $card['bg'] }} border {{ $card['border'] }}">
            <div class="text-2xl font-bold {{ $card['color'] }} leading-none mb-1">{{ $card['value'] }}</div>
            <div class="text-xs text-slate-500">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Completion bar --}}
    @if($taskStats['total'] > 0)
    <div class="glass-card p-5 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-300">Task Completion</span>
            @php $pct = round($taskStats['completed'] / $taskStats['total'] * 100); @endphp
            <span class="text-sm font-bold text-emerald-400">{{ $pct }}%</span>
        </div>
        <div class="w-full h-2.5 bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-emerald-400 transition-all duration-500"
                 style="width: {{ $pct }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-slate-600 mt-1.5">
            <span>0</span>
            <span>{{ $taskStats['total'] }} tasks</span>
        </div>
    </div>
    @endif

    {{-- Task list --}}
    <div class="glass-card overflow-hidden p-0">
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-slate-800">
            <h3 class="font-semibold text-slate-200 text-sm">Assigned Tasks</h3>
            <span class="text-xs text-slate-500">{{ $tasks->count() }} tasks</span>
        </div>

        @if($tasks->isEmpty())
        <div class="py-12 text-center text-slate-500 text-sm">No tasks assigned to this user.</div>
        @else
        <div class="divide-y divide-slate-800">
            @foreach($tasks as $task)
            <div class="flex items-center gap-3 px-4 sm:px-6 py-4 hover:bg-white/[0.02] transition-colors">

                {{-- Priority stripe --}}
                @php
                    $stripe = match($task->priority) {
                        'urgent' => 'bg-red-400',
                        'high'   => 'bg-orange-400',
                        'medium' => 'bg-yellow-400',
                        default  => 'bg-slate-600',
                    };
                @endphp
                <span class="w-1.5 h-8 rounded-full flex-shrink-0 {{ $stripe }}"></span>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-200 truncate {{ $task->status === 'completed' ? 'line-through text-slate-500' : '' }}">
                        {{ $task->title }}
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-0.5">
                        <span>by {{ $task->creator?->name }}</span>
                        @if($task->due_date)
                        <div class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-400' : '' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $task->due_date->format('d M Y') }}
                            @if($task->isOverdue()) <span class="text-red-400">(overdue)</span> @endif
                        </div>
                        @endif
                        @if($task->attachment)
                        <a href="{{ Storage::url($task->attachment) }}" target="_blank"
                           class="flex items-center gap-1 text-cyan-400 hover:text-cyan-300 no-underline">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.41 17.41a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            Attachment
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Badges --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::priorityColor($task->priority) }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::statusColor($task->status) }}">
                        {{ \App\Models\Task::statusLabel($task->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
