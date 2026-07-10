<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Models\WeeklySubmission;

class CalculateWordCount
{
    public function execute(WeeklySubmission $submission): int
    {
        $text = '';

        $submission->load(['areas.outcomes', 'areas.priorities', 'flags', 'crossTeamActions']);

        foreach ($submission->areas as $area) {
            $text .= ' ' . ($area->status_justification ?? '');

            foreach ($area->outcomes as $outcome) {
                $text .= ' ' . $outcome->description;
            }

            foreach ($area->priorities as $priority) {
                $text .= ' ' . $priority->description;
            }
        }

        foreach ($submission->flags as $flag) {
            $text .= ' ' . $flag->risk . ' ' . $flag->cause . ' ' . $flag->consequence;
        }

        foreach ($submission->crossTeamActions as $action) {
            $text .= ' ' . $action->ask;
        }

        return str_word_count(trim($text));
    }
}
