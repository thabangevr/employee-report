<?php

use App\Http\Controllers\Auth\AzureAdController;
use App\Http\Controllers\CeoManagerDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OkrController;
use App\Http\Controllers\SubmissionCommentController;
use App\Http\Controllers\WeeklySubmissionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/auth/azure/redirect', [AzureAdController::class, 'redirect'])->name('auth.azure.redirect');
    Route::get('/auth/azure/callback', [AzureAdController::class, 'callback'])->name('auth.azure.callback');

    // Dev bypass — remove when Azure AD consent is approved
    if (app()->environment('local')) {
        Route::get('/dev-login', function () {
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'thabang@everlytic.com'],
                [
                    'name' => 'Thabang Masango',
                    'role' => \App\Enums\UserRole::Manager->value,
                    'job_title' => 'Manager',
                ],
            );

            Auth::login($user, remember: true);

            return redirect()->route('dashboard');
        })->name('dev.login');

        Route::get('/dev-login-ceo', function () {
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'ceo@everlytic.com'],
                [
                    'name' => 'Dev CEO',
                    'role' => \App\Enums\UserRole::CEO->value,
                    'job_title' => 'CEO',
                ],
            );

            Auth::login($user, remember: true);

            return redirect()->route('dashboard');
        })->name('dev.login.ceo');
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:manager,ceo')->group(function () {
        Route::resource('okrs', OkrController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::patch('okrs/{okr}/toggle', [OkrController::class, 'toggle'])->name('okrs.toggle');
        Route::get('/weekly-submissions/previous-data', [WeeklySubmissionController::class, 'previousData'])
            ->name('weekly-submissions.previous-data');
        Route::post('/weekly-submissions/analyze', [WeeklySubmissionController::class, 'analyze'])
            ->name('weekly-submissions.analyze');
        Route::resource('weekly-submissions', WeeklySubmissionController::class);
        Route::post('/weekly-submissions/{weekly_submission}/submit', [WeeklySubmissionController::class, 'submit'])
            ->name('weekly-submissions.submit');
    });

    Route::middleware('role:ceo')->group(function () {
        Route::get('/managers/{manager}', [CeoManagerDetailController::class, 'show'])
            ->name('ceo.manager.detail');
        Route::get('/submissions/{weekly_submission}/review', [CeoManagerDetailController::class, 'submission'])
            ->name('ceo.submission.review');
    });

    Route::post('/submissions/{weekly_submission}/comments', [SubmissionCommentController::class, 'store'])
        ->name('submission-comments.store');
    Route::delete('/submission-comments/{comment}', [SubmissionCommentController::class, 'destroy'])
        ->name('submission-comments.destroy');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
