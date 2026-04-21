@extends('layouts.app')
@section('title', 'Task Management')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Task Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $tasks->count() }} total tasks</p>
        </div>
        <a href="{{ route('admin.tasks.create') }}" class="btn-primary text-sm no-underline">+ Create Task</a>
    </div>

    {{-- Filter bar --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
        <button onclick="filterTasks('{{ $val }}')"
            class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium border transition-all duration-150 cursor-pointer"
            data-filter="{{ $val }}"
            style="background:rgba(139,154,176,0.08); border-color:rgba(139,154,176,0.2); color:#8b9ab0;">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if($tasks->isEmpty())
    <div class="glass-card p-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-400/20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0-1 2"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm">No tasks yet. <a href="{{ route('admin.tasks.create') }}" class="text-cyan-400 hover:underline">Create the first one.</a></p>
    </div>
    @else
    <div class="grid gap-4" id="taskGrid">
        @foreach($tasks as $task)
        <div class="glass-card p-5 transition-all duration-200 hover:border-slate-600 task-card" data-status="{{ $task->status }}">
            <div class="flex items-start gap-4">

                {{-- Priority dot --}}
                <div class="mt-1 flex-shrink-0">
                    @php
                        $dot = match($task->priority) {
                            'urgent' => 'bg-red-400',
                            'high'   => 'bg-orange-400',
                            'medium' => 'bg-yellow-400',
                            default  => 'bg-slate-500',
                        };
                    @endphp
                    <span class="block w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
                </div>

                {{-- Main content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="font-semibold text-slate-100 text-sm truncate">{{ $task->title }}</h3>
                        @if($task->isOverdue())
                            <span class="text-xs font-medium text-red-400 bg-red-400/10 border border-red-400/30 px-2 py-0.5 rounded-full">Overdue</span>
                        @endif
                    </div>

                    @if($task->description)
                    <p class="text-slate-500 text-xs leading-relaxed mb-2 line-clamp-2">{{ $task->description }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                        {{-- Assigned to --}}
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center text-white font-bold text-[10px] overflow-hidden flex-shrink-0">
                                @if($task->assignee?->profile_image)
                                    <img src="{{ Storage::url($task->assignee->profile_image) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($task->assignee?->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            <span>{{ $task->assignee?->name ?? 'Unknown' }}</span>
                        </div>

                        {{-- Created by --}}
                        <span class="text-slate-600">by {{ $task->creator?->name ?? 'Unknown' }}</span>

                        {{-- Due date --}}
                        @if($task->due_date)
                        <div class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-400' : '' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span>{{ $task->due_date->format('d M Y') }}</span>
                        </div>
                        @endif

                        {{-- Attachment --}}
                        @if($task->attachment)
                        <a href="{{ Storage::url($task->attachment) }}" target="_blank" class="flex items-center gap-1 text-cyan-400 hover:text-cyan-300 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.41 17.41a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            <span>Attachment</span>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Right side: badges + actions --}}
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::priorityColor($task->priority) }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::statusColor($task->status) }}">
                            {{ \App\Models\Task::statusLabel($task->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-1">
                        <a href="{{ route('admin.tasks.edit', $task) }}"
                           class="text-xs px-2.5 py-1 rounded-md border border-cyan-400/30 bg-cyan-400/8 text-cyan-400 hover:bg-cyan-400/20 transition-all no-underline">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}"
                              onsubmit="return confirm('Delete this task?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-xs px-2.5 py-1 rounded-md border border-red-400/30 bg-red-400/8 text-red-400 hover:bg-red-400/20 transition-all cursor-pointer">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
let activeFilter = 'all';
function filterTasks(filter) {
    activeFilter = filter;
    document.querySelectorAll('.task-card').forEach(card => {
        card.style.display = (filter === 'all' || card.dataset.status === filter) ? '' : 'none';
    });
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const active = btn.dataset.filter === filter;
        btn.style.background = active ? 'rgba(0,212,255,0.12)' : 'rgba(139,154,176,0.08)';
        btn.style.borderColor = active ? 'rgba(0,212,255,0.4)' : 'rgba(139,154,176,0.2)';
        btn.style.color = active ? '#00d4ff' : '#8b9ab0';
    });
}
filterTasks('all');
</script>
@endpush
@endsection
