<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\WeeklySubmission;

use App\Actions\WeeklySubmission\CalculateWordCount;
use App\Actions\WeeklySubmission\CreateWeeklySubmission;
use App\Actions\WeeklySubmission\SyncSubmissionRelations;
use App\DTOs\WeeklySubmission\CreateWeeklySubmissionData;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Support\Carbon;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateWeeklySubmissionTest extends TestCase
{
    public function test_execute_creates_submission_and_syncs_relations(): void
    {
        $data = new CreateWeeklySubmissionData(
            userId: 1,
            weekStartDate: Carbon::parse('2026-07-13'),
            oneNumberValue: '85%',
            oneNumberLabel: 'Completion rate',
            okrFocusIds: [],
            areas: [
                ['name' => 'Platform', 'status' => 'green', 'outcomes' => [], 'priorities' => []],
            ],
            flags: [],
            crossTeamActions: [],
        );

        $submission = Mockery::mock(WeeklySubmission::class);
        $submission->shouldReceive('refresh')->andReturnSelf();
        $submission->shouldReceive('update')->with(['word_count' => 10])->once();

        /** @var WeeklySubmissionRepositoryInterface&MockInterface $repository */
        $repository = Mockery::mock(WeeklySubmissionRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->with(Mockery::on(fn (array $attrs) =>
                $attrs['user_id'] === 1
                && $attrs['one_number_value'] === '85%'
                && $attrs['status'] === 'draft'
            ))
            ->once()
            ->andReturn($submission);

        /** @var SyncSubmissionRelations&MockInterface $syncRelations */
        $syncRelations = Mockery::mock(SyncSubmissionRelations::class);
        $syncRelations->shouldReceive('execute')->with($submission, $data)->once();

        /** @var CalculateWordCount&MockInterface $calculateWordCount */
        $calculateWordCount = Mockery::mock(CalculateWordCount::class);
        $calculateWordCount->shouldReceive('execute')->with($submission)->once()->andReturn(10);

        $action = new CreateWeeklySubmission($repository, $syncRelations, $calculateWordCount);
        $result = $action->execute($data);

        $this->assertSame($submission, $result);
    }
}
