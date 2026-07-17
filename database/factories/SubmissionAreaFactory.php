<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AreaStatus;
use App\Models\SubmissionArea;
use App\Models\WeeklySubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubmissionArea> */
class SubmissionAreaFactory extends Factory
{
    protected $model = SubmissionArea::class;

    public function definition(): array
    {
        return [
            'weekly_submission_id' => WeeklySubmission::factory(),
            'name' => fake()->randomElement(['Platform', 'Growth', 'Infrastructure', 'Data']),
            'status' => fake()->randomElement(AreaStatus::cases()),
            'status_justification' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
