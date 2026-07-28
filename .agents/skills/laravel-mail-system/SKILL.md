---
name: laravel-mail-system
description: Guide for creating and managing Laravel mailable classes for email notifications in the LMS. Use this when setting up email templates, creating mailable classes, or sending notifications.
---

# Laravel Mail System

## Creating Mailable Classes

When asked to create email functionality, use artisan:

```bash
php artisan make:mail MailableName
```

This project already has a base mailable pattern in `App\Mail\BaseMailable`.

## Mailable Class Structure

Based on this project's structure, follow this pattern:

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class YourNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user,
        public $data
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Email Subject',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.your-notification',
            with: [
                'user' => $this->user,
                'data' => $this->data,
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
```

## Using BaseMailable for Consistency

This project has a `BaseMailable` class. Extend it for consistent styling:

```php
<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;

class LetterNotificationMail extends BaseMailable implements ShouldQueue
{
    public function __construct(
        public $letter,
        public $recipientName
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: "Letter Created: {$this->letter->name}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.letter-created',
            with: [
                'letter' => $this->letter,
                'recipientName' => $this->recipientName,
            ]
        );
    }
}
```

## Email Templates Location

Email blade templates go in `resources/views/emails/`:

```blade
<!-- resources/views/emails/letter-created.blade.php -->
<x-mail::message>
# Letter Created

Dear {{ $recipientName }},

A new letter has been created for your review.

**Letter Details:**
- Name: {{ $letter->name }}
- Type: {{ $letter->type->name }}
- Created: {{ $letter->created_at->format('Y-m-d H:i') }}

<x-mail::button :url="route('admin.letters.edit', $letter)">
View Letter
</x-mail::button>

Thanks,
{{ config('app.name') }}
</x-mail::message>
```

## Custom Email Mailable

For custom email designs, use `CustomEmail` class or create specialized mailables:

```php
<?php

namespace App\Mail;

class CustomEmail extends BaseMailable
{
    public function __construct(
        public $to,
        public $subject,
        public $htmlContent
    ) {}

    public function envelope()
    {
        return new Envelope(
            to: $this->to,
            subject: $this->subject,
        );
    }

    public function content()
    {
        return new Content(
            html: $this->htmlContent,
        );
    }
}
```

## Sending Emails

### Using Mail Facade

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\LetterNotificationMail;

// Send immediately
Mail::send(new LetterNotificationMail($letter, $userName));

// Queue for later
Mail::queue(new LetterNotificationMail($letter, $userName));
```

### From Models

```php
// In a model or event
Mail::to($user->email)->send(new LetterNotificationMail($letter, $user->name));
```

## Events That Trigger Emails

Set up email triggers in model observers or events:

```php
// In ModelObserver
public function created(Letter $letter): void
{
    Mail::to($letter->firm->email)
        ->send(new LetterCreatedMail($letter));
}

public function updated(Letter $letter): void
{
    if ($letter->isDirty('status')) {
        Mail::to($letter->assignedUser->email)
            ->send(new LetterStatusChangedMail($letter));
    }
}
```

## Email Configuration

Check `config/mail.php` and `.env` for mail settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lms.local
MAIL_FROM_NAME="LMS System"
```

## Testing Emails

In tests, use Mail facade:

```php
Mail::fake();

// Trigger action
$letter->save();

// Assert mail was sent
Mail::assertSent(LetterNotificationMail::class, function ($mail) {
    return $mail->hasTo('user@example.com');
});
```

## Best Practices for This LMS

1. **Queue emails**: Implement `ShouldQueue` to avoid blocking requests
2. **Consistent styling**: Use the `BaseMailable` and consistent email templates
3. **Translations**: Use Laravel's localization for email content (support for `en`, `de` in this project)
4. **Variables safety**: Always escape variables in blade templates
5. **Subject clarity**: Make email subjects clear and searchable
6. **Attachments**: Include relevant documents/letters when appropriate
7. **Unsubscribe**: Consider adding unsubscribe links for bulk emails
8. **Testing**: Always test email content and headers before deployment
