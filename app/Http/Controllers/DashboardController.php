<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = [
                'total_tasks'   => Task::count(),
                'in_progress'   => Task::where('status', 'in_progress')->count(),
                'completed'     => Task::where('status', 'completed')->count(),
                'total_users'   => User::count(),
            ];
            $recentTasks = Task::with(['assignee', 'creator'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        } else {
            $myTasks = Task::where('assigned_to', $user->id);
            $stats = [
                'my_tasks'    => (clone $myTasks)->count(),
                'in_progress' => (clone $myTasks)->where('status', 'in_progress')->count(),
                'completed'   => (clone $myTasks)->where('status', 'completed')->count(),
                'pending'     => (clone $myTasks)->where('status', 'pending')->count(),
            ];
            $recentTasks = Task::with('creator')
                ->where('assigned_to', $user->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('user', 'stats', 'recentTasks'));
    }
}
