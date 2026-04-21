<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'assigned_to', 'title', 'description',
        'status', 'priority', 'due_date', 'order', 'attachment',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public static function statusColor(string $status): string
    {
        return match($status) {
            'pending'     => 'text-amber-400 bg-amber-400/10 border-amber-400/30',
            'in_progress' => 'text-cyan-400 bg-cyan-400/10 border-cyan-400/30',
            'completed'   => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
            'cancelled'   => 'text-slate-400 bg-slate-400/10 border-slate-400/30',
            default       => 'text-slate-400 bg-slate-400/10 border-slate-400/30',
        };
    }

    public static function priorityColor(string $priority): string
    {
        return match($priority) {
            'low'    => 'text-slate-400 bg-slate-400/10 border-slate-400/30',
            'medium' => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
            'high'   => 'text-orange-400 bg-orange-400/10 border-orange-400/30',
            'urgent' => 'text-red-400 bg-red-400/10 border-red-400/30',
            default  => 'text-slate-400 bg-slate-400/10 border-slate-400/30',
        };
    }

    public static function statusLabel(string $status): string
    {
        return match($status) {
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($status),
        };
    }
}
