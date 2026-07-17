<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\AnalyzeSubmissionContent;
use Tests\TestCase;

class AnalyzeSubmissionContentTest extends TestCase
{
    public function test_normalize_produces_valid_structure(): void
    {
        $action = new AnalyzeSubmissionContent();

        $reflection = new \ReflectionMethod($action, 'normalize');
        $reflection->setAccessible(true);

        $input = [
            'areas' => [
                [
                    'name' => 'Platform',
                    'status' => 'green',
                    'status_justification' => 'On track',
                    'outcomes' => [
                        ['description' => 'Shipped auth flow'],
                        ['description' => 'Fixed critical bugs'],
                    ],
                    'priorities' => [
                        ['description' => 'Rate limiting'],
                    ],
                ],
            ],
            'flags' => [
                ['risk' => 'API delay', 'cause' => 'Vendor', 'consequence' => 'Slip'],
            ],
        ];

        $result = $reflection->invoke($action, $input);

        $this->assertCount(1, $result['areas']);
        $this->assertEquals('Platform', $result['areas'][0]['name']);
        $this->assertEquals('green', $result['areas'][0]['status']);
        $this->assertCount(2, $result['areas'][0]['outcomes']);
        $this->assertEquals('', $result['areas'][0]['outcomes'][0]['okr_id']);
        $this->assertCount(1, $result['areas'][0]['priorities']);
        $this->assertCount(1, $result['flags']);
        $this->assertEquals('API delay', $result['flags'][0]['risk']);
    }

    public function test_normalize_handles_empty_data(): void
    {
        $action = new AnalyzeSubmissionContent();

        $reflection = new \ReflectionMethod($action, 'normalize');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($action, []);

        $this->assertCount(1, $result['areas']);
        $this->assertEquals('', $result['areas'][0]['name']);
        $this->assertEmpty($result['flags']);
    }

    public function test_normalize_clamps_invalid_status(): void
    {
        $action = new AnalyzeSubmissionContent();

        $reflection = new \ReflectionMethod($action, 'normalize');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($action, [
            'areas' => [
                ['name' => 'Test', 'status' => 'invalid_status', 'outcomes' => [], 'priorities' => []],
            ],
        ]);

        $this->assertEquals('green', $result['areas'][0]['status']);
    }

    public function test_normalize_handles_string_outcomes(): void
    {
        $action = new AnalyzeSubmissionContent();

        $reflection = new \ReflectionMethod($action, 'normalize');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($action, [
            'areas' => [
                [
                    'name' => 'Test',
                    'status' => 'amber',
                    'outcomes' => ['Direct string outcome'],
                    'priorities' => ['Direct string priority'],
                ],
            ],
        ]);

        $this->assertEquals('Direct string outcome', $result['areas'][0]['outcomes'][0]['description']);
        $this->assertEquals('Direct string priority', $result['areas'][0]['priorities'][0]['description']);
    }
}
