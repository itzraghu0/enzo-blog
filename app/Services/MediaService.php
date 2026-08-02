<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MediaService
{
    public function store(UploadedFile $file, ?User $user = null, array $data = []): Media
    {
        if (! $file->isValid()) {
            abort(422, __('The uploaded file is not valid.'));
        }

        $directory = trim(config('blog.media_directory', 'blog'), '/');
        $subDirectory = now()->format('Y/m');
        $targetDirectory = public_path($directory.'/'.$subDirectory);
        File::ensureDirectoryExists($targetDirectory);

        $filename = $this->buildFilename($file);
        $storedPath = trim($directory.'/'.$subDirectory.'/'.$filename, '/');

        $contents = @file_get_contents($file->getRealPath());
        if ($contents === false) {
            abort(422, __('The uploaded file could not be read.'));
        }

        File::put($targetDirectory.'/'.$filename, $contents);

        $media = Media::create([
            'user_id' => $user?->id,
            'disk' => 'public',
            'path' => $storedPath,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
            'alt_text' => $this->blankToNull($data['alt_text'] ?? null),
            'title' => $this->blankToNull($data['title'] ?? null),
            'caption' => $this->blankToNull($data['caption'] ?? null),
            ...$this->metadataPayload($data),
            'collection' => $this->blankToNull($data['collection'] ?? 'default') ?? 'default',
            'locale' => $this->blankToNull($data['locale'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        $this->generateVariants($media);

        return $media;
    }

    public function attach(Media $media, Model $model, string $collection = 'default', ?string $locale = null): Media
    {
        $media->forceFill([
            'mediable_type' => $model::class,
            'mediable_id' => $model->getKey(),
            'collection' => $collection,
            'locale' => $locale,
        ])->save();

        return $media->refresh();
    }

    public function update(Media $media, array $data): Media
    {
        $media->update([
            'alt_text' => $this->blankToNull($data['alt_text'] ?? null),
            'title' => $this->blankToNull($data['title'] ?? null),
            'caption' => $this->blankToNull($data['caption'] ?? null),
            ...$this->metadataPayload($data),
            'collection' => $this->blankToNull($data['collection'] ?? 'default') ?? 'default',
            'locale' => $this->blankToNull($data['locale'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return $media->refresh();
    }

    public function replaceFile(Media $media, UploadedFile $file, ?User $user = null, array $data = []): Media
    {
        if (! $file->isValid()) {
            abort(422, __('The uploaded file is not valid.'));
        }

        $directory = trim(config('blog.media_directory', 'blog'), '/');
        $subDirectory = now()->format('Y/m');
        $targetDirectory = public_path($directory.'/'.$subDirectory);
        File::ensureDirectoryExists($targetDirectory);

        $filename = $this->buildFilename($file);
        $storedPath = trim($directory.'/'.$subDirectory.'/'.$filename, '/');
        $contents = @file_get_contents($file->getRealPath());

        if ($contents === false) {
            abort(422, __('The uploaded file could not be read.'));
        }

        File::put($targetDirectory.'/'.$filename, $contents);
        File::delete(public_path($media->path));
        $this->deleteVariants($media);

        $media->update([
            'user_id' => $user?->id ?? $media->user_id,
            'disk' => 'public',
            'path' => $storedPath,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
            'alt_text' => $this->blankToNull($data['alt_text'] ?? null),
            'title' => $this->blankToNull($data['title'] ?? null),
            'caption' => $this->blankToNull($data['caption'] ?? null),
            ...$this->metadataPayload($data),
            'collection' => $this->blankToNull($data['collection'] ?? 'default') ?? 'default',
            'locale' => $this->blankToNull($data['locale'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        $this->generateVariants($media->refresh());

        return $media->refresh();
    }

    public function duplicateForModel(Media $media, Model $model, string $collection = 'default', ?string $locale = null, array $overrides = []): Media
    {
        $copy = Media::create(array_merge([
            'user_id' => $media->user_id,
            'disk' => $media->disk,
            'path' => $media->path,
            'filename' => $media->filename,
            'original_name' => $media->original_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'alt_text' => $media->alt_text,
            'title' => $media->title,
            'caption' => $media->caption,
            'seo_keywords' => $media->seo_keywords,
            'hashtags' => $media->hashtags,
            'relevance_notes' => $media->relevance_notes,
            'aeo_summary' => $media->aeo_summary,
            'aeo_questions' => $media->aeo_questions,
            'geo_summary' => $media->geo_summary,
            'geo_entities' => $media->geo_entities,
            'geo_prompts' => $media->geo_prompts,
            'geo_context' => $media->geo_context,
            'collection' => $collection,
            'locale' => $locale ?? $media->locale,
            'sort_order' => $media->sort_order,
        ], $overrides));

        $this->generateVariants($copy);

        return $this->attach($copy, $model, $collection, $locale);
    }

    public function delete(Media $media): bool
    {
        $isSharedPath = Media::query()
            ->whereKeyNot($media->getKey())
            ->where('path', $media->path)
            ->exists();

        if (! $isSharedPath) {
            File::delete(public_path($media->path));
        }

        $this->deleteVariants($media);

        return (bool) $media->delete();
    }

    public function paginate(int $perPage = 24, array $filters = []): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $type = trim((string) ($filters['type'] ?? ''));

        return Media::query()
            ->with(['creator', 'mediable'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('original_name', 'like', "%{$search}%")
                        ->orWhere('filename', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('alt_text', 'like', "%{$search}%")
                        ->orWhere('caption', 'like', "%{$search}%")
                        ->orWhere('path', 'like', "%{$search}%")
                        ->orWhere('seo_keywords', 'like', "%{$search}%")
                        ->orWhere('hashtags', 'like', "%{$search}%")
                        ->orWhere('relevance_notes', 'like', "%{$search}%")
                        ->orWhere('aeo_summary', 'like', "%{$search}%")
                        ->orWhere('geo_summary', 'like', "%{$search}%")
                        ->orWhere('geo_entities', 'like', "%{$search}%")
                        ->orWhere('geo_prompts', 'like', "%{$search}%")
                        ->orWhere('geo_context', 'like', "%{$search}%");
                });
            })
            ->when($type === 'image', function ($query): void {
                $query->where('mime_type', 'like', 'image/%');
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function metadataPayload(array $data): array
    {
        return [
            'seo_keywords' => $this->blankToNull($data['seo_keywords'] ?? null),
            'hashtags' => $this->blankToNull($data['hashtags'] ?? null),
            'relevance_notes' => $this->blankToNull($data['relevance_notes'] ?? null),
            'aeo_summary' => $this->blankToNull($data['aeo_summary'] ?? null),
            'aeo_questions' => $this->questionsToArray($data['aeo_questions'] ?? null),
            'geo_summary' => $this->blankToNull($data['geo_summary'] ?? null),
            'geo_entities' => $this->blankToNull($data['geo_entities'] ?? null),
            'geo_prompts' => $this->blankToNull($data['geo_prompts'] ?? null),
            'geo_context' => $this->blankToNull($data['geo_context'] ?? null),
        ];
    }

    private function questionsToArray(mixed $value): ?array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        $value = $this->blankToNull($value);

        if ($value === null) {
            return null;
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $value))));
    }

    private function buildFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';
        $extension = Str::lower($extension);

        return sprintf(
            '%s.%s',
            uniqid('media_', true),
            $extension
        );
    }

    private function generateVariants(Media $media): void
    {
        if (! str_starts_with((string) $media->mime_type, 'image/') || ! function_exists('imagewebp')) {
            return;
        }

        $sourcePath = public_path($media->path);

        if (! is_file($sourcePath)) {
            return;
        }

        $source = $this->createImageResource($sourcePath);

        if ($source === null) {
            return;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth < 1 || $sourceHeight < 1) {
            imagedestroy($source);

            return;
        }

        foreach (config('blog.media_variants', []) as $variant => $settings) {
            $targetWidth = min((int) ($settings['width'] ?? $sourceWidth), $sourceWidth);
            $targetHeight = (int) round(($sourceHeight / $sourceWidth) * $targetWidth);

            if ($targetWidth < 1 || $targetHeight < 1) {
                continue;
            }

            $target = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $target,
                $source,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            $variantDirectory = $this->variantDirectory($media, (string) $variant);
            File::ensureDirectoryExists($variantDirectory);

            imagewebp(
                $target,
                $variantDirectory.'/'.$this->variantFilename($media),
                (int) ($settings['quality'] ?? 82)
            );

            imagedestroy($target);
        }

        imagedestroy($source);
    }

    private function createImageResource(string $path): ?\GdImage
    {
        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $image = @imagecreatefromstring($contents);

        return $image instanceof \GdImage ? $image : null;
    }

    private function deleteVariants(Media $media): void
    {
        $directory = trim(config('blog.media_directory', 'media/blog'), '/');

        File::deleteDirectory(public_path($directory.'/'.$media->getKey()));
    }

    private function variantDirectory(Media $media, string $variant): string
    {
        $directory = trim(config('blog.media_directory', 'media/blog'), '/');

        return public_path($directory.'/'.$media->getKey().'/'.$variant);
    }

    private function variantFilename(Media $media): string
    {
        return pathinfo((string) $media->filename, PATHINFO_FILENAME).'.webp';
    }
}
