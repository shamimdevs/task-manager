<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function adminReport()
    {
        try {
            $totalTasks = Task::count();

            $statusCounts = Task::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $priorityCounts = Task::select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->pluck('total', 'priority')
                ->toArray();

            $overdue = Task::whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();

            // Per-user breakdown
            $userStats = User::withCount([
                'assignedTasks as total_tasks',
                'assignedTasks as pending_tasks'     => fn($q) => $q->where('status', 'pending'),
                'assignedTasks as inprogress_tasks'  => fn($q) => $q->where('status', 'in_progress'),
                'assignedTasks as completed_tasks'   => fn($q) => $q->where('status', 'completed'),
                'assignedTasks as cancelled_tasks'   => fn($q) => $q->where('status', 'cancelled'),
                'assignedTasks as overdue_tasks'     => fn($q) => $q->whereNotNull('due_date')
                    ->whereDate('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']),
            ])
            ->having('total_tasks', '>', 0)
            ->orderByDesc('total_tasks')
            ->get();

            // Recent 10 tasks
            $recentTasks = Task::with(['creator', 'assignee'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return view('admin.report', compact(
                'totalTasks', 'statusCounts', 'priorityCounts',
                'overdue', 'userStats', 'recentTasks'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load report: ' . $e->getMessage());
        }
    }

    public function userReport()
    {
        try {
            $user = Auth::user();

            $tasks = Task::with('creator')
                ->where('assigned_to', $user->id)
                ->orderByDesc('created_at')
                ->get();

            $total = $tasks->count();

            $statusCounts = [
                'pending'     => $tasks->where('status', 'pending')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'completed'   => $tasks->where('status', 'completed')->count(),
                'cancelled'   => $tasks->where('status', 'cancelled')->count(),
            ];

            $priorityCounts = [
                'urgent' => $tasks->where('priority', 'urgent')->count(),
                'high'   => $tasks->where('priority', 'high')->count(),
                'medium' => $tasks->where('priority', 'medium')->count(),
                'low'    => $tasks->where('priority', 'low')->count(),
            ];

            $overdue = $tasks->filter(fn($t) => $t->isOverdue())->count();

            $completionRate = $total > 0 ? round($statusCounts['completed'] / $total * 100) : 0;

            // Group tasks by status for the list
            $tasksByStatus = $tasks->groupBy('status');

            return view('report', compact(
                'user', 'tasks', 'total', 'statusCounts',
                'priorityCounts', 'overdue', 'completionRate', 'tasksByStatus'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load report: ' . $e->getMessage());
        }
    }
}
