<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\User;
use App\Models\WeeklySubmission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends Factory<WeeklySubmission> */
class WeeklySubmissionFactory extends Factory
{
    protected $model = WeeklySubmission::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'week_start_date' => Carbon::now()->startOfWeek(Carbon::MONDAY),
            'one_number_value' => fake()->randomNumber(2) . '%',
            'one_number_label' => fake()->sentence(3),
            'word_count' => fake()->numberBetween(50, 200),
            'status' => SubmissionStatus::Draft,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => SubmissionStatus::Submitted,
            'submitted_at' => now(),
        ]);
    }
}
