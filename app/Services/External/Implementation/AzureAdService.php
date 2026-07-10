<?php

declare(strict_types=1);

namespace App\Services\External\Implementation;

use App\DTOs\Authentication\AzureAdUserData;
use App\Services\External\Contracts\AzureAdServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AzureAdService implements AzureAdServiceInterface
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('azure')->redirect();
    }

    public function handleCallback(): AzureAdUserData
    {
        $socialiteUser = Socialite::driver('azure')->user();
        $token = $socialiteUser->token;
        $raw = $socialiteUser->getRaw();

        $managerEmail = $this->fetchManagerEmail($token);
        $role = $this->resolveRole($raw);

        return new AzureAdUserData(
            azureAdId: $socialiteUser->getId(),
            name: $socialiteUser->getName(),
            email: $socialiteUser->getEmail(),
            jobTitle: $raw['jobTitle'] ?? null,
            role: $role,
            managerEmail: $managerEmail,
        );
    }

    private function fetchManagerEmail(string $token): ?string
    {
        try {
            $response = Http::withToken($token)
                ->get('https://graph.microsoft.com/v1.0/me/manager', [
                    '$select' => 'mail,userPrincipalName',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return $data['mail'] ?? $data['userPrincipalName'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch manager from Graph API', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function resolveRole(array $rawUser): ?string
    {
        $roles = $rawUser['roles'] ?? [];

        foreach ($roles as $role) {
            if (in_array($role, ['ceo', 'manager', 'employee'])) {
                return $role;
            }
        }

        return null;
    }
}
