@extends('layouts.app')
@section('title', 'Leaderboard')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                style="background:linear-gradient(135deg,#f59e0b,#ef4444);box-shadow:0 0 16px #f59e0b33">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-2xl font-bold leading-tight"
                    style="background:linear-gradient(135deg,#f59e0b,#00d4ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    User Leaderboard
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 truncate">
                    @if($generatedAt)
                    Updated: <span
                        class="text-slate-400">{{ \Carbon\Carbon::parse($generatedAt)->format('d M Y, h:i A') }}</span>
                    @else
                    Not generated yet
                    @endif
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.leaderboard.generate') }}" class="flex-shrink-0">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all cursor-pointer whitespace-nowrap"
                style="background:linear-gradient(135deg,#7c3aed22,#00d4ff11);border:1px solid #7c3aed55;color:#a78bfa;">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Generate Now
            </button>
        </form>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div
        class="flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-4 py-3 mb-5 text-sm text-emerald-400">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($entries->isEmpty())
    {{-- ── Empty State ──────────────────────────────────── --}}
    <div class="glass-card px-6 py-10 sm:py-16 text-center">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
            style="background:linear-gradient(135deg,#f59e0b11,#ef444411);border:1px solid #f59e0b33;">
            <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path d="M16 8v8m-4-5v5m-4-2v2M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <p class="text-slate-300 font-semibold">No leaderboard data yet</p>
        <p class="text-slate-600 text-sm mt-1 mb-5">Rankings are generated daily at midnight or manually by admin.</p>
        <form method="POST" action="{{ route('admin.leaderboard.generate') }}" class="inline">
            @csrf
            <button type="submit" class="btn-primary text-sm px-6">Generate Now</button>
        </form>
    </div>

    @else

    {{-- ── Summary Stats ────────────────────────────────── --}}
    @php $leader = $top3->firstWhere('rank', 1); @endphp
    <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
        <div class="glass-card px-3 sm:px-4 py-3 text-center">
            <p class="text-xl sm:text-2xl font-bold text-amber-400">{{ $totalUsers }}</p>
            <p class="text-xs text-slate-500 mt-0.5 leading-tight">Total<br class="sm:hidden"> Ranked</p>
        </div>
        <div class="glass-card px-3 sm:px-4 py-3 text-center">
            <p class="text-xl sm:text-2xl font-bold text-cyan-400">{{ $leader?->completed_tasks ?? '—' }}</p>
            <p class="text-xs text-slate-500 mt-0.5 leading-tight">Top<br class="sm:hidden"> Done</p>
        </div>
        <div class="glass-card px-3 sm:px-4 py-3 text-center">
            <p class="text-xl sm:text-2xl font-bold text-violet-400">{{ number_format($avgRate, 1) }}%</p>
            <p class="text-xs text-slate-500 mt-0.5 leading-tight">Avg<br class="sm:hidden"> Rate</p>
        </div>
    </div>

    {{-- ── Top 3 Podium ─────────────────────────────────── --}}
    @if($top3->count() >= 2)
    @php
    $rank1 = $top3->firstWhere('rank', 1);
    $rank2 = $top3->firstWhere('rank', 2);
    $rank3 = $top3->firstWhere('rank', 3);
    @endphp

    {{--
        PODIUM LAYOUT:
        Desktop: 2nd (left, medium height) | 1st (center, tallest) | 3rd (right, shortest)
        Mobile:  Stacked vertical list (1st → 2nd → 3rd)
    --}}

    {{-- ── MOBILE: vertical stack ── --}}
    <div class="flex flex-col gap-2 mb-5 sm:hidden">
        @foreach([
        ['entry' => $rank1, 'medal' => '🥇', 'color' => '#f59e0b', 'label' => '1st Place'],
        ['entry' => $rank2, 'medal' => '🥈', 'color' => '#94a3b8', 'label' => '2nd Place'],
        ['entry' => $rank3, 'medal' => '🥉', 'color' => '#cd7c32', 'label' => '3rd Place'],
        ] as $item)
        @if($item['entry'])
        <div class="glass-card flex items-center gap-3 px-4 py-3 rounded-xl"
            style="border-color:{{ $item['color'] }}55;box-shadow:0 4px 16px {{ $item['color'] }}1a;">
            <span class="text-2xl leading-none flex-shrink-0">{{ $item['medal'] }}</span>
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 overflow-hidden"
                style="background:{{ $item['color'] }}22;border:2px solid {{ $item['color'] }}55;color:{{ $item['color'] }};">
                @if($item['entry']->user->profile_image)
                <img src="{{ Storage::url($item['entry']->user->profile_image) }}" class="w-full h-full object-cover"
                    alt="{{ $item['entry']->user->name }}">
                @else
                {{ strtoupper(substr($item['entry']->user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-200 text-sm truncate">{{ $item['entry']->user->name }}</p>
                <p class="text-xs mt-0.5" style="color:{{ $item['color'] }}">
                    {{ $item['label'] }} &middot; ✓ {{ $item['entry']->completed_tasks }} &middot;
                    {{ $item['entry']->completion_rate }}%
                </p>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- ── DESKTOP: podium (2nd | 1st | 3rd) ── --}}
    <div class="hidden sm:flex items-end gap-3 mb-6" style="min-height:220px;">

        {{-- 2nd Place — medium podium --}}
        <div class="flex-1">
            @if($rank2)
            <div class="glass-card flex flex-col items-center text-center px-3 py-4 rounded-xl h-full"
                style="border-color:#94a3b855;box-shadow:0 4px 20px #94a3b81a;min-height:170px;">
                <span class="text-2xl mb-2 leading-none">🥈</span>
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-base font-bold mb-2 flex-shrink-0 overflow-hidden"
                    style="background:#94a3b822;border:2px solid #94a3b855;color:#94a3b8;box-shadow:0 0 10px #94a3b822;">
                    @if($rank2->user->profile_image)
                    <img src="{{ Storage::url($rank2->user->profile_image) }}" class="w-full h-full object-cover"
                        alt="{{ $rank2->user->name }}">
                    @else
                    {{ strtoupper(substr($rank2->user->name, 0, 1)) }}
                    @endif
                </div>
                <p class="font-semibold text-slate-200 text-sm truncate w-full">{{ $rank2->user->name }}</p>
                @if($rank2->user->designation)
                <p class="text-xs text-slate-600 truncate w-full mt-0.5">{{ $rank2->user->designation }}</p>
                @endif
                <p class="text-xs font-bold mt-1" style="color:#94a3b8">2nd Place</p>
                <div class="flex items-center justify-center gap-1.5 mt-2">
                    <span class="text-xs text-emerald-400 font-semibold">✓ {{ $rank2->completed_tasks }}</span>
                    <span class="text-slate-700 text-xs">·</span>
                    <span class="text-xs text-slate-400">{{ $rank2->completion_rate }}%</span>
                </div>
            </div>
            @endif
        </div>

        {{-- 1st Place — tallest podium (scale up) --}}
        <div class="flex-1" style="margin-bottom: 0;">
            @if($rank1)
            <div class="glass-card flex flex-col items-center text-center px-3 py-5 rounded-xl"
                style="border-color:#f59e0b88;box-shadow:0 8px 32px #f59e0b2a,0 0 0 1px #f59e0b22;min-height:220px;background:linear-gradient(160deg,#f59e0b0a 0%,transparent 60%);">
                {{-- Crown glow --}}
                <div class="relative mb-2">
                    <span class="text-3xl leading-none">🥇</span>
                    <div class="absolute inset-0 blur-md opacity-60"
                        style="background:radial-gradient(circle,#f59e0b55,transparent 70%);pointer-events:none;"></div>
                </div>
                <div class="relative w-14 h-14 rounded-full flex items-center justify-center text-lg font-bold mb-2 flex-shrink-0 overflow-hidden"
                    style="background:#f59e0b22;border:2.5px solid #f59e0b88;color:#f59e0b;box-shadow:0 0 16px #f59e0b44;">
                    @if($rank1->user->profile_image)
                    <img src="{{ Storage::url($rank1->user->profile_image) }}" class="w-full h-full object-cover"
                        alt="{{ $rank1->user->name }}">
                    @else
                    {{ strtoupper(substr($rank1->user->name, 0, 1)) }}
                    @endif
                    {{-- Ring glow --}}
                    <div class="absolute inset-0 rounded-full pointer-events-none"
                        style="box-shadow:0 0 0 3px #f59e0b33;"></div>
                </div>
                <p class="font-bold text-slate-100 text-base truncate w-full">{{ $rank1->user->name }}</p>
                @if($rank1->user->designation)
                <p class="text-xs text-slate-500 truncate w-full mt-0.5">{{ $rank1->user->designation }}</p>
                @endif
                <p class="text-xs font-bold mt-1.5" style="color:#f59e0b;letter-spacing:0.04em;">🏆 1st Place</p>
                <div class="flex items-center justify-center gap-1.5 mt-2">
                    <span class="text-sm text-emerald-400 font-bold">✓ {{ $rank1->completed_tasks }}</span>
                    <span class="text-slate-700 text-xs">·</span>
                    <span class="text-sm font-semibold" style="color:#f59e0b">{{ $rank1->completion_rate }}%</span>
                </div>
                {{-- Score badge --}}
                <div class="mt-3 px-3 py-1 rounded-full text-xs font-semibold"
                    style="background:#f59e0b18;border:1px solid #f59e0b44;color:#fbbf24;">
                    Top Performer
                </div>
            </div>
            @endif
        </div>

        {{-- 3rd Place — shortest podium --}}
        <div class="flex-1" style="padding-top: 50px;">
            @if($rank3)
            <div class="glass-card flex flex-col items-center text-center px-3 py-4 rounded-xl h-full"
                style="border-color:#cd7c3255;box-shadow:0 4px 20px #cd7c321a;min-height:150px;">
                <span class="text-2xl mb-2 leading-none">🥉</span>
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-base font-bold mb-2 flex-shrink-0 overflow-hidden"
                    style="background:#cd7c3222;border:2px solid #cd7c3255;color:#cd7c32;box-shadow:0 0 10px #cd7c3222;">
                    @if($rank3->user->profile_image)
                    <img src="{{ Storage::url($rank3->user->profile_image) }}" class="w-full h-full object-cover"
                        alt="{{ $rank3->user->name }}">
                    @else
                    {{ strtoupper(substr($rank3->user->name, 0, 1)) }}
                    @endif
                </div>
                <p class="font-semibold text-slate-200 text-sm truncate w-full">{{ $rank3->user->name }}</p>
                @if($rank3->user->designation)
                <p class="text-xs text-slate-600 truncate w-full mt-0.5">{{ $rank3->user->designation }}</p>
                @endif
                <p class="text-xs font-bold mt-1" style="color:#cd7c32">3rd Place</p>
                <div class="flex items-center justify-center gap-1.5 mt-2">
                    <span class="text-xs text-emerald-400 font-semibold">✓ {{ $rank3->completed_tasks }}</span>
                    <span class="text-slate-700 text-xs">·</span>
                    <span class="text-xs text-slate-400">{{ $rank3->completion_rate }}%</span>
                </div>
            </div>
            @endif
        </div>

    </div>
    {{-- ── End Podium ── --}}
    @endif

    {{-- ── Rankings Table ───────────────────────────────── --}}
    <div class="glass-card overflow-hidden">

        <div class="px-4 py-3 flex items-center justify-between border-b border-slate-800/60"
            style="background:rgba(255,255,255,0.015)">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Full Rankings</p>
            <p class="text-xs text-slate-600">{{ $entries->total() }} users</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid #1e2433;background:rgba(255,255,255,0.02)">
                        <th
                            class="px-2 sm:px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-10 sm:w-14">
                            #</th>
                        <th
                            class="px-2 sm:px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            User</th>
                        <th
                            class="px-2 sm:px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <span class="text-emerald-500">✓</span>
                            <span class="hidden sm:inline"> Done</span>
                        </th>
                        <th
                            class="px-2 sm:px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">
                            Active</th>
                        <th
                            class="px-2 sm:px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">
                            Pending</th>
                        <th
                            class="px-2 sm:px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">
                            Total</th>
                        <th
                            class="px-2 sm:px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    @php
                    $rowStyle = match($entry->rank) {
                    1 => 'background:linear-gradient(90deg,#f59e0b08,transparent);border-left:2px solid #f59e0b55;',
                    2 => 'background:linear-gradient(90deg,#94a3b808,transparent);border-left:2px solid #94a3b855;',
                    3 => 'background:linear-gradient(90deg,#cd7c3208,transparent);border-left:2px solid #cd7c3255;',
                    default => 'border-left:2px solid transparent;',
                    };
                    $rankIcon = match($entry->rank) {
                    1 => '🥇', 2 => '🥈', 3 => '🥉', default => null,
                    };
                    $avatarColor = match($entry->rank) {
                    1 => '#f59e0b', 2 => '#94a3b8', 3 => '#cd7c32', default => '#00d4ff',
                    };
                    $rateColor = $entry->completion_rate >= 75 ? '#34d399' : ($entry->completion_rate >= 40 ? '#00d4ff'
                    : '#94a3b8');
                    @endphp
                    <tr class="hover:bg-white/[0.025] transition-colors"
                        style="{{ $rowStyle }}border-bottom:1px solid #1e243344;">

                        {{-- Rank --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5">
                            @if($rankIcon)
                            <span class="text-base sm:text-lg leading-none">{{ $rankIcon }}</span>
                            @else
                            <span class="text-slate-600 font-mono text-xs font-semibold">#{{ $entry->rank }}</span>
                            @endif
                        </td>

                        {{-- User --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 overflow-hidden"
                                    style="background:{{ $avatarColor }}22;border:1.5px solid {{ $avatarColor }}55;color:{{ $avatarColor }}">
                                    @if($entry->user->profile_image)
                                    <img src="{{ Storage::url($entry->user->profile_image) }}"
                                        class="w-full h-full object-cover">
                                    @else
                                    {{ strtoupper(substr($entry->user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-200 truncate text-xs sm:text-sm">
                                        {{ $entry->user->name }}</p>
                                    @if($entry->user->designation)
                                    <p class="text-xs text-slate-600 truncate hidden sm:block">
                                        {{ $entry->user->designation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Completed --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-lg text-xs font-bold
                                         bg-emerald-400/10 text-emerald-400 border border-emerald-400/20">
                                {{ $entry->completed_tasks }}
                            </span>
                        </td>

                        {{-- In Progress --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5 text-center hidden sm:table-cell">
                            <span class="text-cyan-400 font-semibold text-sm">{{ $entry->in_progress_tasks }}</span>
                        </td>

                        {{-- Pending --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5 text-center hidden md:table-cell">
                            <span class="text-amber-400 font-semibold text-sm">{{ $entry->pending_tasks }}</span>
                        </td>

                        {{-- Total --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5 text-center hidden sm:table-cell">
                            <span class="text-slate-500 text-sm">{{ $entry->total_tasks }}</span>
                        </td>

                        {{-- Completion Rate --}}
                        <td class="px-2 sm:px-4 py-3 sm:py-3.5">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <div class="flex-1 h-1.5 rounded-full overflow-hidden min-w-[30px]"
                                    style="background:#1e2433">
                                    <div class="h-full rounded-full"
                                        style="width:{{ $entry->completion_rate }}%;background:linear-gradient(90deg,#00d4ff,#7c3aed);">
                                    </div>
                                </div>
                                <span class="text-xs font-bold flex-shrink-0 w-8 sm:w-10 text-right"
                                    style="color:{{ $rateColor }}">
                                    {{ $entry->completion_rate }}%
                                </span>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($entries->hasPages())
        <div class="px-4 py-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800/60"
            style="background:rgba(255,255,255,0.01)">
            <p class="text-xs text-slate-500">
                Showing <span
                    class="text-slate-400 font-medium">{{ $entries->firstItem() }}–{{ $entries->lastItem() }}</span>
                of <span class="text-slate-400 font-medium">{{ $entries->total() }}</span> users
            </p>
            {{ $entries->links() }}
        </div>
        @endif

    </div>
    @endif

</div>
@endsection