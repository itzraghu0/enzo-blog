<?php

namespace App\Services;

use Illuminate\Support\Str;

class SlugService
{
    public function make(string $value): string
    {
        return Str::slug($value) ?: 'item';
    }

    public function unique(string $value, callable $exists): string
    {
        $base = $this->make($value);
        $slug = $base;
        $index = 2;

        while ($exists($slug)) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}
