<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'is_featured',
        'published_at',
        'country_code',
        'region',
        'city',
        'latitude',
        'longitude',
        'timezone',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category')->withTimestamps();
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function postMedia(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function libraryMedia(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'post_media')
            ->withPivot(['collection', 'locale', 'purpose', 'sort_order'])
            ->withTimestamps();
    }

    public function previewMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('collection', 'preview');
    }

    public function translated(string $locale = null): ?PostTranslation
    {
        $locale ??= config('blog.default_locale');

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('blog.default_locale'))
            ?? $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', config('blog.default_locale'))->first();
    }

    public function translationFor(string $locale = null): ?PostTranslation
    {
        return $this->translated($locale);
    }
}
