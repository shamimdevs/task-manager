<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by'  => User::factory(),
            'assigned_to' => User::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status'      => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority'    => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date'    => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'order'       => fake()->numberBetween(0, 10),
            'attachment'  => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function overdue(): static
    {
        return $this->state([
            'due_date' => now()->subDays(3)->toDateString(),
            'status'   => 'pending',
        ]);
    }

    public function urgent(): static
    {
        return $this->state(['priority' => 'urgent']);
    }
}
