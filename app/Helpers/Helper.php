<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;

class Helper
{
    public static function setActive(string $path): string
    {
        $currentPath = request()->path();
        $normalizedPath = trim($path, '/');

        if ($normalizedPath === '') {
            return $currentPath === '/' ? 'active' : '';
        }

        return request()->is($normalizedPath) || request()->is($normalizedPath.'/*')
            ? 'active'
            : '';
    }

    public static function highlightText(?string $text, ?string $keyword): HtmlString|string
    {
        $text ??= '';
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return e($text);
        }

        $escapedText = e($text);
        $escapedKeyword = preg_quote(e($keyword), '/');

        return new HtmlString(
            preg_replace('/('.$escapedKeyword.')/i', '<mark>$1</mark>', $escapedText) ?? $escapedText
        );
    }
}
