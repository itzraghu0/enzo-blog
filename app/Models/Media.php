<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'disk',
        'path',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'alt_text',
        'title',
        'caption',
        'seo_keywords',
        'hashtags',
        'relevance_notes',
        'aeo_summary',
        'aeo_questions',
        'geo_summary',
        'geo_entities',
        'geo_prompts',
        'geo_context',
        'collection',
        'locale',
        'mediable_type',
        'mediable_id',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'aeo_questions' => 'array',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function postAttachments(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function url(): string
    {
        return asset($this->path);
    }

    public function variantPath(string $variant): ?string
    {
        if (! array_key_exists($variant, config('blog.media_variants', []))) {
            return null;
        }

        $baseName = pathinfo((string) $this->filename, PATHINFO_FILENAME);
        $directory = trim(config('blog.media_directory', 'media/blog'), '/');
        $path = trim($directory.'/'.$this->getKey().'/'.$variant.'/'.$baseName.'.webp', '/');

        return is_file(public_path($path)) ? $path : null;
    }

    public function variantUrl(string $variant): ?string
    {
        $path = $this->variantPath($variant);

        return $path ? asset($path) : null;
    }

    public function variantUrls(): array
    {
        return collect(array_keys(config('blog.media_variants', [])))
            ->mapWithKeys(fn (string $variant): array => [$variant => $this->variantUrl($variant)])
            ->all();
    }
}
