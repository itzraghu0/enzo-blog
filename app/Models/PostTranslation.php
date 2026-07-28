<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTranslation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'meta_description',
        'og_title',
        'og_description',
        'canonical_url',
        'preview_image_alt',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
