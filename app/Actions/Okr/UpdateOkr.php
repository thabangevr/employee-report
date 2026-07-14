<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\DTOs\Okr\UpdateOkrData;
use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;

class UpdateOkr
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(UpdateOkrData $data): Okr
    {
        /** @var Okr */
        $okr = $this->okrRepository->update($data->okrId, $data->toArray());

        $existingIds = $okr->keyResults()->pluck('id')->toArray();
        $incomingIds = [];

        foreach ($data->keyResults as $index => $keyResult) {
            if (!empty($keyResult['id'])) {
                $okr->keyResults()->where('id', $keyResult['id'])->update([
                    'type' => $keyResult['type'],
                    'description' => $keyResult['description'],
                    'sort_order' => $index,
                ]);
                $incomingIds[] = (int) $keyResult['id'];
            } else {
                $created = $okr->keyResults()->create([
                    'type' => $keyResult['type'],
                    'description' => $keyResult['description'],
                    'sort_order' => $index,
                ]);
                $incomingIds[] = $created->id;
            }
        }

        $toDelete = array_diff($existingIds, $incomingIds);
        if ($toDelete) {
            $okr->keyResults()->whereIn('id', $toDelete)->delete();
        }

        return $okr->load('keyResults');
    }
}
