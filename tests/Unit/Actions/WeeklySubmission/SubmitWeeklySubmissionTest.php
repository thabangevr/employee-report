<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\SubmitWeeklySubmission;
use App\Enums\SubmissionStatus;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SubmitWeeklySubmissionTest extends TestCase
{
    public function test_execute_updates_status_and_submitted_at(): void
    {
        $submission = Mockery::mock(WeeklySubmission::class);
        $submission->shouldReceive('update')
            ->with(Mockery::on(fn (array $attrs) =>
                $attrs['status'] === SubmissionStatus::Submitted->value
                && isset($attrs['submitted_at'])
            ))
            ->once();
        $submission->shouldReceive('refresh')->andReturnSelf();

        /** @var WeeklySubmissionRepositoryInterface&MockInterface $repository */
        $repository = Mockery::mock(WeeklySubmissionRepositoryInterface::class);
        $repository->shouldReceive('findOrFail')->with(1)->once()->andReturn($submission);

        $action = new SubmitWeeklySubmission($repository);
        $result = $action->execute(1);

        $this->assertSame($submission, $result);
    }
}
