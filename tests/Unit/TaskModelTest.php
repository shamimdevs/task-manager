<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    // ── isOverdue() ──────────────────────────────────────────────

    public function test_task_is_overdue_when_due_date_is_past_and_status_is_pending(): void
    {
        $task = Task::factory()->overdue()->make();

        $this->assertTrue($task->isOverdue());
    }

    public function test_task_is_not_overdue_when_due_date_is_in_future(): void
    {
        $task = Task::factory()->make([
            'due_date' => now()->addDays(5)->toDateString(),
            'status'   => 'pending',
        ]);

        $this->assertFalse($task->isOverdue());
    }

    public function test_completed_task_is_not_overdue_even_if_due_date_is_past(): void
    {
        $task = Task::factory()->make([
            'due_date' => now()->subDays(3)->toDateString(),
            'status'   => 'completed',
        ]);

        $this->assertFalse($task->isOverdue());
    }

    public function test_cancelled_task_is_not_overdue_even_if_due_date_is_past(): void
    {
        $task = Task::factory()->make([
            'due_date' => now()->subDays(3)->toDateString(),
            'status'   => 'cancelled',
        ]);

        $this->assertFalse($task->isOverdue());
    }

    public function test_task_without_due_date_is_never_overdue(): void
    {
        $task = Task::factory()->make([
            'due_date' => null,
            'status'   => 'pending',
        ]);

        $this->assertFalse($task->isOverdue());
    }

    // ── statusLabel() ────────────────────────────────────────────

    public function test_status_label_returns_correct_labels(): void
    {
        $this->assertSame('Pending',     Task::statusLabel('pending'));
        $this->assertSame('In Progress', Task::statusLabel('in_progress'));
        $this->assertSame('Completed',   Task::statusLabel('completed'));
        $this->assertSame('Cancelled',   Task::statusLabel('cancelled'));
    }

    public function test_status_label_returns_ucfirst_for_unknown_status(): void
    {
        $this->assertSame('Unknown', Task::statusLabel('unknown'));
    }

    // ── statusColor() ────────────────────────────────────────────

    public function test_status_color_returns_correct_css_classes(): void
    {
        $this->assertStringContainsString('amber',   Task::statusColor('pending'));
        $this->assertStringContainsString('cyan',    Task::statusColor('in_progress'));
        $this->assertStringContainsString('emerald', Task::statusColor('completed'));
        $this->assertStringContainsString('slate',   Task::statusColor('cancelled'));
    }

    // ── priorityColor() ──────────────────────────────────────────

    public function test_priority_color_returns_correct_css_classes(): void
    {
        $this->assertStringContainsString('slate',  Task::priorityColor('low'));
        $this->assertStringContainsString('yellow', Task::priorityColor('medium'));
        $this->assertStringContainsString('orange', Task::priorityColor('high'));
        $this->assertStringContainsString('red',    Task::priorityColor('urgent'));
    }

    // ── Relationships ─────────────────────────────────────────────

    public function test_task_belongs_to_creator(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $task = Task::factory()->create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $task->creator);
        $this->assertEquals($admin->id, $task->creator->id);
    }

    public function test_task_belongs_to_assignee(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $task = Task::factory()->create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $task->assignee);
        $this->assertEquals($user->id, $task->assignee->id);
    }

    public function test_user_has_many_assigned_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        Task::factory()->count(3)->create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
        ]);

        $this->assertCount(3, $user->assignedTasks);
    }

    public function test_user_has_many_created_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        Task::factory()->count(2)->create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
        ]);

        $this->assertCount(2, $admin->createdTasks);
    }

    // ── Fillable / Casting ───────────────────────────────────────

    public function test_task_casts_due_date_to_carbon(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $task = Task::factory()->create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
            'due_date'    => '2025-12-31',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->due_date);
    }

    public function test_task_can_be_created_with_fillable_attributes(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $task = Task::create([
            'created_by'  => $admin->id,
            'assigned_to' => $user->id,
            'title'       => 'Test Task',
            'description' => 'Test description',
            'status'      => 'pending',
            'priority'    => 'high',
            'due_date'    => now()->addDays(7)->toDateString(),
            'order'       => 1,
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task', 'priority' => 'high']);
    }
}
