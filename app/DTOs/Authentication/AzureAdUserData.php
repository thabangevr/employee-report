<?php

declare(strict_types=1);

namespace App\DTOs\Authentication;

class AzureAdUserData
{
    public function __construct(
        public readonly string $azureAdId,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $jobTitle,
        public readonly ?string $role,
        public readonly ?string $managerEmail,
    ) {}

    public function toArray(): array
    {
        return [
            'azure_ad_id' => $this->azureAdId,
            'name' => $this->name,
            'email' => $this->email,
            'job_title' => $this->jobTitle,
        ];
    }
}
