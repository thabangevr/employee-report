<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\DeleteWeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class DeleteWeeklySubmissionTest extends TestCase
{
    public function test_execute_deletes_submission(): void
    {
        /** @var WeeklySubmissionRepositoryInterface&MockInterface $repository */
        $repository = Mockery::mock(WeeklySubmissionRepositoryInterface::class);
        $repository->shouldReceive('delete')->with(1)->once()->andReturn(true);

        $action = new DeleteWeeklySubmission($repository);
        $result = $action->execute(1);

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_not_found(): void
    {
        /** @var WeeklySubmissionRepositoryInterface&MockInterface $repository */
        $repository = Mockery::mock(WeeklySubmissionRepositoryInterface::class);
        $repository->shouldReceive('delete')->with(999)->once()->andReturn(false);

        $action = new DeleteWeeklySubmission($repository);
        $result = $action->execute(999);

        $this->assertFalse($result);
    }
}
