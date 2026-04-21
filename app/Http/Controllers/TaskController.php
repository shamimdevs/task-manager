<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search   = $request->input('search', '');
            $status   = $request->input('status', '');
            $priority = $request->input('priority', '');

            $tasks = Task::with(['creator', 'assignee'])
                ->when($search, function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('assignee', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })
                ->when($status, fn($q) => $q->where('status', $status))
                ->when($priority, fn($q) => $q->where('priority', $priority))
                ->orderBy('order')
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();

            return view('admin.tasks.index', compact('tasks', 'search', 'status', 'priority'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load tasks: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::where('status', 'active')->orderBy('name')->get();
            return view('admin.tasks.create', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'assigned_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'order'       => 'nullable|integer|min:0',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,zip|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $attachmentPath = null;
                if ($request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')->store('task-attachments', 'public');
                }

                Task::create([
                    'created_by'  => Auth::id(),
                    'assigned_to' => $request->assigned_to,
                    'title'       => $request->title,
                    'description' => $request->description,
                    'status'      => $request->status,
                    'priority'    => $request->priority,
                    'due_date'    => $request->due_date,
                    'order'       => $request->order ?? 0,
                    'attachment'  => $attachmentPath,
                ]);
            });

            return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    public function edit(Task $task)
    {
        try {
            $users = User::where('status', 'active')->orderBy('name')->get();
            return view('admin.tasks.edit', compact('task', 'users'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'assigned_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'order'       => 'nullable|integer|min:0',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,zip|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request, $task) {
                $data = [
                    'assigned_to' => $request->assigned_to,
                    'title'       => $request->title,
                    'description' => $request->description,
                    'status'      => $request->status,
                    'priority'    => $request->priority,
                    'due_date'    => $request->due_date,
                    'order'       => $request->order ?? $task->order,
                ];

                if ($request->hasFile('attachment')) {
                    if ($task->attachment) {
                        Storage::disk('public')->delete($task->attachment);
                    }
                    $data['attachment'] = $request->file('attachment')->store('task-attachments', 'public');
                }

                $task->update($data);
            });

            return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    public function destroy(Task $task)
    {
        try {
            DB::transaction(function () use ($task) {
                if ($task->attachment) {
                    Storage::disk('public')->delete($task->attachment);
                }
                $task->delete();
            });

            return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }
}