# Agent Instructions for LMS Project

This file provides custom instructions for various AI agents and LLMs working on the Laravel LMS project.

## Project Overview

This is a Laravel-based Learning Management System (LMS) with:

- Multi-language support (English, German)
- Admin panel for managing users, letters, documents, meetings, and more
- Complex data models including firms, parties, customers, contracts
- Mail notifications and email system
- Document management and storage
- Event and meeting management

## Technology Stack

- **Framework**: Laravel 13
- **Frontend**: Blade templates with Tailwind CSS and Keenthemes components
- **Database**: MySQL/PostgreSQL
- **Testing**: PHPUnit and Pest
- **Languages**: PHP 8.2+, JavaScript (ES6+)
- **Localization**: English (en) and German (de)

## Available Agent Skills

The project includes 8 specialized agent skills in `.github/skills/`:

1. **laravel-model-management**
    - Creating Eloquent models
    - Defining relationships (hasMany, belongsTo, belongsToMany)
    - Setting up model observers for auto-generation
    - Database attributes and casting

2. **laravel-controller-api**
    - Building resource controllers
    - Creating API endpoints
    - RESTful routing patterns
    - Proper HTTP status codes

3. **laravel-mail-system**
    - Creating mailable classes
    - Email templates (Blade)
    - Extending BaseMailable
    - Queuing notifications

4. **laravel-validation**
    - Form request validation
    - Custom validation rules
    - Field-specific rules for models
    - API validation responses

5. **laravel-admin-panel**
    - Admin page templates
    - Navigation items in navbar
    - Dropdown menus
    - Form layouts and styling

6. **laravel-database-migrations**
    - Creating migrations
    - Table structure definition
    - Indexes and foreign keys
    - Modifying existing tables

7. **laravel-service-layer**
    - Service class structure
    - Business logic encapsulation
    - Dependency injection
    - Error handling and transactions

8. **laravel-testing**
    - Feature tests (HTTP workflows)
    - Unit tests (components)
    - Test factories
    - PHPUnit/Pest syntax

## Code Style & Conventions

### Naming

| Type        | Style              | Example                                  |
| ----------- | ------------------ | ---------------------------------------- |
| Controllers | Singular           | `LetterController`                       |
| Models      | Singular           | `Letter`, `Firm`                         |
| Tables      | Plural, snake_case | `letters`, `user_roles`                  |
| Routes      | Kebab-case         | `admin/letters`, `admin/customer-number` |
| Variables   | camelCase          | `$letterData`, `$firmId`                 |
| Constants   | UPPER_SNAKE_CASE   | `ROLE_ADMIN`, `STATUS_ACTIVE`            |

### Directory Structure

```
app/
├── Console/Commands/
├── Http/
│   ├── Controllers/
│   │   └── Admin/              # Admin panel controllers
│   ├── Middleware/
│   └── Requests/               # Form request validation
├── Mail/                       # Mailable classes
├── Models/                     # Eloquent models
├── Observers/                  # Model observers
├── Providers/
├── Rules/                      # Custom validation rules
└── Services/                   # Service layer classes
database/
├── migrations/
├── seeders/
└── factories/
resources/
├── views/
│   ├── admin/                  # Admin panel templates
│   ├── emails/                 # Email templates
│   └── layouts/
├── css/
└── js/
tests/
├── Feature/                    # Feature tests
└── Unit/                       # Unit tests
```

## PHP Standards

### Type Hints (Always Use)

```php
public function index(): View
public function store(Request $request): RedirectResponse
public function getData(): Collection
```

### Imports (Group Logically)

```php
// 1. Framework
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

// 2. App
use App\Models\Letter;
use App\Services\LetterService;
```

### Model Definition

```php
protected $fillable = ['name', 'status'];
protected $casts = ['created_at' => 'datetime', 'is_active' => 'boolean'];
```

### Constructor Dependency Injection

```php
public function __construct(private LetterService $service) {}
```

### Method Order in Classes

1. Properties
2. Constructor
3. Public methods
4. Protected methods
5. Private methods

## Blade Templates

- Use `{{ $variable }}`
- Translate user-facing text: `{{ __('key') }}`
- Never output raw HTML without sanitization
- Use 4-space indentation
- Prefer Laravel components and custom Blade components

## Route Organization

```php
// Admin routes
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('letters', LetterController::class);
});

// API routes
Route::prefix('api/v1')->group(function () {
    Route::apiResource('letters', Api\LetterController::class);
});
```

## Database Patterns

### Migration Template

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('firm_id')->constrained();
    $table->enum('status', ['draft', 'active'])->default('draft');
    $table->timestamps();
    $table->index('status');
});
```

### Model Template

```php
class Letter extends Model {
    protected $fillable = ['name', 'status', 'firm_id'];
    protected $casts = ['created_at' => 'datetime'];

    public function firm(): BelongsTo {
        return $this->belongsTo(Firm::class);
    }
}
```

## Core Domain Models

### Main Entities

- **User**: System users with roles
- **Firm**: Organizations
- **Letter**: Primary domain model
- **Document**: Attachments
- **Party**: Legal parties
- **Meeting**: Scheduled meetings
- **Contact**: Contact information

### Auto-Generated Number Models (via Observers)

- **CustomerNumber**
- **ContractNumber**
- **FileNumber**
- **AccountNumber**
- **TaxNumber**
- **SubjectNumber**

## Localization

- **Supported Languages**: English (`en`), German (`de`)
- **Files Location**: `lang/{locale}/`
- **Always Use**: `__('key')` helper for user-facing text

## Authentication & Authorization

- Use Laravel's built-in authentication system
- Implement permissions using gates and policies
- Check roles in middleware and authorization logic

**User Roles:**

- **Admin**: Full system access
- **User**: Firm-limited access
- **Viewer**: Read-only access

## Best Practices by Component

### Controllers

- ✅ Validate input using Form Requests
- ✅ Keep controllers lean
- ✅ Move business logic to services
- ✅ Use route model binding
- ✅ Return appropriate HTTP status codes
- ❌ Don't put complex logic in controllers

### Services

- ✅ Encapsulate business logic
- ✅ Handle multi-model operations
- ✅ Manage database transactions
- ✅ Throw meaningful exceptions
- ✅ Write unit tests
- ❌ Don't call services from other services directly without dependency injection

### Models

- ✅ Define relationships with type hints
- ✅ Use observers for auto-generation
- ✅ Cast attributes appropriately
- ✅ Document complex relationships
- ❌ Don't put business logic in models

### Forms & Validation

- ✅ Use Form Request classes
- ✅ Include CSRF tokens: `@csrf`
- ✅ Show validation errors clearly
- ✅ Use flash messages: `with('success', 'message')`
- ✅ Make forms accessible with labels

### Testing

- ✅ Test happy paths and edge cases
- ✅ Test authorization separately
- ✅ Use factories for test data
- ✅ Use `Mail::fake()` for email testing
- ✅ Aim for >80% code coverage
- ❌ Don't test framework code

## Error Handling & Status Codes

| Status | Use                      | Example           |
| ------ | ------------------------ | ----------------- |
| 200    | Successful GET/PUT/PATCH | Fetch data        |
| 201    | Successful POST          | Create resource   |
| 204    | Successful DELETE        | No content        |
| 400    | Bad request              | Malformed request |
| 401    | Unauthorized             | Missing auth      |
| 403    | Forbidden                | Permission denied |
| 404    | Not found                | Resource missing  |
| 422    | Unprocessable            | Validation error  |
| 500    | Server error             | Log, don't expose |

## Performance Optimization

### Queries

- Use eager loading: `Model::with('relationship')->get()`
- Add database indexes on foreign keys and search columns
- Paginate large result sets
- Avoid N+1 queries

### Caching

- Cache frequently accessed data
- Use cache tags for related data
- Clear cache on updates

### Tasks

- Queue long-running operations (mail, file processing)
- Use `ShouldQueue` interface
- Implement retry logic

## File Management

### Storage

- Store in `storage/` directory
- Use `Storage` facade
- Validate file types and sizes
- Generate unique filenames
- Clean up old files

### Uploads

```php
$path = $file->store("letters/{$letter->id}", 'private');
```

## Email & Notifications

### Mailable Classes

```php
class LetterCreatedMail extends BaseMailable {
    public function __construct(public $letter) {}

    public function content() {
        return new Content(view: 'emails.letter-created');
    }
}
```

### Sending

```php
Mail::to($user->email)->send(new LetterCreatedMail($letter));
Mail::to($user->email)->queue(new LetterCreatedMail($letter));
```

### Testing

```php
Mail::fake();
// Trigger action
Mail::assertSent(LetterCreatedMail::class);
```

## Common Artisan Commands

```bash
# Generate Resources
php artisan make:model ModelName -mco          # Model + Migration + Controller + Observer
php artisan make:controller Admin/Name --resource
php artisan make:request StoreNameRequest
php artisan make:mail MailName
php artisan make:test FeatureTest

# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh --seed
php artisan db:seed --class=SpecificSeeder

# Testing
php artisan test
php artisan test tests/Feature/Test.php
php artisan test --coverage

# Optimization
php artisan cache:clear
php artisan route:cache
php artisan config:cache

# Development
php artisan serve
```

## Configuration Files

| File                     | Purpose                              |
| ------------------------ | ------------------------------------ |
| `.env`                   | Environment variables (never commit) |
| `config/app.php`         | App name, locale, timezone           |
| `config/mail.php`        | Email configuration                  |
| `config/database.php`    | Database connections                 |
| `config/filesystems.php` | Storage disk setup                   |
| `config/auth.php`        | Authentication settings              |

## Project-Specific Rules

1. **Always extend base classes** - Models extend `Model`, Controllers extend `Controller`
2. **Use observers** - Auto-generate numbers via observers, not in controllers
3. **Keep business logic in services** - Never put complex logic in controllers
4. **Always validate** - Use Form Requests for all user input
5. **Test everything** - Every feature should have tests
6. **Support localization** - Use `__()` for all user-facing text
7. **Be consistent** - Follow existing patterns in the codebase
8. **Document decisions** - Add comments for non-obvious code
9. **Clean up** - Remove unused code and imports
10. **Security first** - Validate input, escape output, use CSRF tokens

## When to Use Agent Skills

Tell me to use a specific skill when you need:

- **laravel-model-management** → "Create a new model with relationships"
- **laravel-controller-api** → "Build a REST controller"
- **laravel-mail-system** → "Set up email notifications"
- **laravel-validation** → "Add form validation"
- **laravel-admin-panel** → "Create an admin page"
- **laravel-database-migrations** → "Create a migration"
- **laravel-service-layer** → "Build a service class"
- **laravel-testing** → "Write tests"

## Unique Project Characteristics

### Number Auto-Generation

This project uses model observers to auto-generate unique number sequences:

- Customer Numbers
- Contract Numbers
- File Numbers
- Account Numbers
- Tax Numbers
- Subject Numbers

Always implement these via observers, never in controllers.

### Admin Panel Navigation

The admin panel uses Keenthemes-based menus with:

- Dropdown support
- Active state highlighting via `Helper::setActive()`
- Icon system (ki-filled icons)
- Mobile responsive design

### Multi-Language Support

The project supports English and German. Always:

- Use `__('key')` for user-facing text
- Store translations in `lang/{locale}/` directories
- Test with both languages

### Data Relationships

The LMS has complex relationships:

- Firms contain multiple Letters, Users, Parties
- Letters can have multiple Documents
- Parties have PartyMembers
- Users have assigned Firms
- Letters have assigned Users

Always use eager loading to prevent N+1 queries.

## Additional Resources

- [Laravel Official Documentation](https://laravel.com/docs)
- [PSR-12 Code Style Guide](https://www.php-fig.org/psr/psr-12/)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- Project README.md for setup instructions
- `.github/skills/` for specialized guidance files

## Code Quality Checklist

Before submitting code:

- [ ] Type hints on all methods
- [ ] Error handling implemented
- [ ] Validation in place
- [ ] Tests written
- [ ] Localization strings used
- [ ] Performance optimized (eager loading, indexes)
- [ ] Security considerations addressed
- [ ] Comments on complex logic
- [ ] Follows project conventions
- [ ] No dead code or unused imports
