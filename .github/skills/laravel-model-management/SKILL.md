---
name: laravel-model-management
description: Guide for creating and managing Laravel Eloquent models with observers, relationships, and database attributes. Use this when working with Laravel models, creating new models, adding relationships, or setting up model observers for this LMS project.
---

# Laravel Model Management

When working with Laravel models in this LMS project, follow these practices:

## Model Creation with Artisan

When asked to create a new model, use the Laravel artisan command:

```bash
php artisan make:model ModelName -mco
```

The flags mean:

- `-m` creates a migration
- `-c` creates a controller
- `-o` creates an observer

## Model Structure

Follow this structure for models:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class YourModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'field1',
        'field2',
        'field3',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define relationships
    public function relatedModel(): BelongsTo
    {
        return $this->belongsTo(RelatedModel::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChildModel::class);
    }

    // Add any custom methods
    public function someMethod()
    {
        // Implementation
    }
}
```

## Model Observers

When models need observers (for auto-generated data like numbers), create them using artisan:

```bash
php artisan make:observer ModelNameObserver --model=ModelName
```

Observers in this project (like `AccountNumberObserver`, `ContractNumberObserver`) should handle:

- Auto-generating number sequences
- Updating related data on creation/update
- Validation before deletion
- Logging important changes

Example observer pattern:

```php
<?php

namespace App\Observers;

use App\Models\YourModel;

class YourModelObserver
{
    public function creating(YourModel $model): void
    {
        // Set default values, generate unique numbers, etc.
    }

    public function created(YourModel $model): void
    {
        // Perform actions after model is created
    }

    public function updating(YourModel $model): void
    {
        // Validate and prepare updates
    }

    public function deleting(YourModel $model): void
    {
        // Check if model can be deleted
    }
}
```

## Relationships Patterns

Based on this LMS project structure, use these relationship patterns:

- **belongsTo**: Use for models that belong to a parent (e.g., `Letter` belongs to `LetterType`)
- **hasMany**: Use for one-to-many relationships (e.g., `Firm` has many `Letters`)
- **belongsToMany**: Use for many-to-many relationships with pivot tables
- **hasManyThrough**: Use for accessing distant relationships (e.g., `Firm` → `Letters` → `Documents`)

## Registering Observers

Register observers in `AppServiceProvider`:

```php
use App\Models\YourModel;
use App\Observers\YourModelObserver;

public function boot(): void
{
    YourModel::observe(YourModelObserver::class);
}
```

## Database Attributes

Define protected $casts for proper data type handling:

```php
protected $casts = [
    'is_active' => 'boolean',
    'status' => 'string',
    'meta_data' => 'array',
    'created_at' => 'datetime',
];
```

## Best Practices for This Project

1. Always define `$fillable` to prevent mass assignment vulnerabilities
2. Use mutators/accessors for data transformation when needed
3. Add proper indexes to frequently queried columns in migrations
4. Document complex relationships with comments
5. Keep model files focused on their primary responsibility
6. Use type hints for better IDE autocomplete and code quality
