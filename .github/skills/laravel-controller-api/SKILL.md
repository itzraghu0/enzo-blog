---
name: laravel-controller-api
description: Guide for creating and managing Laravel controllers and API endpoints for the LMS project. Use this when building controllers, creating API routes, or implementing resource actions.
---

# Laravel Controller & API Development

## Creating Controllers

When asked to create a controller for the LMS, use artisan:

```bash
# Basic controller
php artisan make:controller ControllerName

# Resource controller (REST endpoints)
php artisan make:controller ControllerName --resource

# API resource controller (for API-only operations)
php artisan make:controller ControllerName --api

# With model binding
php artisan make:controller ControllerName --model=ModelName
```

## Controller Structure for LMS

Follow this structure for admin panel controllers:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YourModel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class YourModelController extends Controller
{
    public function index(): View
    {
        $items = YourModel::paginate(15);
        return view('admin.your-model.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.your-model.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field1' => 'required|string|max:255',
            'field2' => 'required|unique:your_models',
        ]);

        YourModel::create($validated);

        return redirect()->route('admin.your-model')
            ->with('success', 'Created successfully');
    }

    public function edit(YourModel $model): View
    {
        return view('admin.your-model.edit', compact('model'));
    }

    public function update(Request $request, YourModel $model): RedirectResponse
    {
        $validated = $request->validate([
            'field1' => 'required|string|max:255',
            'field2' => "required|unique:your_models,field2,{$model->id}",
        ]);

        $model->update($validated);

        return redirect()->route('admin.your-model')
            ->with('success', 'Updated successfully');
    }

    public function destroy(YourModel $model): RedirectResponse
    {
        $model->delete();

        return redirect()->route('admin.your-model')
            ->with('success', 'Deleted successfully');
    }
}
```

## API Resource Controllers

For API endpoints, return JSON responses:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YourModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class YourModelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(YourModel::paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field1' => 'required|string|max:255',
            'field2' => 'required|unique:your_models',
        ]);

        $model = YourModel::create($validated);

        return response()->json($model, 201);
    }

    public function show(YourModel $model): JsonResponse
    {
        return response()->json($model);
    }

    public function update(Request $request, YourModel $model): JsonResponse
    {
        $validated = $request->validate([
            'field1' => 'required|string|max:255',
            'field2' => "required|unique:your_models,field2,{$model->id}",
        ]);

        $model->update($validated);

        return response()->json($model);
    }

    public function destroy(YourModel $model): JsonResponse
    {
        $model->delete();

        return response()->json(null, 204);
    }
}
```

## Route Organization for LMS

Structure routes in files:

- `routes/web.php` - Admin panel and web routes
- `routes/api.php` - API endpoints

### Web Routes Pattern

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('your-model', YourModelController::class);

    // Custom routes
    Route::get('letters/search', [LetterController::class, 'search'])
        ->name('letters.search');
});
```

### API Routes Pattern

```php
Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::apiResource('your-model', Api\YourModelController::class);
});
```

## Controller Middleware

Apply middleware in controller constructor:

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
}
```

## Best Practices for This LMS Project

1. **Consistent naming**: Use ResourceController naming (plural for routes, singular for class names)
2. **Type hints**: Always use return type hints for better clarity
3. **Request validation**: Extract validation rules into FormRequest classes for complex rules
4. **Error handling**: Return appropriate HTTP status codes (200, 201, 400, 404, 422, 500)
5. **Response consistency**: Always return consistent response structures (especially for API)
6. **Authorization**: Use Laravel gates and policies for authorization checks
7. **Eager loading**: Use `with()` to prevent N+1 queries when loading relationships
8. **Redirect messages**: Use session flash messages for user feedback
