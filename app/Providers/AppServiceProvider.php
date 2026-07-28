<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\GeoService;
use App\Services\MediaService;
use App\Services\PostService;
use App\Services\SeoService;
use App\Services\SlugService;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationService::class);
        $this->app->singleton(SlugService::class);
        $this->app->singleton(SeoService::class);
        $this->app->singleton(GeoService::class);
        $this->app->singleton(MediaService::class);
        $this->app->singleton(PostService::class);
        $this->app->singleton(CategoryService::class);
        $this->app->singleton(CommentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        VerifyEmail::createUrlUsing(function ($notifiable): string {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }
}
