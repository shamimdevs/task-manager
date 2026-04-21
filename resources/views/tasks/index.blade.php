@extends('layouts.app')
@section('title', 'My Tasks')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">My Tasks</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $tasks->total() }} task(s) assigned to you</p>
        </div>
        {{-- Quick counts --}}
        <div class="hidden sm:flex items-center gap-2">
            @php
                $allTasks = \App\Models\Task::where('assigned_to', auth()->id());
                $counts = [
                    'pending'     => (clone $allTasks)->where('status','pending')->count(),
                    'in_progress' => (clone $allTasks)->where('status','in_progress')->count(),
                    'completed'   => (clone $allTasks)->where('status','completed')->count(),
                ];
            @endphp
            @if($counts['pending'])
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-amber-400 bg-amber-400/10 border-amber-400/30">{{ $counts['pending'] }} pending</span>
            @endif
            @if($counts['in_progress'])
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-cyan-400 bg-cyan-400/10 border-cyan-400/30">{{ $counts['in_progress'] }} in progress</span>
            @endif
            @if($counts['completed'])
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-emerald-400 bg-emerald-400/10 border-emerald-400/30">{{ $counts['completed'] }} done</span>
            @endif
        </div>
    </div>

    {{-- Search + Filters --}}
    <form method="GET" action="{{ route('my-tasks.index') }}" class="mb-6">
        <div class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       class="input-dark w-full pl-9" placeholder="Search tasks…">
            </div>

            {{-- Status --}}
            <select name="status" onchange="this.form.submit()"
                    class="input-dark cursor-pointer" style="width:auto; min-width:130px;">
                <option value="">All Status</option>
                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                <option value="{{ $val }}" {{ $status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Priority --}}
            <select name="priority" onchange="this.form.submit()"
                    class="input-dark cursor-pointer" style="width:auto; min-width:130px;">
                <option value="">All Priority</option>
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                <option value="{{ $val }}" {{ $priority === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary text-sm px-4">Search</button>

            @if($search || $status || $priority)
            <a href="{{ route('my-tasks.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-slate-400 border border-slate-700 hover:text-slate-200 hover:border-slate-500 transition-all no-underline">
                ✕ Clear
            </a>
            @endif
        </div>
    </form>

    {{-- Task list --}}
    @if($tasks->isEmpty())
    <div class="glass-card p-16 text-center">
        <div class="w-14 h-14 rounded-2xl bg-cyan-400/10 border border-cyan-400/20 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm">
            @if($search || $status || $priority)
                No tasks match your filters.
            @else
                No tasks assigned to you yet.
            @endif
        </p>
    </div>
    @else
    <div class="flex flex-col gap-3">
        @foreach($tasks as $task)
        <div class="glass-card hover:border-slate-600 transition-all duration-200 overflow-hidden">
            <div class="flex items-stretch">

                {{-- Priority stripe --}}
                @php
                    $stripe = match($task->priority) {
                        'urgent' => 'bg-red-500',
                        'high'   => 'bg-orange-400',
                        'medium' => 'bg-yellow-400',
                        default  => 'bg-slate-600',
                    };
                @endphp
                <div class="w-1 flex-shrink-0 {{ $stripe }}"></div>

                <div class="flex-1 p-5">
                    <div class="flex items-start gap-4">

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                <h3 class="font-semibold text-sm {{ $task->status === 'completed' ? 'line-through text-slate-500' : 'text-slate-100' }}">
                                    {{ $task->title }}
                                </h3>
                                @if($task->isOverdue())
                                    <span class="text-xs font-medium text-red-400 bg-red-400/10 border border-red-400/30 px-2 py-0.5 rounded-full">Overdue</span>
                                @endif
                            </div>

                            @if($task->description)
                            <p class="text-slate-500 text-xs leading-relaxed mb-3 line-clamp-2">{{ $task->description }}</p>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ \App\Models\Task::priorityColor($task->priority) }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @if($task->due_date)
                                <div class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-400' : '' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Due {{ $task->due_date->format('d M Y') }}
                                </div>
                                @endif
                                <span>by {{ $task->creator?->name }}</span>
                                @if($task->attachment)
                                <a href="{{ Storage::url($task->attachment) }}" target="_blank"
                                   class="flex items-center gap-1 text-cyan-400 hover:text-cyan-300 transition-colors no-underline">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.41 17.41a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                    Attachment
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Status + Actions --}}
                        <div class="flex-shrink-0 flex flex-col items-end gap-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ \App\Models\Task::statusColor($task->status) }}">
                                {{ \App\Models\Task::statusLabel($task->status) }}
                            </span>

                            @if(!in_array($task->status, ['completed', 'cancelled']))
                            <div class="flex items-center gap-1.5">
                                @if($task->status !== 'in_progress')
                                <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-md border border-cyan-400/30 bg-cyan-400/8 text-cyan-400 hover:bg-cyan-400/20 transition-all cursor-pointer">Start</button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-md border border-emerald-400/30 bg-emerald-400/8 text-emerald-400 hover:bg-emerald-400/20 transition-all cursor-pointer">Done</button>
                                </form>
                            </div>
                            @endif

                            @if($task->status === 'completed')
                            <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="text-xs px-2.5 py-1 rounded-md border border-slate-600 text-slate-500 hover:text-slate-300 hover:border-slate-500 transition-all cursor-pointer">Reopen</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($tasks->hasPages())
    <div class="mt-4 flex items-center justify-between">
        <p class="text-xs text-slate-500">
            Showing {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
        </p>
        {{ $tasks->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
