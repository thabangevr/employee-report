<?php

declare(strict_types=1);

namespace App\Socialite;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class AzureAdProvider extends AbstractProvider
{
    protected $scopes = ['openid', 'profile', 'email', 'User.Read'];

    protected $scopeSeparator = ' ';

    private string $tenantId;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, string $tenantId)
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);

        $this->tenantId = $tenantId;
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(
            'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize',
            $state,
        );
    }

    protected function getTokenUrl(): string
    {
        return 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token';
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get('https://graph.microsoft.com/v1.0/me', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            RequestOptions::QUERY => [
                '$select' => 'id,displayName,mail,userPrincipalName,jobTitle,department',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['displayName'],
            'email' => $user['mail'] ?? $user['userPrincipalName'],
        ]);
    }
}
