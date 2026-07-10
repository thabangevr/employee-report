# Employee Report — Architecture & Conventions

## Project Overview

Employee reporting application where employees submit OKRs and managers review them. Built on **Laravel 10**, PHP 8.1+.

## Architecture: Repository Pattern + Actions

This project uses a **Repository + Actions** architecture. Follow these rules strictly when generating any code.

### Folder Structure

```
app/
├── Actions/                          # Internal business logic (grouped by domain)
│   └── WeeklyOkrSubmission/
│       ├── CreateWeeklySubmission.php
│       ├── UpdateWeeklySubmission.php
│       └── DeleteWeeklySubmission.php
│
├── DTOs/                             # Data Transfer Objects (grouped by domain)
│   └── WeeklyOkrSubmission/
│       ├── CreateWeeklySubmissionData.php
│       └── UpdateWeeklySubmissionData.php
│
├── Repositories/
│   ├── Contracts/                    # Interfaces at root of Contracts
│   │   └── WeeklyOkrSubmissionRepositoryInterface.php
│   └── Eloquent/                     # Implementations at root of Eloquent
│       └── WeeklyOkrSubmissionRepository.php
│
├── Services/
│   └── External/                     # ONLY for external integrations (SSO, APIs, etc.)
│       ├── Contracts/
│       │   └── SsoAuthenticationServiceInterface.php
│       └── Implementation/
│           └── SsoAuthenticationService.php
│
├── Http/
│   ├── Controllers/
│   ├── Requests/                     # Form Requests handle validation
│   └── Resources/                    # API Resources for response transformation
│
├── Models/
├── Providers/
│   └── RepositoryServiceProvider.php # All repository & service bindings here
└── ...
```

### Rules

#### Controllers
- Controllers are **thin**. They receive the request, delegate to an Action, and return a response.
- Never put business logic in controllers.
- A controller method should: validate (via Form Request), build a DTO, call an Action, return a response.

#### Actions (`app/Actions/`)
- Actions handle **all internal application logic**. Use Actions instead of services for anything internal.
- Group actions by **feature/domain**: `Actions/WeeklyOkrSubmission/`, `Actions/MonthlyReview/`, etc.
- Each action is a single-purpose class with one public `execute()` method.
- Actions receive a DTO (or simple typed parameters) and return a result.
- Actions **can call other actions** for composition.
- Actions **can call repositories** for data access.
- Actions **must not** call controllers or depend on HTTP-layer concerns (Request, Response).

```php
namespace App\Actions\WeeklyOkrSubmission;

class CreateWeeklySubmission
{
    public function __construct(
        private WeeklyOkrSubmissionRepositoryInterface $repository,
    ) {}

    public function execute(CreateWeeklySubmissionData $data): WeeklyOkrSubmission
    {
        return $this->repository->create($data->toArray());
    }
}
```

#### DTOs (`app/DTOs/`)
- Group by domain, matching the Actions structure.
- DTOs are simple readonly classes that carry data between layers.
- Use named constructor (`fromRequest()`, `fromArray()`) for creation.
- Suffix with `Data`: `CreateWeeklySubmissionData`, `UpdateWeeklySubmissionData`.

```php
namespace App\DTOs\WeeklyOkrSubmission;

class CreateWeeklySubmissionData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $userId,
    ) {}

    public static function fromRequest(StoreWeeklySubmissionRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            description: $request->validated('description'),
            userId: $request->user()->id,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
```

#### Repositories (`app/Repositories/`)
- **Contracts/** — Interfaces live at the root of this folder. Suffix: `RepositoryInterface`.
- **Eloquent/** — Implementations live at the root of this folder. Suffix: `Repository`.
- Repositories handle **only data access** — no business logic.
- Every repository must implement its corresponding interface.

```php
// app/Repositories/Contracts/WeeklyOkrSubmissionRepositoryInterface.php
namespace App\Repositories\Contracts;

interface WeeklyOkrSubmissionRepositoryInterface
{
    public function find(int $id): ?WeeklyOkrSubmission;
    public function create(array $data): WeeklyOkrSubmission;
    public function update(int $id, array $data): WeeklyOkrSubmission;
    public function delete(int $id): bool;
}
```

```php
// app/Repositories/Eloquent/WeeklyOkrSubmissionRepository.php
namespace App\Repositories\Eloquent;

class WeeklyOkrSubmissionRepository implements WeeklyOkrSubmissionRepositoryInterface
{
    public function __construct(private WeeklyOkrSubmission $model) {}

    // ... implement interface methods using Eloquent
}
```

#### External Services (`app/Services/External/`)
- **Only** for integrations with external systems (SSO, third-party APIs, email providers, etc.).
- Never use Services for internal application logic — use Actions instead.
- **Contracts/** — Interfaces for external services.
- **Implementation/** — Concrete implementations.

#### Bindings (`app/Providers/RepositoryServiceProvider.php`)
- All repository interface-to-implementation bindings go in a dedicated `RepositoryServiceProvider`.
- All external service interface-to-implementation bindings go here too.
- Register this provider in `config/app.php`.

```php
namespace App\Providers;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            WeeklyOkrSubmissionRepositoryInterface::class,
            WeeklyOkrSubmissionRepository::class,
        );
    }
}
```

### Naming Conventions (Laravel Standard)

| Type | Convention | Example |
|---|---|---|
| Model | Singular, PascalCase | `WeeklyOkrSubmission` |
| Controller | Singular, PascalCase + `Controller` | `WeeklyOkrSubmissionController` |
| Repository Interface | PascalCase + `RepositoryInterface` | `WeeklyOkrSubmissionRepositoryInterface` |
| Repository | PascalCase + `Repository` | `WeeklyOkrSubmissionRepository` |
| Action | Verb + Noun, PascalCase | `CreateWeeklySubmission` |
| DTO | Verb + Noun + `Data` | `CreateWeeklySubmissionData` |
| Form Request | Verb + Noun + `Request` | `StoreWeeklySubmissionRequest` |
| Migration | Laravel default snake_case | `create_weekly_okr_submissions_table` |
| Table | Plural, snake_case | `weekly_okr_submissions` |
| Foreign key | Singular model + `_id` | `user_id`, `weekly_okr_submission_id` |
| Route | Plural, kebab-case, resourceful | `weekly-okr-submissions` |
| API Resource | Singular + `Resource` | `WeeklyOkrSubmissionResource` |
| External Service Interface | PascalCase + `ServiceInterface` | `SsoAuthenticationServiceInterface` |
| External Service | PascalCase + `Service` | `SsoAuthenticationService` |

### Request Flow

```
Request → FormRequest (validation) → Controller → DTO → Action → Repository → Model/DB
                                                          ↓
                                                   (may call other Actions)
                                                          ↓
Response ← Resource ← Controller ← Result ← Action
```

### Testing

- Use **PHPUnit** (already included).
- Test Actions as unit tests — mock the repository interface.
- Test Repositories as integration tests against a real database.
- Test Controllers as feature tests via HTTP.

### General

- Follow **PSR-12** coding standards.
- Use **strict types** (`declare(strict_types=1);`) in every PHP file.
- Use constructor injection via Laravel's service container — no `app()` or `resolve()` helpers.
- Use PHP 8.1+ features: readonly properties, enums, named arguments, match expressions.

## Frontend Stack

- **Blade** templates with **Alpine.js** for interactivity
- **Bootstrap 5** for layout and responsive grid
- No jQuery — use Alpine.js for all interactive behavior
- Custom CSS overrides follow the Everlytic design system below

### Design System (Everlytic Theme)

All UI must follow this design language consistently.

#### Color Palette

| Token | Hex | Usage |
|---|---|---|
| `--ev-dark` | `#1c2b3a` | Header, dark backgrounds, primary text headings |
| `--ev-green` | `#6cbf3e` | Accent, active states, section titles, logo |
| `--ev-bg` | `#f4f5f7` | Page background |
| `--ev-white` | `#ffffff` | Card backgrounds |
| `--ev-text` | `#1a2533` | Primary body text |
| `--ev-text-secondary` | `#8ea4ba` | Labels, muted text, meta info |
| `--ev-text-body` | `#2a3b4d` | Card body text |
| `--ev-text-heading` | `#3a5068` | Sub-headings, card headlines |
| `--ev-border` | `#e0e6ec` | Card borders, dividers |
| `--ev-border-light` | `#f0f3f6` | Table row dividers, subtle separators |
| `--ev-green-value` | `#2e7d18` | Green status values |
| `--ev-green-bg` | `#eaf6e2` | Green pill/badge background |
| `--ev-amber` | `#e8a020` | Amber accent borders |
| `--ev-amber-value` | `#92620a` | Amber status values |
| `--ev-amber-bg` | `#fef3dd` | Amber pill/badge background |
| `--ev-red` | `#c83232` | Blocker/red status values and borders |
| `--ev-red-bg` | `#fde8e8` | Blocker pill/badge background |
| `--ev-highlight-bg` | `#f0fae8` | Highlight box background |
| `--ev-highlight-border` | `#b8e09c` | Highlight box border |
| `--ev-tag-bg` | `#eef4fb` | OKR tag background |
| `--ev-tag-text` | `#3a6285` | OKR tag text |

#### Typography

- Font stack: `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
- Base size: `14px`, line-height: `1.55`
- Section titles: `11px`, uppercase, `letter-spacing: 0.08em`, color `--ev-green`
- Labels: `11px`, uppercase, `font-weight: 600`, color `--ev-text-secondary`

#### Components

- **Cards**: `background: #fff`, `border: 1px solid #e0e6ec`, `border-radius: 10px`, `padding: 16px–20px`
- **Pills/Badges**: `border-radius: 20px`, `font-size: 11px`, `font-weight: 700`, `padding: 2px 9px`
- **Header**: Dark bar `#1c2b3a`, logo with green mark `#6cbf3e`
- **Tabs**: Inside header bar, active state uses green underline + green text
- **Flag cards**: Left border `4px` colored by severity (red for blocker, amber for warning)
- **Tables**: Clean borders, uppercase `11px` headers, `#f8f9fa` header background
- **Metric cards**: Large `32px` value, small uppercase label above, subtitle below
- **Buttons**: Follow Bootstrap conventions but use `--ev-green` as primary color

#### Layout Rules

- Max content width: `1280px`, centered
- Grid gaps: `14px` standard
- Padding: `28px 32px` for main content, `20px 32px` for header
- Mobile breakpoint at `700px`: single column, reduced padding
