@extends('layouts.app')
@section('title', 'My Tasks')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">My Tasks</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $tasks->count() }} task(s) assigned to you</p>
        </div>
        {{-- Stats row --}}
        <div class="flex items-center gap-3">
            @php
                $pending   = $tasks->where('status', 'pending')->count();
                $progress  = $tasks->where('status', 'in_progress')->count();
                $completed = $tasks->where('status', 'completed')->count();
            @endphp
            @if($pending)
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-amber-400 bg-amber-400/10 border-amber-400/30">{{ $pending }} pending</span>
            @endif
            @if($progress)
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-cyan-400 bg-cyan-400/10 border-cyan-400/30">{{ $progress }} in progress</span>
            @endif
            @if($completed)
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full border text-emerald-400 bg-emerald-400/10 border-emerald-400/30">{{ $completed }} done</span>
            @endif
        </div>
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
        <p class="text-slate-400 text-sm">No tasks assigned to you yet.</p>
    </div>
    @else
    <div class="flex flex-col gap-4" id="taskGrid">
        @foreach($tasks as $task)
        <div class="glass-card transition-all duration-200 hover:border-slate-600 task-card overflow-hidden" data-status="{{ $task->status }}">
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
                                <h3 class="font-semibold text-slate-100 text-sm {{ $task->status === 'completed' ? 'line-through text-slate-500' : '' }}">
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
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ \App\Models\Task::priorityColor($task->priority) }}">{{ ucfirst($task->priority) }}</span>

                                @if($task->due_date)
                                <div class="flex items-center gap-1 {{ $task->isOverdue() ? 'text-red-400' : '' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span>Due {{ $task->due_date->format('d M Y') }}</span>
                                </div>
                                @endif

                                <span>Assigned by {{ $task->creator?->name }}</span>

                                @if($task->attachment)
                                <a href="{{ Storage::url($task->attachment) }}" target="_blank"
                                   class="flex items-center gap-1 text-cyan-400 hover:text-cyan-300 transition-colors no-underline">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.41 17.41a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                    Attachment
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Status changer --}}
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
                                    <button type="submit"
                                        class="text-xs px-2.5 py-1 rounded-md border border-cyan-400/30 bg-cyan-400/8 text-cyan-400 hover:bg-cyan-400/20 transition-all cursor-pointer">
                                        Start
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit"
                                        class="text-xs px-2.5 py-1 rounded-md border border-emerald-400/30 bg-emerald-400/8 text-emerald-400 hover:bg-emerald-400/20 transition-all cursor-pointer">
                                        Done
                                    </button>
                                </form>
                            </div>
                            @endif

                            @if($task->status === 'completed')
                            <form method="POST" action="{{ route('my-tasks.update-status', $task) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit"
                                    class="text-xs px-2.5 py-1 rounded-md border border-slate-600 text-slate-500 hover:text-slate-300 hover:border-slate-500 transition-all cursor-pointer">
                                    Reopen
                                </button>
                            </form>
                            @endif
                        </div>
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
function filterTasks(filter) {
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
