@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-100 mb-1">
            {{ $user->isAdmin() ? 'Admin Dashboard' : 'My Dashboard' }}
        </h1>
        <p class="text-sm text-slate-500">
            Welcome back, <span class="text-cyan-400 font-medium">{{ $user->name }}</span>
            <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full border
                {{ $user->isAdmin() ? 'text-violet-400 bg-violet-400/10 border-violet-400/30' : 'text-cyan-400 bg-cyan-400/10 border-cyan-400/30' }}">
                {{ strtoupper($user->role) }}
            </span>
        </p>
    </div>

    {{-- Stats grid --}}
    @php
    $statCards = $user->isAdmin()
        ? [
            ['label' => 'Total Tasks',  'value' => $stats['total_tasks'],  'color' => '#00d4ff', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2'],
            ['label' => 'In Progress',  'value' => $stats['in_progress'],  'color' => '#f59e0b', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
            ['label' => 'Completed',    'value' => $stats['completed'],    'color' => '#10b981', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
            ['label' => 'Total Users',  'value' => $stats['total_users'],  'color' => '#7c3aed', 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
          ]
        : [
            ['label' => 'My Tasks',     'value' => $stats['my_tasks'],    'color' => '#00d4ff', 'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2'],
            ['label' => 'In Progress',  'value' => $stats['in_progress'], 'color' => '#f59e0b', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
            ['label' => 'Completed',    'value' => $stats['completed'],   'color' => '#10b981', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
            ['label' => 'Pending',      'value' => $stats['pending'],     'color' => '#ef4444', 'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
          ];
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($statCards as $card)
        <div class="glass-card p-5 flex items-center gap-4 hover:-translate-y-0.5 transition-all duration-200 cursor-default group">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:{{ $card['color'] }}18;">
                <svg class="w-5 h-5" fill="none" stroke="{{ $card['color'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-100 leading-none">{{ $card['value'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Recent Tasks --}}
    <div class="glass-card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
            <h2 class="font-semibold text-slate-200 text-sm">
                {{ $user->isAdmin() ? 'Recent Tasks' : 'My Recent Tasks' }}
            </h2>
            <a href="{{ $user->isAdmin() ? route('admin.tasks.index') : route('my-tasks.index') }}"
               class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors no-underline">
                View all →
            </a>
        </div>

        @if($recentTasks->isEmpty())
        <div class="px-6 py-12 text-center">
            <p class="text-slate-500 text-sm">
                @if($user->isAdmin())
                    No tasks yet. <a href="{{ route('admin.tasks.create') }}" class="text-cyan-400 hover:underline">Create one.</a>
                @else
                    No tasks assigned to you yet.
                @endif
            </p>
        </div>
        @else
        <div class="divide-y divide-slate-800">
            @foreach($recentTasks as $task)
            <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-white/[0.02] transition-colors">
                @php
                    $dot = match($task->priority) {
                        'urgent' => 'bg-red-400',
                        'high'   => 'bg-orange-400',
                        'medium' => 'bg-yellow-400',
                        default  => 'bg-slate-600',
                    };
                @endphp
                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>

                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-200 truncate {{ $task->status === 'completed' ? 'line-through text-slate-500' : '' }}">
                        {{ $task->title }}
                    </p>
                    <p class="text-xs text-slate-600 mt-0.5">
                        @if($user->isAdmin())
                            Assigned to {{ $task->assignee?->name }}
                        @else
                            by {{ $task->creator?->name }}
                        @endif
                        @if($task->due_date)
                            · Due {{ $task->due_date->format('d M') }}
                        @endif
                    </p>
                </div>

                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border flex-shrink-0 {{ \App\Models\Task::statusColor($task->status) }}">
                    {{ \App\Models\Task::statusLabel($task->status) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
