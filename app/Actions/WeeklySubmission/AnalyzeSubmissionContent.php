<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use Anthropic\Client;

class AnalyzeSubmissionContent
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(apiKey: config('anthropic.api_key'));
    }

    public function execute(string $rawContent): array
    {
        $systemPrompt = <<<'PROMPT'
You are an assistant that extracts structured weekly submission data from raw text.

Analyze the provided text and extract the following structure as JSON:

{
  "areas": [
    {
      "name": "Area/project name",
      "status": "green|amber|blocker",
      "status_justification": "Brief reason for the status",
      "outcomes": [
        {"description": "What was achieved last week"}
      ],
      "priorities": [
        {"description": "What will be focused on this week"}
      ]
    }
  ],
  "flags": [
    {
      "risk": "What is at risk",
      "cause": "Why it is at risk",
      "consequence": "What happens if not addressed"
    }
  ]
}

Rules:
- Each area MUST have a status: "green" (on track), "amber" (at risk/delayed), or "blocker" (blocked/critical issue).
- Determine status from context clues: delays, blockers, issues = amber or blocker; completed/on-track = green.
- Extract 2-4 outcomes (last week achievements) per area.
- Extract 2-4 priorities (this week focus) per area.
- If you find risks, blockers, or red flags, extract them as flags with risk/cause/consequence.
- If no flags are evident, return an empty flags array.
- Return ONLY valid JSON. No markdown, no code fences, no explanation.
PROMPT;

        $message = $this->client->messages->create(
            model: config('anthropic.model'),
            maxTokens: 4096,
            system: $systemPrompt,
            messages: [
                ['role' => 'user', 'content' => "Extract structured weekly submission data from this update:\n\n" . $rawContent],
            ],
        );

        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text = $block->text;
                break;
            }
        }

        $text = trim($text);
        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\s*/', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
        }

        $parsed = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse AI response: ' . json_last_error_msg());
        }

        return $this->normalize($parsed);
    }

    private function normalize(array $data): array
    {
        $areas = [];
        foreach ($data['areas'] ?? [] as $area) {
            $status = $area['status'] ?? 'green';
            if (!in_array($status, ['green', 'amber', 'blocker'], true)) {
                $status = 'green';
            }

            $outcomes = [];
            foreach ($area['outcomes'] ?? [] as $outcome) {
                $desc = is_string($outcome) ? $outcome : ($outcome['description'] ?? '');
                if ($desc !== '') {
                    $outcomes[] = ['description' => $desc, 'okr_id' => ''];
                }
            }

            $priorities = [];
            foreach ($area['priorities'] ?? [] as $priority) {
                $desc = is_string($priority) ? $priority : ($priority['description'] ?? '');
                if ($desc !== '') {
                    $priorities[] = ['description' => $desc, 'okr_id' => ''];
                }
            }

            if (empty($outcomes)) {
                $outcomes[] = ['description' => '', 'okr_id' => ''];
            }
            if (empty($priorities)) {
                $priorities[] = ['description' => '', 'okr_id' => ''];
            }

            $areas[] = [
                'name' => $area['name'] ?? '',
                'manager_area_id' => null,
                'status' => $status,
                'status_justification' => $area['status_justification'] ?? '',
                'outcomes' => $outcomes,
                'priorities' => $priorities,
            ];
        }

        $flags = [];
        foreach ($data['flags'] ?? [] as $flag) {
            $flags[] = [
                'risk' => $flag['risk'] ?? '',
                'cause' => $flag['cause'] ?? '',
                'consequence' => $flag['consequence'] ?? '',
            ];
        }

        if (empty($areas)) {
            $areas[] = [
                'name' => '',
                'manager_area_id' => null,
                'status' => null,
                'status_justification' => '',
                'outcomes' => [['description' => '', 'okr_id' => '']],
                'priorities' => [['description' => '', 'okr_id' => '']],
            ];
        }

        return [
            'areas' => $areas,
            'flags' => $flags,
        ];
    }
}
