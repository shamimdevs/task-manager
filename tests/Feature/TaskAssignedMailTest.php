<?php

namespace Tests\Feature;

use App\Mail\TaskAssignedMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskAssignedMailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->admin = User::factory()->admin()->create();
        $this->user  = User::factory()->create();
    }

    // ── Create: toggle ON ────────────────────────────────────────

    public function test_email_is_sent_when_toggle_is_on(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'New Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'medium',
                 'send_mail'   => '1',
             ]);

        Mail::assertSent(TaskAssignedMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function test_email_is_sent_to_correct_assignee(): void
    {
        $otherUser = User::factory()->create();

        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Specific Task',
                 'assigned_to' => $otherUser->id,
                 'status'      => 'pending',
                 'priority'    => 'high',
                 'send_mail'   => '1',
             ]);

        Mail::assertSent(TaskAssignedMail::class, fn($mail) => $mail->hasTo($otherUser->email));
        Mail::assertNotSent(TaskAssignedMail::class, fn($mail) => $mail->hasTo($this->user->email));
    }

    public function test_email_contains_correct_task_data(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Fix Critical Bug',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'urgent',
                 'send_mail'   => '1',
             ]);

        Mail::assertSent(TaskAssignedMail::class, function ($mail) {
            return $mail->task->title === 'Fix Critical Bug'
                && $mail->assignee->id === $this->user->id;
        });
    }

    // ── Create: toggle OFF ───────────────────────────────────────

    public function test_email_is_not_sent_when_toggle_is_off(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Silent Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'low',
                 'send_mail'   => '0',
             ]);

        Mail::assertNotSent(TaskAssignedMail::class);
    }

    public function test_email_is_not_sent_when_toggle_is_absent(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'No Toggle Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'low',
             ]);

        Mail::assertNotSent(TaskAssignedMail::class);
    }

    public function test_only_one_email_sent_per_task_creation(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'One Email Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'medium',
                 'send_mail'   => '1',
             ]);

        Mail::assertSentCount(1);
    }

    // ── Update: reassignment ─────────────────────────────────────

    public function test_email_is_sent_when_task_is_reassigned(): void
    {
        $newUser = User::factory()->create();

        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->put(route('admin.tasks.update', $task), [
                 'title'       => $task->title,
                 'assigned_to' => $newUser->id,
                 'status'      => $task->status,
                 'priority'    => $task->priority,
             ]);

        Mail::assertSent(TaskAssignedMail::class, fn($mail) => $mail->hasTo($newUser->email));
    }

    public function test_email_is_not_sent_when_assignee_unchanged_on_update(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->put(route('admin.tasks.update', $task), [
                 'title'       => 'Updated Title',
                 'assigned_to' => $this->user->id,
                 'status'      => 'in_progress',
                 'priority'    => $task->priority,
             ]);

        Mail::assertNotSent(TaskAssignedMail::class);
    }

    public function test_reassignment_email_goes_to_new_assignee_not_old(): void
    {
        $newUser = User::factory()->create();

        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->put(route('admin.tasks.update', $task), [
                 'title'       => $task->title,
                 'assigned_to' => $newUser->id,
                 'status'      => $task->status,
                 'priority'    => $task->priority,
             ]);

        Mail::assertSent(TaskAssignedMail::class, fn($mail) => $mail->hasTo($newUser->email));
        Mail::assertNotSent(TaskAssignedMail::class, fn($mail) => $mail->hasTo($this->user->email));
    }
}
