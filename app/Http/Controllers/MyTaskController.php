<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyTaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::with('creator')
                ->where('assigned_to', Auth::id())
                ->orderBy('order')
                ->orderByDesc('created_at')
                ->get();

            return view('tasks.index', compact('tasks'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load tasks: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        if ($task->assigned_to !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        try {
            DB::transaction(function () use ($request, $task) {
                $task->update(['status' => $request->status]);
            });

            return back()->with('success', 'Task status updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
