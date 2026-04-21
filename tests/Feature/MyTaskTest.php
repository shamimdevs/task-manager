<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyTaskTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->user  = User::factory()->create();
    }

    // ── Access Control ───────────────────────────────────────────

    public function test_guest_cannot_view_my_tasks(): void
    {
        $this->get(route('my-tasks.index'))
             ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_my_tasks(): void
    {
        $this->actingAs($this->user)
             ->get(route('my-tasks.index'))
             ->assertOk()
             ->assertViewIs('tasks.index');
    }

    // ── Only Own Tasks Visible ───────────────────────────────────

    public function test_user_only_sees_their_own_assigned_tasks(): void
    {
        $otherUser = User::factory()->create();

        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'My Task',
        ]);
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $otherUser->id,
            'title'       => 'Other Task',
        ]);

        $response = $this->actingAs($this->user)
             ->get(route('my-tasks.index'));

        $tasks = $response->viewData('tasks');
        $this->assertCount(1, $tasks);
        $this->assertSame('My Task', $tasks->first()->title);
    }

    public function test_my_tasks_can_be_filtered_by_status(): void
    {
        Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);
        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
             ->get(route('my-tasks.index', ['status' => 'pending']));

        $tasks = $response->viewData('tasks');
        $this->assertTrue($tasks->every(fn($t) => $t->status === 'pending'));
    }

    public function test_my_tasks_can_be_searched_by_title(): void
    {
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Write unit tests',
        ]);
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Deploy to server',
        ]);

        $response = $this->actingAs($this->user)
             ->get(route('my-tasks.index', ['search' => 'unit']));

        $tasks = $response->viewData('tasks');
        $this->assertCount(1, $tasks);
        $this->assertSame('Write unit tests', $tasks->first()->title);
    }

    // ── Update Status ─────────────────────────────────────────────

    public function test_user_can_mark_their_task_as_in_progress(): void
    {
        $task = Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->user)
             ->patch(route('my-tasks.update-status', $task), ['status' => 'in_progress'])
             ->assertRedirect()
             ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_mark_their_task_as_completed(): void
    {
        $task = Task::factory()->inProgress()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->user)
             ->patch(route('my-tasks.update-status', $task), ['status' => 'completed'])
             ->assertRedirect()
             ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_user_cannot_update_status_of_another_users_task(): void
    {
        $otherUser = User::factory()->create();

        $task = Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $otherUser->id,
        ]);

        $this->actingAs($this->user)
             ->patch(route('my-tasks.update-status', $task), ['status' => 'completed'])
             ->assertRedirect()
             ->assertSessionHas('error');

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'status' => 'pending',
        ]);
    }

    public function test_status_update_rejects_invalid_status_value(): void
    {
        $task = Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->user)
             ->patch(route('my-tasks.update-status', $task), ['status' => 'flying'])
             ->assertSessionHasErrors('status');
    }

    public function test_guest_cannot_update_task_status(): void
    {
        $task = Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->patch(route('my-tasks.update-status', $task), ['status' => 'completed'])
             ->assertRedirect(route('login'));
    }
}
