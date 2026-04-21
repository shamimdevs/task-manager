<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rank');
            $table->unsignedInteger('completed_tasks')->default(0);
            $table->unsignedInteger('in_progress_tasks')->default(0);
            $table->unsignedInteger('pending_tasks')->default(0);
            $table->unsignedInteger('cancelled_tasks')->default(0);
            $table->unsignedInteger('total_tasks')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_snapshots');
    }
};
