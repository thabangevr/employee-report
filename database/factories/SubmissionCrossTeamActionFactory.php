<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SubmissionCrossTeamAction;
use App\Models\WeeklySubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SubmissionCrossTeamAction> */
class SubmissionCrossTeamActionFactory extends Factory
{
    protected $model = SubmissionCrossTeamAction::class;

    public function definition(): array
    {
        return [
            'weekly_submission_id' => WeeklySubmission::factory(),
            'owner_name' => fake()->name(),
            'ask' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
