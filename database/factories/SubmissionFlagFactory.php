<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SubmissionFlag;
use App\Models\WeeklySubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubmissionFlag> */
class SubmissionFlagFactory extends Factory
{
    protected $model = SubmissionFlag::class;

    public function definition(): array
    {
        return [
            'weekly_submission_id' => WeeklySubmission::factory(),
            'risk' => fake()->sentence(),
            'cause' => fake()->sentence(),
            'consequence' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
