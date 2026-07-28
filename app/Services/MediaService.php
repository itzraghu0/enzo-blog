<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function store(UploadedFile $file, ?User $user = null, array $data = []): Media
    {
        $disk = $data['disk'] ?? config('blog.media_disk', 'public');
        $directory = trim(config('blog.media_directory', 'blog'), '/');
        $subDirectory = now()->format('Y/m');
        $storedPath = $file->store($directory.'/'.$subDirectory, $disk);

        return Media::create([
            'user_id' => $user?->id,
            'disk' => $disk,
            'path' => $storedPath,
            'filename' => basename($storedPath),
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

    public function delete(Media $media): bool
    {
        Storage::disk($media->disk)->delete($media->path);

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
}
