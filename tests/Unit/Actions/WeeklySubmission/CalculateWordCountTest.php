<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\CalculateWordCount;
use App\Models\User;
use App\Models\WeeklySubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateWordCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_counts_words_from_all_text_fields(): void
    {
        $user = User::factory()->manager()->create();

        $submission = WeeklySubmission::factory()->create(['user_id' => $user->id]);

        $area = $submission->areas()->create([
            'name' => 'Platform',
            'status' => 'green',
            'status_justification' => 'Everything is on track',
            'sort_order' => 0,
        ]);

        $area->outcomes()->create([
            'description' => 'Shipped the new auth flow',
            'sort_order' => 0,
        ]);

        $area->priorities()->create([
            'description' => 'Focus on API rate limiting',
            'sort_order' => 0,
        ]);

        $submission->flags()->create([
            'risk' => 'Deadline pressure',
            'cause' => 'Vendor delay',
            'consequence' => 'Launch postponed',
            'sort_order' => 0,
        ]);

        $submission->crossTeamActions()->create([
            'owner_name' => 'Design',
            'ask' => 'Review the mockups by Friday',
            'sort_order' => 0,
        ]);

        $action = new CalculateWordCount();
        $count = $action->execute($submission);

        $this->assertGreaterThan(0, $count);
        $this->assertIsInt($count);
    }

    public function test_returns_zero_for_empty_submission(): void
    {
        $user = User::factory()->manager()->create();
        $submission = WeeklySubmission::factory()->create(['user_id' => $user->id]);

        $action = new CalculateWordCount();
        $count = $action->execute($submission);

        $this->assertEquals(0, $count);
    }
}
