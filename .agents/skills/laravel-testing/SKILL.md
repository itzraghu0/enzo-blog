---
name: laravel-testing
description: Guide for writing and running tests for the Laravel LMS project. Use this when creating unit tests, feature tests, or setting up test configurations.
---

# Laravel Testing

This project uses PHPUnit and Pest for testing. Tests are located in `tests/` directory.

## Test Structure

```
tests/
├── Feature/          # Feature tests (test full workflows)
├── Unit/             # Unit tests (test individual components)
├── CreatesApplication.php  # Test setup trait
└── TestCase.php      # Base test class
```

## Creating Tests

```bash
# Feature test
php artisan make:test LetterControllerTest

# Unit test
php artisan make:test UserModelTest --unit

# Model test
php artisan make:test Models/LetterTest --unit
```

## Base Test Class

Extend the project's `TestCase`:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup code for all tests
    }
}
```

## Feature Tests (Full Workflows)

Test complete features including HTTP requests:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Letter;
use App\Models\Firm;
use Tests\TestCase;

class LetterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_can_view_letters_list()
    {
        Letter::factory()->count(3)->create();

        $response = $this->get(route('admin.letters'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $response->assertViewCount('items', 3);
    }

    public function test_can_create_letter()
    {
        $firm = Firm::factory()->create();

        $response = $this->post(route('admin.letters.store'), [
            'name' => 'Test Letter',
            'letter_type_id' => 1,
            'firm_id' => $firm->id,
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.letters'));
        $this->assertDatabaseHas('letters', ['name' => 'Test Letter']);
    }

    public function test_validation_fails_without_required_fields()
    {
        $response = $this->post(route('admin.letters.store'), [
            'name' => '',  // Missing required field
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_update_letter()
    {
        $letter = Letter::factory()->create();

        $response = $this->put(route('admin.letters.update', $letter), [
            'name' => 'Updated Name',
            'status' => 'sent',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_letter()
    {
        $letter = Letter::factory()->create();

        $response = $this->delete(route('admin.letters.destroy', $letter));

        $response->assertRedirect();
        $this->assertModelMissing($letter);
    }

    public function test_unauthorized_user_cannot_access()
    {
        $this->actingAs(User::factory()->viewer()->create());

        $response = $this->post(route('admin.letters.store'), []);

        $response->assertForbidden();
    }
}
```

## Unit Tests (Individual Components)

Test models, services, and business logic:

```php
<?php

namespace Tests\Unit;

use App\Models\Letter;
use App\Models\Firm;
use PHPUnit\Framework\TestCase;

class LetterTest extends TestCase
{
    public function test_letter_has_correct_relationships()
    {
        $letter = Letter::factory()
            ->for(Firm::factory())
            ->create();

        $this->assertInstanceOf(Firm::class, $letter->firm);
    }

    public function test_letter_belongs_to_firm()
    {
        $firm = Firm::factory()->create();
        $letter = Letter::factory()->for($firm)->create();

        $this->assertTrue($letter->firm->is($firm));
    }

    public function test_letter_has_documents()
    {
        $letter = Letter::factory()
            ->has(Document::factory()->count(3))
            ->create();

        $this->assertCount(3, $letter->documents);
    }
}
```

## Service Tests

Test business logic:

```php
<?php

namespace Tests\Unit;

use App\Services\LetterService;
use App\Models\Letter;
use App\Models\Firm;
use Tests\TestCase;

class LetterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LetterService $letterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->letterService = app(LetterService::class);
    }

    public function test_create_letter_saves_to_database()
    {
        $firm = Firm::factory()->create();

        $letter = $this->letterService->createLetter([
            'name' => 'Test Letter',
            'type_id' => 1,
            'firm_id' => $firm->id,
        ]);

        $this->assertInstanceOf(Letter::class, $letter);
        $this->assertDatabaseHas('letters', [
            'name' => 'Test Letter',
            'firm_id' => $firm->id,
        ]);
    }

    public function test_archive_old_letters()
    {
        Letter::factory()
            ->create(['updated_at' => now()->subDays(100)]);

        Letter::factory()
            ->create(['updated_at' => now()]);

        $archived = $this->letterService->archiveOldLetters(daysOld: 90);

        $this->assertEquals(1, $archived);
    }
}
```

## API Tests

Test API endpoints:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Letter;
use Tests\TestCase;

class LetterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_letters()
    {
        $user = User::factory()->admin()->create();
        Letter::factory()->count(5)->create();

        $response = $this->actingAs($user)->getJson('/api/v1/letters');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_can_create_letter_via_api()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/letters', [
            'name' => 'API Letter',
            'type_id' => 1,
            'firm_id' => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name']);
    }

    public function test_invalid_data_returns_validation_errors()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/letters', [
            'name' => '',  // Invalid
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
```

## Factory Usage

Factories help create test data:

```php
// Create single model
$letter = Letter::factory()->create();

// Create with specific attributes
$letter = Letter::factory()->create([
    'status' => 'sent',
    'firm_id' => $firm->id,
]);

// Create multiple
$letters = Letter::factory()->count(10)->create();

// Create with relationships
$letter = Letter::factory()
    ->for(Firm::factory())
    ->create();

// Create with related collection
$letter = Letter::factory()
    ->has(Document::factory()->count(3))
    ->create();
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/LetterControllerTest.php

# Run specific test method
php artisan test tests/Feature/LetterControllerTest.php --filter test_can_create_letter

# Run with coverage
php artisan test --coverage

# Run only unit tests
php artisan test tests/Unit

# Run only feature tests
php artisan test tests/Feature

# Verbose output
php artisan test --verbose
```

## Assertions Reference

```php
// Database assertions
$this->assertDatabaseHas('table', ['column' => 'value']);
$this->assertDatabaseMissing('table', ['column' => 'value']);
$this->assertModelMissing($model);

// HTTP assertions
$response->assertStatus(200);
$response->assertOk();
$response->assertCreated();
$response->assertNotFound();
$response->assertForbidden();
$response->assertUnauthorized();

// View assertions
$response->assertViewIs('admin.letters.index');
$response->assertViewHas('items');
$response->assertViewCount('items', 5);

// Session assertions
$response->assertSessionHas('success');
$response->assertSessionHasErrors(['name']);

// JSON assertions
$response->assertJson(['id' => 1]);
$response->assertJsonStructure(['data' => ['id', 'name']]);
$response->assertJsonCount(5, 'data');

// Redirect assertions
$response->assertRedirect('/admin/letters');
$response->assertRedirectToRoute('admin.letters');

// Collection assertions
$this->assertCount(5, $collection);
$this->assertTrue($collection->contains($item));
```

## Best Practices for LMS Testing

1. **Use RefreshDatabase**: Ensure clean database for each test
2. **User authentication**: Use `actingAs()` for authenticated requests
3. **Factories**: Use factories instead of creating models directly
4. **Isolated tests**: Each test should be independent
5. **Clear naming**: Test method names should describe what they test
6. **Arrange-Act-Assert**: Structure tests clearly
7. **Test edge cases**: Test validation, permissions, edge cases
8. **Mock external services**: Mock API calls and notifications
9. **Coverage**: Aim for >80% code coverage
10. **Database transactions**: Use transactions for faster test execution
