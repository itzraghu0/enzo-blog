<?php

use App\Support\BlogLocale;

return [
    'default_locale' => env('BLOG_DEFAULT_LOCALE', BlogLocale::GERMAN),
    'supported_locales' => BlogLocale::ORDER,
    'media_disk' => env('BLOG_MEDIA_DISK', 'public'),
    'media_directory' => env('BLOG_MEDIA_DIRECTORY', 'media/blog'),
    'media_variants' => [
        'lg' => ['width' => 1200, 'quality' => 82],
        'md' => ['width' => 768, 'quality' => 82],
        'sm' => ['width' => 360, 'quality' => 80],
    ],
];
