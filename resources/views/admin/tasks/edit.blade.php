@extends('layouts.app')
@section('title', 'Edit Task')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <div class="mb-8">
        <a href="{{ route('admin.tasks.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-300 transition-colors no-underline mb-3">
            ← Back to Tasks
        </a>
        <h1 class="text-2xl font-bold text-slate-100">Edit Task</h1>
        <p class="text-sm text-slate-500 mt-0.5">Created by {{ $task->creator?->name }} · {{ $task->created_at->format('d M Y') }}</p>
    </div>

    <div class="glass-card p-6">
        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-400 space-y-1 pl-4 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="flex flex-col gap-5">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Title <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                           class="input-dark w-full">
                </div>

                {{-- Assigned To --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Assign To <span class="text-red-400">*</span></label>
                    <select name="assigned_to" class="input-dark w-full cursor-pointer" required>
                        <option value="">— Select User —</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->designation ? '('.$user->designation.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="input-dark w-full resize-y"
                        placeholder="Task details...">{{ old('description', $task->description) }}</textarea>
                </div>

                {{-- Priority + Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Priority</label>
                        <select name="priority" class="input-dark w-full cursor-pointer">
                            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $task->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Status</label>
                        <select name="status" class="input-dark w-full cursor-pointer">
                            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Due Date + Order --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                               class="input-dark w-full cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Order</label>
                        <input type="number" name="order" value="{{ old('order', $task->order) }}" min="0"
                               class="input-dark w-full">
                    </div>
                </div>

                {{-- Current Attachment --}}
                @if($task->attachment)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/40 border border-slate-700/50">
                    <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.41 17.41a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-slate-400 mb-0.5">Current attachment</p>
                        <a href="{{ Storage::url($task->attachment) }}" target="_blank"
                           class="text-sm text-cyan-400 hover:text-cyan-300 truncate block no-underline">
                            {{ basename($task->attachment) }}
                        </a>
                    </div>
                </div>
                @endif

                {{-- New Attachment --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">
                        {{ $task->attachment ? 'Replace Attachment' : 'Attachment' }}
                        <span class="text-slate-600 font-normal">(optional)</span>
                    </label>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-700 rounded-xl p-6 cursor-pointer hover:border-cyan-400/50 hover:bg-cyan-400/5 transition-all group">
                        <svg class="w-8 h-8 text-slate-600 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span id="attachLabel" class="text-sm text-slate-500 group-hover:text-slate-400">Click to upload</span>
                        <span class="text-xs text-slate-600">JPG, PNG, PDF, DOC, XLSX, ZIP — max 5MB</span>
                        <input type="file" name="attachment" class="hidden"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.zip"
                               onchange="document.getElementById('attachLabel').textContent = this.files[0]?.name || 'Click to upload'">
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full mt-1">Update Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
