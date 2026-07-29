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

        return Media::create([
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
            'collection' => $this->blankToNull($data['collection'] ?? 'default') ?? 'default',
            'locale' => $this->blankToNull($data['locale'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
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
            'collection' => $collection,
            'locale' => $locale ?? $media->locale,
            'sort_order' => $media->sort_order,
        ], $overrides));

        return $this->attach($copy, $model, $collection, $locale);
    }

    public function delete(Media $media): bool
    {
        File::delete(public_path($media->path));

        return (bool) $media->delete();
    }

    public function paginate(int $perPage = 24): LengthAwarePaginator
    {
        return Media::query()
            ->with(['creator', 'mediable'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
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
}
