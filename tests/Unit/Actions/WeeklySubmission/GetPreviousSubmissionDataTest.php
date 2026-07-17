<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\GetPreviousSubmissionData;
use App\Models\User;
use App\Models\WeeklySubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPreviousSubmissionDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_null_when_no_submissions(): void
    {
        $user = User::factory()->manager()->create();

        $action = app(GetPreviousSubmissionData::class);
        $result = $action->execute($user->id);

        $this->assertNull($result);
    }

    public function test_returns_structure_from_latest_submission(): void
    {
        $user = User::factory()->manager()->create();

        $submission = WeeklySubmission::factory()->create([
            'user_id' => $user->id,
            'one_number_value' => '80%',
            'one_number_label' => 'Target hit',
        ]);

        $area = $submission->areas()->create([
            'name' => 'Engineering',
            'status' => 'green',
            'status_justification' => 'All good',
            'sort_order' => 0,
        ]);

        $area->outcomes()->create([
            'description' => 'Old outcome',
            'sort_order' => 0,
        ]);

        $submission->crossTeamActions()->create([
            'owner_name' => 'Marketing',
            'ask' => 'Old ask',
            'sort_order' => 0,
        ]);

        $action = app(GetPreviousSubmissionData::class);
        $result = $action->execute($user->id);

        $this->assertNotNull($result);
        $this->assertEquals('80%', $result['one_number_value']);
        $this->assertEquals('Engineering', $result['areas'][0]['name']);
        $this->assertNull($result['areas'][0]['status']);
        $this->assertEquals('', $result['areas'][0]['outcomes'][0]['description']);
        $this->assertEquals('Marketing', $result['cross_team_actions'][0]['owner_name']);
        $this->assertEquals('', $result['cross_team_actions'][0]['ask']);
        $this->assertEmpty($result['flags']);
    }
}
