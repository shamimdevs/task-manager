@extends('layouts.app')
@section('title', 'Create Task')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <div class="mb-8">
        <a href="{{ route('admin.tasks.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-300 transition-colors no-underline mb-3">
            ← Back to Tasks
        </a>
        <h1 class="text-2xl font-bold text-slate-100">Create New Task</h1>
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

        <form method="POST" action="{{ route('admin.tasks.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="flex flex-col gap-5">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Title <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="input-dark w-full" placeholder="Enter task title">
                </div>

                {{-- Assigned To --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Assign To <span class="text-red-400">*</span></label>
                    <select name="assigned_to" class="input-dark w-full cursor-pointer" required>
                        <option value="">— Select User —</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->designation ? '('.$user->designation.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="input-dark w-full resize-y"
                        placeholder="Task details, requirements, notes...">{{ old('description') }}</textarea>
                </div>

                {{-- Priority + Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Priority</label>
                        <select name="priority" class="input-dark w-full cursor-pointer">
                            @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', 'medium') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Status</label>
                        <select name="status" class="input-dark w-full cursor-pointer">
                            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', 'pending') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Due Date + Order --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               class="input-dark w-full cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1.5">Order</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                               class="input-dark w-full" placeholder="0">
                    </div>
                </div>

                {{-- Attachment --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1.5">Attachment <span class="text-slate-600 font-normal">(optional)</span></label>
                    <label class="flex flex-col items-center justify-center gap-2 border border-dashed border-slate-700 rounded-xl p-6 cursor-pointer hover:border-cyan-400/50 hover:bg-cyan-400/5 transition-all group">
                        <svg class="w-8 h-8 text-slate-600 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span id="attachLabel" class="text-sm text-slate-500 group-hover:text-slate-400">Click to upload file</span>
                        <span class="text-xs text-slate-600">JPG, PNG, PDF, DOC, XLSX, ZIP — max 5MB</span>
                        <input type="file" name="attachment" class="hidden"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.zip"
                               onchange="document.getElementById('attachLabel').textContent = this.files[0]?.name || 'Click to upload file'">
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full mt-1">Create Task</button>
            </div>
        </form>
    </div>
</div>
@endsection
