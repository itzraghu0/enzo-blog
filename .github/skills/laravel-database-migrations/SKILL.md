---
name: laravel-database-migrations
description: Guide for creating and managing Laravel database migrations for the LMS project. Use this when creating database schema changes, migrations, or modifying table structures.
---

# Laravel Database Migrations

## Creating Migrations

When asked to create a migration, use artisan:

```bash
# For creating a new table
php artisan make:migration create_table_name_table

# For modifying an existing table
php artisan make:migration add_fields_to_table_name_table

# For both model and migration
php artisan make:model ModelName -m
```

## Migration File Structure

Migration files are stored in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('your_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('firm_id')->constrained();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('your_models');
    }
};
```

## Column Types for LMS

```php
// IDs and relationships
$table->id();                              // Auto-incrementing ID
$table->foreignId('firm_id')->constrained();  // Foreign key
$table->unsignedBigInteger('user_id');    // Alternative foreign key

// Text
$table->string('name', 255);               // VARCHAR(255)
$table->text('description');               // TEXT
$table->char('code', 10);                  // Fixed length

// Numbers (customer numbers, contract numbers, etc)
$table->string('customer_number')->unique();  // Unique identifier
$table->integer('sequence_number');        // Sequential number
$table->decimal('amount', 10, 2);          // Money values

// Enums/Status
$table->enum('status', ['draft', 'sent', 'received'])->default('draft');
$table->enum('role', ['admin', 'user', 'viewer'])->default('user');

// Booleans
$table->boolean('is_active')->default(true);
$table->boolean('is_primary')->default(false);

// Dates/Times
$table->date('date_of_birth');
$table->datetime('scheduled_at')->nullable();
$table->timestamps();                      // created_at, updated_at
$table->softDeletes();                     // Soft delete support

// JSON
$table->json('metadata')->nullable();
$table->jsonb('settings')->default('{}');

// File paths
$table->string('document_path')->nullable();
$table->string('file_name');
```

## Indexes and Keys

```php
// Single column
$table->index('status');
$table->unique('email');

// Multiple columns
$table->index(['firm_id', 'status']);
$table->unique(['email', 'firm_id']);

// Foreign keys
$table->foreignId('firm_id')->constrained();
$table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');

// Text search (FULLTEXT)
$table->fullText(['name', 'description']);
```

## Modifying Existing Tables

```php
// Add columns
php artisan make:migration add_columns_to_letters_table

public function up(): void
{
    Schema::table('letters', function (Blueprint $table) {
        $table->string('tracking_number')->unique()->after('id');
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::table('letters', function (Blueprint $table) {
        $table->dropColumn('tracking_number');
        $table->dropSoftDeletes();
    });
}
```

## Common LMS Table Structures

### Letters Table

```php
Schema::create('letters', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('letter_type_id')->constrained();
    $table->foreignId('letter_case_id')->constrained();
    $table->foreignId('firm_id')->constrained();
    $table->foreignId('assigned_to')->nullable()->constrained('users');
    $table->enum('status', ['draft', 'sent', 'received', 'archived'])->default('draft');
    $table->date('sent_date')->nullable();
    $table->date('received_date')->nullable();
    $table->timestamps();
    $table->index(['status', 'firm_id']);
});
```

### Users Table (if extending default)

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'user', 'viewer'])->default('user');
    $table->foreignId('firm_id')->nullable()->constrained();
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
});
```

### Documents Table

```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('file_path');
    $table->string('file_type');
    $table->unsignedBigInteger('file_size');
    $table->foreignId('letter_id')->nullable()->constrained()->onDelete('cascade');
    $table->foreignId('uploaded_by')->constrained('users');
    $table->timestamps();
    $table->index('letter_id');
});
```

## Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh (rollback + migrate)
php artisan migrate:refresh

# Refresh with seeding
php artisan migrate:refresh --seed

# Show migration status
php artisan migrate:status
```

## Seeding Tables

Create seeders for test data:

```bash
php artisan make:seeder YourModelSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\YourModel;
use Illuminate\Database\Seeder;

class YourModelSeeder extends Seeder
{
    public function run(): void
    {
        YourModel::factory()->count(50)->create();
    }
}
```

Register in `DatabaseSeeder`:

```php
public function run(): void
{
    $this->call([
        YourModelSeeder::class,
    ]);
}
```

## Best Practices for This LMS

1. **Naming**: Use plural snake_case for table names (`letters`, `customer_numbers`)
2. **Foreign keys**: Always add foreign key constraints for relational integrity
3. **Indexes**: Add indexes to frequently queried columns (status, firm_id, dates)
4. **Soft deletes**: Consider `softDeletes()` for audit trails instead of hard deletes
5. **Timestamps**: Always include `timestamps()` for created_at and updated_at
6. **Nullable**: Use `->nullable()` for optional fields
7. **Defaults**: Set appropriate defaults for status and boolean fields
8. **Type consistency**: Use `decimal` for money, `string` for unique identifiers
9. **Rollback safety**: Always implement complete `down()` methods
10. **Testing**: Use fresh migrations in tests with `RefreshDatabase` trait
