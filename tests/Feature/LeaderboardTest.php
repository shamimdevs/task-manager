<?php

namespace Tests\Feature;

use App\Models\LeaderboardSnapshot;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
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

    public function test_guest_cannot_access_leaderboard(): void
    {
        $this->get(route('admin.leaderboard'))
             ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_leaderboard(): void
    {
        $this->actingAs($this->user)
             ->get(route('admin.leaderboard'))
             ->assertForbidden();
    }

    public function test_admin_can_access_leaderboard(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.leaderboard'))
             ->assertOk()
             ->assertViewIs('admin.leaderboard');
    }

    public function test_inactive_admin_cannot_access_leaderboard(): void
    {
        $inactive = User::factory()->admin()->inactive()->create();

        $this->actingAs($inactive)
             ->get(route('admin.leaderboard'))
             ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_trigger_leaderboard_generation(): void
    {
        $this->post(route('admin.leaderboard.generate'))
             ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_trigger_leaderboard_generation(): void
    {
        $this->actingAs($this->user)
             ->post(route('admin.leaderboard.generate'))
             ->assertForbidden();
    }

    // ── Empty State ──────────────────────────────────────────────

    public function test_leaderboard_shows_empty_state_when_no_data(): void
    {
        $response = $this->actingAs($this->admin)
             ->get(route('admin.leaderboard'));

        $response->assertOk();
        $entries = $response->viewData('entries');
        $this->assertCount(0, $entries);
    }

    // ── Generate ─────────────────────────────────────────────────

    public function test_admin_can_generate_leaderboard_via_button(): void
    {
        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.leaderboard.generate'))
             ->assertRedirect()
             ->assertSessionHas('success');

        $this->assertDatabaseHas('leaderboard_snapshots', [
            'user_id' => $this->user->id,
            'rank'    => 1,
        ]);
    }

    public function test_generate_creates_correct_task_counts(): void
    {
        Task::factory()->completed()->count(3)->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);
        Task::factory()->inProgress()->count(2)->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);
        Task::factory()->pending()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.leaderboard.generate'));

        $snapshot = LeaderboardSnapshot::where('user_id', $this->user->id)->first();

        $this->assertEquals(3, $snapshot->completed_tasks);
        $this->assertEquals(2, $snapshot->in_progress_tasks);
        $this->assertEquals(1, $snapshot->pending_tasks);
        $this->assertEquals(6, $snapshot->total_tasks);
        $this->assertEquals(50.00, $snapshot->completion_rate);
    }

    public function test_generate_ranks_users_by_completed_tasks(): void
    {
        $topUser = User::factory()->create();
        $lowUser = User::factory()->create();

        Task::factory()->completed()->count(5)->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $topUser->id,
        ]);
        Task::factory()->completed()->count(1)->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $lowUser->id,
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.leaderboard.generate'));

        $this->assertDatabaseHas('leaderboard_snapshots', ['user_id' => $topUser->id, 'rank' => 1]);
        $this->assertDatabaseHas('leaderboard_snapshots', ['user_id' => $lowUser->id, 'rank' => 2]);
    }

    public function test_generate_replaces_previous_snapshot(): void
    {
        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);

        $this->actingAs($this->admin)->post(route('admin.leaderboard.generate'));
        $this->actingAs($this->admin)->post(route('admin.leaderboard.generate'));

        $this->assertEquals(1, LeaderboardSnapshot::where('user_id', $this->user->id)->count());
    }

    public function test_generate_only_includes_active_users(): void
    {
        $inactiveUser = User::factory()->inactive()->create();

        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $inactiveUser->id,
        ]);

        $this->actingAs($this->admin)
             ->post(route('admin.leaderboard.generate'));

        $this->assertDatabaseMissing('leaderboard_snapshots', [
            'user_id' => $inactiveUser->id,
        ]);
    }

    // ── View Data ────────────────────────────────────────────────

    public function test_leaderboard_view_passes_correct_variables(): void
    {
        Task::factory()->completed()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
        ]);
        $this->actingAs($this->admin)->post(route('admin.leaderboard.generate'));

        $response = $this->actingAs($this->admin)
             ->get(route('admin.leaderboard'));

        $response->assertViewHas('entries');
        $response->assertViewHas('top3');
        $response->assertViewHas('generatedAt');
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('avgRate');
    }

    public function test_leaderboard_entries_are_paginated(): void
    {
        // $this->user (from setUp) + 14 new = 15 active regular users total
        $users = User::factory()->count(14)->create();

        foreach ($users as $u) {
            Task::factory()->completed()->create([
                'created_by'  => $this->admin->id,
                'assigned_to' => $u->id,
            ]);
        }

        $this->actingAs($this->admin)->post(route('admin.leaderboard.generate'));

        $response = $this->actingAs($this->admin)
             ->get(route('admin.leaderboard'));

        $entries = $response->viewData('entries');
        $this->assertEquals(10, $entries->perPage());
        $this->assertEquals(15, $entries->total());
        $this->assertCount(10, $entries->items());
    }

    public function test_leaderboard_second_page_works(): void
    {
        // $this->user (from setUp) + 14 new = 15 active regular users total
        $users = User::factory()->count(14)->create();

        foreach ($users as $u) {
            Task::factory()->completed()->create([
                'created_by'  => $this->admin->id,
                'assigned_to' => $u->id,
            ]);
        }

        $this->actingAs($this->admin)->post(route('admin.leaderboard.generate'));

        $response = $this->actingAs($this->admin)
             ->get(route('admin.leaderboard', ['page' => 2]));

        $entries = $response->viewData('entries');
        $this->assertCount(5, $entries->items());
    }
}
