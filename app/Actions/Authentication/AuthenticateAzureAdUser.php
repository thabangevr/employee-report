<?php

declare(strict_types=1);

namespace App\Actions\Authentication;

use App\DTOs\Authentication\AzureAdUserData;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class AuthenticateAzureAdUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function execute(AzureAdUserData $data): User
    {
        $user = $this->userRepository->findByAzureAdId($data->azureAdId);

        $attributes = $data->toArray();

        if ($data->role) {
            $attributes['role'] = $data->role;
        }

        $managerId = $this->resolveManagerId($data->managerEmail);
        if ($managerId) {
            $attributes['manager_id'] = $managerId;
        }

        if ($user) {
            return $this->userRepository->update($user->id, $attributes);
        }

        if (! isset($attributes['role'])) {
            $attributes['role'] = UserRole::Employee->value;
        }

        return $this->userRepository->create($attributes);
    }

    private function resolveManagerId(?string $managerEmail): ?int
    {
        if (! $managerEmail) {
            return null;
        }

        $manager = $this->userRepository->findByEmail($managerEmail);

        return $manager?->id;
    }
}
