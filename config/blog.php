<?php

use App\Support\BlogLocale;

return [
    'default_locale' => env('BLOG_DEFAULT_LOCALE', 'en'),
    'supported_locales' => BlogLocale::ORDER,
    'media_disk' => env('BLOG_MEDIA_DISK', 'public'),
    'media_directory' => env('BLOG_MEDIA_DIRECTORY', 'media/blog'),
];
