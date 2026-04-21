<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTaskTest extends TestCase
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

    public function test_guest_cannot_access_task_management(): void
    {
        $this->get(route('admin.tasks.index'))
             ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_task_management(): void
    {
        $this->actingAs($this->user)
             ->get(route('admin.tasks.index'))
             ->assertForbidden();
    }

    public function test_admin_can_access_task_management(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.tasks.index'))
             ->assertOk()
             ->assertViewIs('admin.tasks.index');
    }

    public function test_inactive_user_cannot_access_task_management(): void
    {
        $inactive = User::factory()->admin()->inactive()->create();

        $this->actingAs($inactive)
             ->get(route('admin.tasks.index'))
             ->assertRedirect(route('login'));
    }

    // ── Index / List ─────────────────────────────────────────────

    public function test_admin_sees_all_tasks_on_index(): void
    {
        Task::factory()->count(3)->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->get(route('admin.tasks.index'))
             ->assertOk()
             ->assertViewHas('tasks');
    }

    public function test_task_index_can_filter_by_status(): void
    {
        Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Pending Task',
        ]);
        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Completed Task',
        ]);

        $response = $this->actingAs($this->admin)
             ->get(route('admin.tasks.index', ['status' => 'pending']));

        $response->assertOk();
        $tasks = $response->viewData('tasks');
        $this->assertTrue($tasks->every(fn($t) => $t->status === 'pending'));
    }

    public function test_task_index_can_filter_by_priority(): void
    {
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'priority'    => 'urgent',
        ]);
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'priority'    => 'low',
        ]);

        $response = $this->actingAs($this->admin)
             ->get(route('admin.tasks.index', ['priority' => 'urgent']));

        $tasks = $response->viewData('tasks');
        $this->assertTrue($tasks->every(fn($t) => $t->priority === 'urgent'));
    }

    public function test_task_index_can_search_by_title(): void
    {
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Fix login bug',
        ]);
        Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'title'       => 'Design dashboard',
        ]);

        $response = $this->actingAs($this->admin)
             ->get(route('admin.tasks.index', ['search' => 'login']));

        $tasks = $response->viewData('tasks');
        $this->assertCount(1, $tasks);
        $this->assertSame('Fix login bug', $tasks->first()->title);
    }

    // ── Create ───────────────────────────────────────────────────

    public function test_admin_can_view_create_task_form(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.tasks.create'))
             ->assertOk()
             ->assertViewIs('admin.tasks.create');
    }

    public function test_admin_can_create_a_task(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'New Feature Task',
                 'assigned_to' => $this->user->id,
                 'description' => 'Build the feature',
                 'status'      => 'pending',
                 'priority'    => 'high',
                 'due_date'    => now()->addDays(7)->toDateString(),
                 'order'       => 0,
             ])
             ->assertRedirect(route('admin.tasks.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title'       => 'New Feature Task',
            'assigned_to' => $this->user->id,
            'created_by'  => $this->admin->id,
            'status'      => 'pending',
            'priority'    => 'high',
        ]);
    }

    public function test_task_creation_requires_title(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => '',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'medium',
             ])
             ->assertSessionHasErrors('title');
    }

    public function test_task_creation_requires_valid_assigned_user(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Some Task',
                 'assigned_to' => 9999,
                 'status'      => 'pending',
                 'priority'    => 'medium',
             ])
             ->assertSessionHasErrors('assigned_to');
    }

    public function test_task_creation_requires_valid_status(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Some Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'invalid_status',
                 'priority'    => 'medium',
             ])
             ->assertSessionHasErrors('status');
    }

    public function test_task_creation_requires_valid_priority(): void
    {
        $this->actingAs($this->admin)
             ->post(route('admin.tasks.store'), [
                 'title'       => 'Some Task',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'super_urgent',
             ])
             ->assertSessionHasErrors('priority');
    }

    // ── Edit / Update ────────────────────────────────────────────

    public function test_admin_can_view_edit_task_form(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->get(route('admin.tasks.edit', $task))
             ->assertOk()
             ->assertViewIs('admin.tasks.edit')
             ->assertViewHas('task', $task);
    }

    public function test_admin_can_update_a_task(): void
    {
        $task = Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->put(route('admin.tasks.update', $task), [
                 'title'       => 'Updated Title',
                 'assigned_to' => $this->user->id,
                 'status'      => 'in_progress',
                 'priority'    => 'urgent',
                 'order'       => 1,
             ])
             ->assertRedirect(route('admin.tasks.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'title'  => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    public function test_task_update_requires_title(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->put(route('admin.tasks.update', $task), [
                 'title'       => '',
                 'assigned_to' => $this->user->id,
                 'status'      => 'pending',
                 'priority'    => 'medium',
             ])
             ->assertSessionHasErrors('title');
    }

    // ── Delete ───────────────────────────────────────────────────

    public function test_admin_can_delete_a_task(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->delete(route('admin.tasks.destroy', $task))
             ->assertRedirect(route('admin.tasks.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_regular_user_cannot_delete_a_task(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->user)
             ->delete(route('admin.tasks.destroy', $task))
             ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
