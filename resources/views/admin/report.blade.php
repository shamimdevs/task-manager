@extends('layouts.app')
@section('title', 'Admin Report')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-100">Admin Report</h1>
            <p class="text-sm text-slate-500 mt-0.5">Generated {{ now()->format('d M Y, H:i') }}</p>
        </div>
    </div>

    {{-- ── OVERVIEW STATS ── --}}
    @php
    $pending = $statusCounts['pending'] ?? 0;
    $inprogress = $statusCounts['in_progress'] ?? 0;
    $completed = $statusCounts['completed'] ?? 0;
    $cancelled = $statusCounts['cancelled'] ?? 0;
    $completionRate = $totalTasks > 0 ? round($completed / $totalTasks * 100) : 0;
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @php $overviewCards = [
        ['label'=>'Total Tasks', 'value'=>$totalTasks, 'color'=>'text-slate-200', 'ring'=>'border-slate-700',
        'bg'=>'bg-slate-800/40'],
        ['label'=>'Pending', 'value'=>$pending, 'color'=>'text-amber-400', 'ring'=>'border-amber-400/25',
        'bg'=>'bg-amber-400/8'],
        ['label'=>'In Progress', 'value'=>$inprogress, 'color'=>'text-cyan-400', 'ring'=>'border-cyan-400/25',
        'bg'=>'bg-cyan-400/8'],
        ['label'=>'Completed', 'value'=>$completed, 'color'=>'text-emerald-400',
        'ring'=>'border-emerald-400/25','bg'=>'bg-emerald-400/8'],
        ['label'=>'Cancelled', 'value'=>$cancelled, 'color'=>'text-slate-400', 'ring'=>'border-slate-600',
        'bg'=>'bg-slate-800/40'],
        ['label'=>'Overdue', 'value'=>$overdue, 'color'=>'text-red-400', 'ring'=>'border-red-400/25',
        'bg'=>'bg-red-400/8'],
        ]; @endphp
        @foreach($overviewCards as $c)
        <div class="glass-card p-5 text-center border {{ $c['ring'] }} {{ $c['bg'] }}">
            <div class="text-3xl font-bold {{ $c['color'] }} leading-none mb-1.5">{{ $c['value'] }}</div>
            <div class="text-xs text-slate-500 font-medium">{{ $c['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── OVERALL COMPLETION BAR ── --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-slate-200">Overall Completion Rate</span>
            <span class="text-xl font-bold text-emerald-400">{{ $completionRate }}%</span>
        </div>
        <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden mb-4">
            <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-emerald-400 transition-all duration-700"
                style="width: {{ $completionRate }}%"></div>
        </div>
        {{-- Multi-segment bar --}}
        @if($totalTasks > 0)
        <div class="flex w-full h-2 rounded-full overflow-hidden gap-0.5 mb-3">
            @if($pending) <div class="bg-amber-400   transition-all"
                style="width:{{ round($pending/$totalTasks*100) }}%" title="Pending"></div> @endif
            @if($inprogress) <div class="bg-cyan-400    transition-all"
                style="width:{{ round($inprogress/$totalTasks*100) }}%" title="In Progress"></div> @endif
            @if($completed) <div class="bg-emerald-400 transition-all"
                style="width:{{ round($completed/$totalTasks*100) }}%" title="Completed"></div> @endif
            @if($cancelled) <div class="bg-slate-500   transition-all"
                style="width:{{ round($cancelled/$totalTasks*100) }}%" title="Cancelled"></div> @endif
        </div>
        <div class="flex flex-wrap gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-amber-400"></span>Pending
                {{ round($pending/$totalTasks*100) }}%</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-cyan-400"></span>In Progress
                {{ round($inprogress/$totalTasks*100) }}%</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-400"></span>Completed
                {{ round($completed/$totalTasks*100) }}%</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-slate-500"></span>Cancelled
                {{ round($cancelled/$totalTasks*100) }}%</span>
        </div>
        @endif
    </div>

    {{-- ── PRIORITY BREAKDOWN ── --}}
    <div class="glass-card p-6 mb-6">
        <h2 class="text-sm font-semibold text-slate-200 mb-4">Task Priority Breakdown</h2>
        @php
        $priorityMeta = [
        'urgent' => ['label'=>'Urgent','color'=>'bg-red-500', 'text'=>'text-red-400', 'border'=>'border-red-400/30',
        'bg'=>'bg-red-400/10'],
        'high' => ['label'=>'High', 'color'=>'bg-orange-400', 'text'=>'text-orange-400',
        'border'=>'border-orange-400/30', 'bg'=>'bg-orange-400/10'],
        'medium' => ['label'=>'Medium','color'=>'bg-yellow-400', 'text'=>'text-yellow-400',
        'border'=>'border-yellow-400/30', 'bg'=>'bg-yellow-400/10'],
        'low' => ['label'=>'Low', 'color'=>'bg-slate-500', 'text'=>'text-slate-400', 'border'=>'border-slate-500/30',
        'bg'=>'bg-slate-500/10'],
        ];
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            @foreach($priorityMeta as $key => $meta)
            @php $cnt = $priorityCounts[$key] ?? 0; @endphp
            <div class="rounded-xl p-4 border {{ $meta['border'] }} {{ $meta['bg'] }}">
                <div class="text-2xl font-bold {{ $meta['text'] }} mb-1">{{ $cnt }}</div>
                <div class="text-xs text-slate-500">{{ $meta['label'] }} Priority</div>
                @if($totalTasks > 0)
                <div class="mt-2 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                    <div class="{{ $meta['color'] }} h-full rounded-full"
                        style="width:{{ round($cnt/$totalTasks*100) }}%"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── PER-USER BREAKDOWN ── --}}
    <div class="glass-card overflow-hidden p-0 mb-6">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-200">User-wise Task Breakdown</h2>
            <span class="text-xs text-slate-500">{{ $userStats->count() }} active users</span>
        </div>

        @if($userStats->isEmpty())
        <div class="py-10 text-center text-slate-500 text-sm">No users with tasks yet.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/40 py-5!">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            User</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Total</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-amber-500/70 uppercase tracking-wider">
                            Pending</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-cyan-500/70 uppercase tracking-wider">
                            In Progress</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-emerald-500/70 uppercase tracking-wider">
                            Completed</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Cancelled</th>
                        <th
                            class="px-4 py-3.5 text-center text-xs font-semibold text-red-500/70 uppercase tracking-wider">
                            Overdue</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Progress</th>
                        <th
                            class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($userStats as $u)
                    @php $rate = $u->total_tasks > 0 ? round($u->completed_tasks / $u->total_tasks * 100) : 0; @endphp
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 overflow-hidden border border-slate-700">
                                    @if($u->profile_image)
                                    <img src="{{ Storage::url($u->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                    {{ strtoupper(substr($u->name,0,1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-200">{{ $u->name }}</p>
                                    @if($u->designation)
                                    <p class="text-xs text-slate-500">{{ $u->designation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-semibold text-slate-200">{{ $u->total_tasks }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span
                                class="text-sm font-semibold {{ $u->pending_tasks ? 'text-amber-400' : 'text-slate-600' }}">{{ $u->pending_tasks }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span
                                class="text-sm font-semibold {{ $u->inprogress_tasks ? 'text-cyan-400' : 'text-slate-600' }}">{{ $u->inprogress_tasks }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span
                                class="text-sm font-semibold {{ $u->completed_tasks ? 'text-emerald-400' : 'text-slate-600' }}">{{ $u->completed_tasks }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span
                                class="text-sm font-semibold {{ $u->cancelled_tasks ? 'text-slate-400' : 'text-slate-600' }}">{{ $u->cancelled_tasks }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span
                                class="text-sm font-semibold {{ $u->overdue_tasks ? 'text-red-400' : 'text-slate-600' }}">{{ $u->overdue_tasks }}</span>
                        </td>
                        <td class="px-5 py-3.5 min-w-36">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $rate >= 80 ? 'bg-emerald-400' : ($rate >= 40 ? 'bg-cyan-400' : 'bg-amber-400') }}"
                                        style="width:{{ $rate }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-semibold w-9 text-right {{ $rate >= 80 ? 'text-emerald-400' : ($rate >= 40 ? 'text-cyan-400' : 'text-amber-400') }}">{{ $rate }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('admin.users.report', $u) }}"
                                class="text-xs px-3 py-1 rounded-lg border border-violet-400/30 bg-violet-400/8 text-violet-400 hover:bg-violet-400/20 transition-all no-underline">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── RECENT TASKS ── --}}
    <div class="glass-card overflow-hidden p-0">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-200">Recent Tasks</h2>
            <a href="{{ route('admin.tasks.index') }}"
                class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors no-underline">View all →</a>
        </div>
        <div class="divide-y divide-slate-800">
            @forelse($recentTasks as $task)
            <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-white/[0.02] transition-colors">
                @php $dot = match($task->priority){
                'urgent'=>'bg-red-400','high'=>'bg-orange-400','medium'=>'bg-yellow-400',default=>'bg-slate-600' };
                @endphp
                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                <div class="flex-1 min-w-0">
                    <p
                        class="text-sm text-slate-200 truncate {{ $task->status==='completed'?'line-through text-slate-500':'' }}">
                        {{ $task->title }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">
                        → {{ $task->assignee?->name }} · by {{ $task->creator?->name }}
                        @if($task->due_date) · Due {{ $task->due_date->format('d M') }} @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::priorityColor($task->priority) }}">{{ ucfirst($task->priority) }}</span>
                    <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ \App\Models\Task::statusColor($task->status) }}">{{ \App\Models\Task::statusLabel($task->status) }}</span>
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-slate-500 text-sm">No tasks yet.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection