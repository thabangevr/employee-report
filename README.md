# Everlytic Employee Report

An internal reporting application where managers submit weekly OKR-aligned updates and executives get a consolidated view of progress, risks, and team health across the organisation.

## What It Does

- **Managers** log their OKRs, then submit structured weekly updates covering area status (green/amber/blocker), last-week outcomes, this-week priorities, flags, and cross-team actions
- **CEO / Executives** see an aggregated dashboard with submission progress, area health metrics, all flags across managers, and can drill into any manager's detail view
- **Comments** allow the CEO to leave feedback on a manager's submission, visible to both sides
- **Word count tracking** enforces the SOP guideline of 150-200 words per submission

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 10, PHP 8.1+ |
| Frontend | Blade templates, Alpine.js, Bootstrap 5 |
| AI | Anthropic Claude SDK (`anthropic-ai/sdk`) for content analysis |
| Authentication | Azure AD SSO via Laravel Socialite (custom provider) |
| Database | MySQL |
| Design System | Custom Everlytic theme (CSS variables) |

## Architecture

The project follows a **Repository + Actions** pattern:

```
Request -> FormRequest (validation) -> Controller -> DTO -> Action -> Repository -> Model/DB
                                                             |
                                                      (may call other Actions)
                                                             |
Response <- Controller <- Result <- Action
```

- **Controllers** are thin — validate, build DTO, call action, return response
- **Actions** (`app/Actions/`) handle all internal business logic, one public `execute()` method each
- **Repositories** (`app/Repositories/`) handle data access only, with interfaces in `Contracts/` and Eloquent implementations in `Eloquent/`
- **DTOs** (`app/DTOs/`) carry data between layers as readonly classes
- **External Services** (`app/Services/External/`) are reserved for third-party integrations only (e.g. Azure AD)

## Key Features

### Role-Based Dashboards
- **CEO Dashboard** — Portfolio metrics, submission progress bar, flags across all managers, manager status cards with drill-down
- **Manager Dashboard** — Latest submission summary with metrics, area statuses, priorities, outcomes, and flags
- **Employee Dashboard** — Personal OKR view and update history

### Weekly Submissions
- **Dual-mode entry** — users choose between two ways to populate their update:
  - **Paste & Analyze** — paste raw weekly update text and let Claude AI extract areas, statuses, outcomes, priorities, and flags automatically
  - **Enter Manually** — fill in repeatable form fields directly
- **Reuse last week** — pre-fills the form with the previous week's structure (area names, cross-team action owners, headline number, OKR focus) while clearing per-week content so users start fresh
- Structured form with dynamic repeatable sections (areas, outcomes, priorities, flags, cross-team actions)
- OKR tagging on outcomes and priorities
- Live word count with colour-coded feedback
- Draft/submit workflow — submitted updates are locked from editing
- Headline number for key weekly metric

### OKR Management
- Managers add, edit, deactivate, and delete their OKRs
- Active OKRs appear as tagging options in weekly submissions

### CEO Review & Comments
- Drill into any manager's submission with full detail view
- Submission history sidebar for navigating between weeks
- Comment thread on each submission visible to both CEO and manager

## Setup

```bash
# Install dependencies
composer install

# Copy environment file and configure
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_DATABASE=everlytic_employee_report
DB_USERNAME=root
DB_PASSWORD=

# Configure Anthropic API key for AI-assisted submissions
ANTHROPIC_API_KEY=sk-ant-...

# Configure Azure AD in .env (optional — dev login available)
AZURE_AD_CLIENT_ID=
AZURE_AD_CLIENT_SECRET=
AZURE_AD_REDIRECT_URI=
AZURE_AD_TENANT_ID=

# Run migrations
php artisan migrate

# Start the server
php artisan serve
```

### Dev Login

In `local` environment, the login page shows dev bypass buttons:
- **Dev Login (Manager)** — logs in as a manager user
- **Dev Login (CEO)** — logs in as a CEO user

## Project Structure

```
app/
├── Actions/                    # Business logic grouped by domain
│   ├── Authentication/
│   ├── Dashboard/
│   ├── Okr/
│   ├── SubmissionComment/
│   └── WeeklySubmission/
├── DTOs/                       # Data transfer objects
│   ├── Authentication/
│   └── WeeklySubmission/
├── Enums/                      # UserRole, SubmissionStatus, AreaStatus
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Repositories/
│   ├── Contracts/              # Repository interfaces
│   └── Eloquent/               # Eloquent implementations
├── Services/External/          # Third-party integrations only
│   ├── Contracts/
│   └── Implementation/
└── Providers/
    └── RepositoryServiceProvider.php
```

## Testing

Tests will use PHPUnit with the following strategy:
- **Unit tests** for Actions (mock repository interfaces)
- **Integration tests** for Repositories (real database)
- **Feature tests** for Controllers (HTTP lifecycle)

## License

Proprietary — Everlytic internal use only.
