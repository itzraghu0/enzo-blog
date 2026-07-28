<?php

return [
    'default_locale' => env('BLOG_DEFAULT_LOCALE', 'en'),
    'supported_locales' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('BLOG_SUPPORTED_LOCALES', 'en,de'))
    ))),
    'media_disk' => env('BLOG_MEDIA_DISK', 'public'),
    'media_directory' => env('BLOG_MEDIA_DIRECTORY', 'blog'),
];
