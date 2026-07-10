<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Authentication\AuthenticateAzureAdUser;
use App\Http\Controllers\Controller;
use App\Services\External\Contracts\AzureAdServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AzureAdController extends Controller
{
    public function __construct(
        private AzureAdServiceInterface $azureAdService,
        private AuthenticateAzureAdUser $authenticateAzureAdUser,
    ) {}

    public function redirect(): RedirectResponse
    {
        return $this->azureAdService->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $data = $this->azureAdService->handleCallback();
            $user = $this->authenticateAzureAdUser->execute($data);

            Auth::login($user, remember: true);

            return redirect()->intended('/dashboard');
        } catch (\Throwable $e) {
            Log::error('Azure AD login failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')->with('error', 'Login failed. Please try again.');
        }
    }
}
