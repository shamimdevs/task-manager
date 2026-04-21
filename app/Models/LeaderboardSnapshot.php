<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardSnapshot extends Model
{
    protected $fillable = [
        'user_id', 'rank', 'completed_tasks', 'in_progress_tasks',
        'pending_tasks', 'cancelled_tasks', 'total_tasks',
        'completion_rate', 'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
