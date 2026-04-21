<?php

namespace App\Console\Commands;

use App\Models\LeaderboardSnapshot;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLeaderboard extends Command
{
    protected $signature   = 'leaderboard:generate';
    protected $description = 'Generate the user leaderboard snapshot';

    public function handle(): int
    {
        $generatedAt = now();

        $users = User::where('role', 'user')
            ->where('status', 'active')
            ->withCount([
                'assignedTasks as total_tasks',
                'assignedTasks as completed_tasks'  => fn($q) => $q->where('status', 'completed'),
                'assignedTasks as in_progress_tasks' => fn($q) => $q->where('status', 'in_progress'),
                'assignedTasks as pending_tasks'     => fn($q) => $q->where('status', 'pending'),
                'assignedTasks as cancelled_tasks'   => fn($q) => $q->where('status', 'cancelled'),
            ])
            ->orderByDesc('completed_tasks')
            ->orderByDesc('total_tasks')
            ->get();

        DB::transaction(function () use ($users, $generatedAt) {
            LeaderboardSnapshot::query()->delete();

            foreach ($users as $index => $user) {
                $rate = $user->total_tasks > 0
                    ? round(($user->completed_tasks / $user->total_tasks) * 100, 2)
                    : 0;

                LeaderboardSnapshot::create([
                    'user_id'          => $user->id,
                    'rank'             => $index + 1,
                    'completed_tasks'  => $user->completed_tasks,
                    'in_progress_tasks' => $user->in_progress_tasks,
                    'pending_tasks'    => $user->pending_tasks,
                    'cancelled_tasks'  => $user->cancelled_tasks,
                    'total_tasks'      => $user->total_tasks,
                    'completion_rate'  => $rate,
                    'generated_at'     => $generatedAt,
                ]);
            }
        });

        $this->info("Leaderboard generated for {$users->count()} users.");
        return self::SUCCESS;
    }
}
