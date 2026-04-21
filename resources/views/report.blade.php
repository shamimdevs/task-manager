@extends('layouts.app')
@section('title', 'My Report')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    {{-- Header --}}
    <div class="mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-100">My Report</h1>
            <p class="text-sm text-slate-500 mt-0.5">Generated {{ now()->format('d M Y, H:i') }}</p>
        </div>
    </div>

    {{-- ── PROFILE SUMMARY ── --}}
    <div class="glass-card p-4 sm:p-6 mb-6">
        <div class="flex items-center gap-5 flex-wrap">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center text-2xl font-bold text-white flex-shrink-0 overflow-hidden border-2 border-slate-700">
                @if($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name,0,1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold text-slate-100">{{ $user->name }}</h2>
                @if($user->designation)
                    <p class="text-cyan-400 text-sm font-medium">{{ $user->designation }}</p>
                @endif
                <p class="text-slate-500 text-sm">{{ $user->email }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-3xl font-bold text-slate-100 leading-none">{{ $completionRate }}%</div>
                <div class="text-xs text-slate-500 mt-0.5">Completion Rate</div>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php $statCards = [
            ['label'=>'Total',       'value'=>$total,                    'color'=>'text-slate-200',   'border'=>'border-slate-700',     'bg'=>'bg-slate-800/40'],
            ['label'=>'Pending',     'value'=>$statusCounts['pending'],  'color'=>'text-amber-400',   'border'=>'border-amber-400/25',  'bg'=>'bg-amber-400/8'],
            ['label'=>'In Progress', 'value'=>$statusCounts['in_progress'],'color'=>'text-cyan-400', 'border'=>'border-cyan-400/25',   'bg'=>'bg-cyan-400/8'],
            ['label'=>'Completed',   'value'=>$statusCounts['completed'],'color'=>'text-emerald-400', 'border'=>'border-emerald-400/25','bg'=>'bg-emerald-400/8'],
            ['label'=>'Cancelled',   'value'=>$statusCounts['cancelled'],'color'=>'text-slate-400',   'border'=>'border-slate-600',     'bg'=>'bg-slate-800/40'],
            ['label'=>'Overdue',     'value'=>$overdue,                  'color'=>'text-red-400',     'border'=>'border-red-400/25',    'bg'=>'bg-red-400/8'],
        ]; @endphp
        @foreach($statCards as $c)
        <div class="glass-card p-4 text-center border {{ $c['border'] }} {{ $c['bg'] }}">
            <div class="text-2xl font-bold {{ $c['color'] }} leading-none mb-1">{{ $c['value'] }}</div>
            <div class="text-xs text-slate-500">{{ $c['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── PROGRESS BARS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- Status breakdown --}}
        <div class="glass-card p-6">
            <h2 class="text-sm font-semibold text-slate-200 mb-4">Status Breakdown</h2>
            @php $statusMeta = [
                'pending'     => ['label'=>'Pending',     'bar'=>'bg-amber-400',   'text'=>'text-amber-400'],
                'in_progress' => ['label'=>'In Progress', 'bar'=>'bg-cyan-400',    'text'=>'text-cyan-400'],
                'completed'   => ['label'=>'Completed',   'bar'=>'bg-emerald-400', 'text'=>'text-emerald-400'],
                'cancelled'   => ['label'=>'Cancelled',   'bar'=>'bg-slate-500',   'text'=>'text-slate-400'],
            ]; @endphp
            <div class="flex flex-col gap-3.5">
                @foreach($statusMeta as $key => $meta)
                @php $cnt = $statusCounts[$key]; $pct = $total > 0 ? round($cnt/$total*100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-slate-400 font-medium">{{ $meta['label'] }}</span>
                        <span class="{{ $meta['text'] }} font-semibold">{{ $cnt }} <span class="text-slate-600">({{ $pct }}%)</span></span>
                    </div>
                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div class="{{ $meta['bar'] }} h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Overall completion --}}
            @if($total > 0)
            <div class="mt-5 pt-4 border-t border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-slate-400 font-medium">Overall Completion</span>
                    <span class="text-sm font-bold text-emerald-400">{{ $completionRate }}%</span>
                </div>
                <div class="h-3 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-emerald-400" style="width:{{ $completionRate }}%"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Priority breakdown --}}
        <div class="glass-card p-6">
            <h2 class="text-sm font-semibold text-slate-200 mb-4">Priority Breakdown</h2>
            @php $priorityMeta = [
                'urgent' => ['label'=>'Urgent','bar'=>'bg-red-500',    'text'=>'text-red-400'],
                'high'   => ['label'=>'High',  'bar'=>'bg-orange-400', 'text'=>'text-orange-400'],
                'medium' => ['label'=>'Medium','bar'=>'bg-yellow-400', 'text'=>'text-yellow-400'],
                'low'    => ['label'=>'Low',   'bar'=>'bg-slate-500',  'text'=>'text-slate-400'],
            ]; @endphp
            <div class="flex flex-col gap-3.5">
                @foreach($priorityMeta as $key => $meta)
                @php $cnt = $priorityCounts[$key]; $pct = $total > 0 ? round($cnt/$total*100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-slate-400 font-medium">{{ $meta['label'] }}</span>
                        <span class="{{ $meta['text'] }} font-semibold">{{ $cnt }} <span class="text-slate-600">({{ $pct }}%)</span></span>
                    </div>
                    <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div class="{{ $meta['bar'] }} h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── TASKS BY STATUS SECTIONS ── --}}
    @php
        $sections = [
            'in_progress' => ['label'=>'In Progress', 'color'=>'text-cyan-400',    'border'=>'border-l-cyan-400',    'dot'=>'bg-cyan-400'],
            'pending'     => ['label'=>'Pending',      'color'=>'text-amber-400',   'border'=>'border-l-amber-400',   'dot'=>'bg-amber-400'],
            'completed'   => ['label'=>'Completed',    'color'=>'text-emerald-400', 'border'=>'border-l-emerald-400', 'dot'=>'bg-emerald-400'],
            'cancelled'   => ['label'=>'Cancelled',    'color'=>'text-slate-400',   'border'=>'border-l-slate-500',   'dot'=>'bg-slate-500'],
        ];
    @endphp

    @foreach($sections as $status => $meta)
    @if(isset($tasksByStatus[$status]) && $tasksByStatus[$status]->count())
    <div class="glass-card overflow-hidden p-0 mb-4">
        <div class="flex items-center gap-3 px-4 sm:px-6 py-3.5 border-b border-slate-800">
            <span class="w-2.5 h-2.5 rounded-full {{ $meta['dot'] }}"></span>
            <h3 class="text-sm font-semibold {{ $meta['color'] }}">{{ $meta['label'] }}</h3>
            <span class="text-xs text-slate-500 ml-auto">{{ $tasksByStatus[$status]->count() }} tasks</span>
        </div>
        <div class="divide-y divide-slate-800">
            @foreach($tasksByStatus[$status] as $task)
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3.5 hover:bg-white/[0.02] transition-colors border-l-2 {{ $meta['border'] }}">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium {{ $status==='completed'?'line-through text-slate-500':'text-slate-200' }} truncate">{{ $task->title }}</p>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-0.5">
                        <span>by {{ $task->creator?->name }}</span>
                        @if($task->due_date)
                        <span class="flex items-center gap-1 {{ $task->isOverdue()?'text-red-400':'' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $task->due_date->format('d M Y') }}
                            @if($task->isOverdue()) · <span class="text-red-400">Overdue</span> @endif
                        </span>
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
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full border flex-shrink-0 {{ \App\Models\Task::priorityColor($task->priority) }}">
                    {{ ucfirst($task->priority) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    @if($total === 0)
    <div class="glass-card p-16 text-center">
        <p class="text-slate-400 text-sm">No tasks assigned to you yet.</p>
    </div>
    @endif

</div>
@endsection
