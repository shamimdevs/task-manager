<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>New Task Assigned</title>
<style>
  body { margin:0; padding:0; background:#060810; font-family:'Segoe UI',Arial,sans-serif; }
  .wrapper { max-width:560px; margin:40px auto; padding:0 16px; }
  .card { background:#0d1117; border:1px solid #1e2433; border-radius:16px; overflow:hidden; }
  .header { background:linear-gradient(135deg,#00d4ff22,#7c3aed22); border-bottom:1px solid #1e2433; padding:32px 40px 28px; text-align:center; }
  .logo { width:48px; height:48px; background:linear-gradient(135deg,#00d4ff,#7c3aed); border-radius:12px; margin:0 auto 16px; display:inline-flex; align-items:center; justify-content:center; }
  .header-title { font-size:22px; font-weight:700; color:#f1f5f9; margin:0; }
  .header-sub { font-size:14px; color:#8b9ab0; margin:6px 0 0; }
  .body { padding:36px 40px; }
  h2 { font-size:18px; font-weight:600; color:#f1f5f9; margin:0 0 16px; }
  p { font-size:15px; color:#8b9ab0; line-height:1.7; margin:0 0 20px; }
  .task-card { background:#ffffff06; border:1px solid #1e2433; border-radius:12px; padding:20px 24px; margin:20px 0 28px; }
  .task-title { font-size:17px; font-weight:700; color:#e2e8f0; margin:0 0 14px; }
  .meta-row { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
  .badge { display:inline-block; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:.3px; }
  .badge-pending  { background:#f59e0b1a; color:#fbbf24; border:1px solid #f59e0b33; }
  .badge-in_progress { background:#00d4ff1a; color:#22d3ee; border:1px solid #00d4ff33; }
  .badge-completed { background:#10b9811a; color:#34d399; border:1px solid #10b98133; }
  .badge-cancelled { background:#64748b1a; color:#94a3b8; border:1px solid #64748b33; }
  .badge-low    { background:#64748b1a; color:#94a3b8; border:1px solid #64748b33; }
  .badge-medium { background:#eab3081a; color:#fcd34d; border:1px solid #eab30833; }
  .badge-high   { background:#f974161a; color:#fb923c; border:1px solid #f9741633; }
  .badge-urgent { background:#ef44441a; color:#f87171; border:1px solid #ef444433; }
  .meta-item { font-size:13px; color:#8b9ab0; margin-bottom:6px; }
  .meta-item span { color:#cbd5e1; font-weight:500; }
  .divider { height:1px; background:#1e2433; margin:14px 0; }
  .desc { font-size:14px; color:#8b9ab0; line-height:1.7; white-space:pre-line; }
  .btn { display:block; text-align:center; background:linear-gradient(135deg,#00d4ff,#7c3aed); color:#fff !important; text-decoration:none; font-weight:600; font-size:15px; padding:14px 32px; border-radius:10px; margin:28px 0 0; }
  .footer { text-align:center; padding:20px 40px 28px; font-size:13px; color:#4b5563; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    {{-- Header --}}
    <div class="header">
      <div class="logo">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
          <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
      </div>
      <div class="header-title">{{ config('app.name') }}</div>
      <div class="header-sub">Task Management System</div>
    </div>

    {{-- Body --}}
    <div class="body">
      <h2>You have a new task! 🎯</h2>
      <p>Hi <strong style="color:#e2e8f0;">{{ $assignee->name }}</strong>,</p>
      <p>
        <strong style="color:#e2e8f0;">{{ $task->creator->name }}</strong>
        has assigned a new task to you. Here are the details:
      </p>

      {{-- Task Card --}}
      <div class="task-card">
        <div class="task-title">{{ $task->title }}</div>

        <div class="meta-row">
          <span class="badge badge-{{ $task->status }}">
            {{ \App\Models\Task::statusLabel($task->status) }}
          </span>
          <span class="badge badge-{{ $task->priority }}">
            {{ ucfirst($task->priority) }} Priority
          </span>
        </div>

        <div class="meta-item">
          📅 Due Date:&nbsp;
          <span>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</span>
        </div>
        <div class="meta-item">
          👤 Assigned by:&nbsp;
          <span>{{ $task->creator->name }}</span>
        </div>

        @if($task->description)
          <div class="divider"></div>
          <div class="desc">{{ $task->description }}</div>
        @endif
      </div>

      <p style="margin:0;">Log in to your dashboard to view the full task details and start working on it.</p>

      <a href="{{ url('/dashboard') }}" class="btn">Go to My Tasks →</a>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }} &nbsp;·&nbsp; This is an automated email, please do not reply.
    </div>
  </div>
</div>
</body>
</html>
