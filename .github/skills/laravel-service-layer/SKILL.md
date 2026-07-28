---
name: laravel-service-layer
description: Guide for building service classes and business logic layer for the LMS project. Use this when creating service classes, managing complex business logic, or implementing domain operations.
---

# Laravel Service Layer

## Service Layer Purpose

Service classes encapsulate business logic, making controllers thin and keeping the codebase organized. This project has a `app/Services/` directory for service classes.

## Creating Service Classes

Service classes should handle:

- Complex business logic
- Multiple model operations
- External API integrations
- Data transformations
- Domain-specific operations

## Service Class Structure

```php
<?php

namespace App\Services;

use App\Models\Letter;
use App\Models\Firm;
use Exception;
use Illuminate\Support\Collection;

class LetterService
{
    /**
     * Create a new letter with related data
     */
    public function createLetter(array $data): Letter
    {
        try {
            $letter = Letter::create([
                'name' => $data['name'],
                'letter_type_id' => $data['type_id'],
                'firm_id' => $data['firm_id'],
                'status' => 'draft',
            ]);

            // Perform related operations
            if (isset($data['assignee_id'])) {
                $this->assignLetter($letter, $data['assignee_id']);
            }

            if (isset($data['tags'])) {
                $letter->tags()->sync($data['tags']);
            }

            return $letter;
        } catch (Exception $e) {
            throw new Exception("Failed to create letter: {$e->getMessage()}");
        }
    }

    /**
     * Update letter and related data
     */
    public function updateLetter(Letter $letter, array $data): Letter
    {
        $letter->update([
            'name' => $data['name'] ?? $letter->name,
            'letter_type_id' => $data['type_id'] ?? $letter->letter_type_id,
            'status' => $data['status'] ?? $letter->status,
        ]);

        if (isset($data['tags'])) {
            $letter->tags()->sync($data['tags']);
        }

        return $letter;
    }

    /**
     * Assign letter to user
     */
    public function assignLetter(Letter $letter, int $userId): void
    {
        $letter->update(['assigned_to' => $userId]);

        // Send notification to user
        // notify()->letterAssigned($letter, $userId);
    }

    /**
     * Get letters by firm with filters
     */
    public function getLettersByFirm(Firm $firm, array $filters = []): Collection
    {
        $query = $firm->letters();

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['type_id'] ?? null) {
            $query->where('letter_type_id', $filters['type_id']);
        }

        if ($filters['search'] ?? null) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->get();
    }

    /**
     * Archive old letters
     */
    public function archiveOldLetters(int $daysOld = 90): int
    {
        return Letter::where('updated_at', '<', now()->subDays($daysOld))
            ->where('status', '!=', 'archived')
            ->update(['status' => 'archived']);
    }

    /**
     * Generate report
     */
    public function generateReport(Firm $firm, string $period = 'month'): array
    {
        $date = $period === 'month' ? now()->subMonth() : now()->subYear();

        return [
            'total_letters' => $firm->letters()->where('created_at', '>=', $date)->count(),
            'sent_letters' => $firm->letters()->where('status', 'sent')->where('created_at', '>=', $date)->count(),
            'received_letters' => $firm->letters()->where('status', 'received')->where('created_at', '>=', $date)->count(),
            'pending_letters' => $firm->letters()->where('status', 'draft')->count(),
        ];
    }
}
```

## Using Services in Controllers

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLetterRequest;
use App\Models\Letter;
use App\Services\LetterService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LetterController extends Controller
{
    public function __construct(private LetterService $letterService)
    {
    }

    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            $letter = $this->letterService->createLetter($request->validated());

            return redirect()->route('admin.letters.edit', $letter)
                ->with('success', 'Letter created successfully');
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(StoreLetterRequest $request, Letter $letter): RedirectResponse
    {
        $this->letterService->updateLetter($letter, $request->validated());

        return redirect()->route('admin.letters.edit', $letter)
            ->with('success', 'Letter updated successfully');
    }
}
```

## Service Dependencies

Inject services using constructor dependency injection:

```php
class LetterService
{
    public function __construct(
        private NotificationService $notificationService,
        private DocumentService $documentService,
    ) {}

    public function createLetter(array $data): Letter
    {
        // Use injected services
        $letter = Letter::create($data);

        $this->notificationService->sendCreatedNotification($letter);

        return $letter;
    }
}
```

## Service Examples for LMS

### User Management Service

```php
class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'firm_id' => $data['firm_id'],
        ]);
    }

    public function updateUserRole(User $user, string $role): User
    {
        // Validate role change permissions
        if (auth()->user()->cannot('updateRole', $user)) {
            throw new AuthorizationException('Unauthorized role change');
        }

        $user->update(['role' => $role]);

        return $user;
    }

    public function deactivateUser(User $user): void
    {
        $user->update(['is_active' => false]);
        // Revoke tokens, notify user, etc
    }
}
```

### Document Management Service

```php
class DocumentService
{
    public function uploadDocument(UploadedFile $file, Letter $letter, User $uploader): Document
    {
        $path = $file->store("letters/{$letter->id}", 'private');

        return Document::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'letter_id' => $letter->id,
            'uploaded_by' => $uploader->id,
        ]);
    }

    public function deleteDocument(Document $document): bool
    {
        Storage::disk('private')->delete($document->file_path);
        return $document->delete();
    }
}
```

### Notification Service

```php
class NotificationService
{
    public function notifyLetterCreated(Letter $letter): void
    {
        Mail::to($letter->firm->email)
            ->send(new LetterCreatedMail($letter));
    }

    public function notifyLetterAssigned(Letter $letter, User $assignee): void
    {
        Mail::to($assignee->email)
            ->send(new LetterAssignedMail($letter));
    }

    public function notifyStatusChanged(Letter $letter, string $oldStatus, string $newStatus): void
    {
        // Notify relevant parties
    }
}
```

## Service Binding in Provider

Register services in `AppServiceProvider`:

```php
public function register(): void
{
    $this->app->singleton(LetterService::class);
    $this->app->singleton(UserService::class);
    $this->app->singleton(DocumentService::class);
}
```

## Error Handling in Services

```php
class PaymentService
{
    public function processPayment(Order $order, Payment $payment): bool
    {
        try {
            $response = $this->gateway->charge(
                $payment->amount,
                $payment->token
            );

            if (!$response->successful()) {
                throw new PaymentFailedException($response->error);
            }

            $order->update(['payment_status' => 'completed']);
            return true;

        } catch (PaymentFailedException $e) {
            Log::error("Payment failed for order {$order->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
```

## Best Practices for LMS Services

1. **Single Responsibility**: Each service handles one domain (Users, Letters, Documents)
2. **Testability**: Services make unit testing easy and focused
3. **Reusability**: Services can be used by controllers, commands, jobs, APIs
4. **Error Handling**: Throw meaningful exceptions with clear messages
5. **Transactions**: Wrap multi-step operations in database transactions
6. **Logging**: Log important business operations for audit trails
7. **Dependency Injection**: Always inject dependencies, don't use singletons
8. **Naming**: Use clear, action-based method names (`createLetter`, `updateStatus`, `archive`)
9. **Return types**: Specify return types for IDE support and clarity
10. **Documentation**: Document complex business logic with comments
