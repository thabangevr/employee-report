<?php

declare(strict_types=1);

namespace App\Services\External\Contracts;

use App\DTOs\Authentication\AzureAdUserData;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface AzureAdServiceInterface
{
    public function redirect(): RedirectResponse;

    public function handleCallback(): AzureAdUserData;
}
