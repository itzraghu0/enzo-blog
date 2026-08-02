<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $mediaService)
    {
    }

    public function index(Request $request): View|JsonResponse
    {
        $perPage = max(1, min((int) $request->integer('per_page', 50), 60));

        $mediaItems = $this->mediaService->paginate(
            $perPage,
            [
                'search' => $request->get('search'),
                'type' => $request->get('type', 'image'),
            ],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $mediaItems->getCollection()->map(fn (Media $media): array => $this->serializeMedia($media))->values(),
                'meta' => [
                    'current_page' => $mediaItems->currentPage(),
                    'last_page' => $mediaItems->lastPage(),
                    'per_page' => $mediaItems->perPage(),
                    'total' => $mediaItems->total(),
                    'next_page_url' => $mediaItems->nextPageUrl(),
                ],
            ]);
        }

        return view('admin.blog.media.index', [
            'mediaItems' => $mediaItems,
            'stats' => [
                'total' => \App\Models\Media::query()->count(),
                'thisMonth' => \App\Models\Media::query()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
                'publicFolder' => 'public/media/blog',
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.media.create', [
            'media' => new Media(),
            'formAction' => route('admin.media.store'),
            'submitLabel' => __('Upload media'),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse|RedirectResponse
    {
        $media = $this->mediaService->store($request->file('file'), $request->user(), $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Media uploaded successfully'),
                'data' => $this->serializeMedia($media->load('creator')),
            ], 201);
        }

        return redirect()
            ->route('admin.media.edit', $media)
            ->with('success', __('Media uploaded successfully'));
    }

    public function show(Media $media): JsonResponse
    {
        return response()->json([
            'data' => $this->serializeMedia($media->load('creator', 'mediable')),
        ]);
    }

    public function edit(Media $media): View
    {
        return view('admin.blog.media.edit', [
            'media' => $media->load('creator', 'mediable'),
            'formAction' => route('admin.media.update', $media),
            'formMethod' => 'PUT',
            'submitLabel' => __('Update media'),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
        ]);
    }

    public function update(StoreMediaRequest $request, Media $media): JsonResponse|RedirectResponse
    {
        if ($request->hasFile('file')) {
            $media = $this->mediaService->replaceFile($media, $request->file('file'), $request->user(), $request->validated());
        } else {
            $media = $this->mediaService->update($media, $request->validated());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Media updated successfully'),
                'data' => $this->serializeMedia($media->load('creator')),
            ]);
        }

        return redirect()
            ->route('admin.media.edit', $media)
            ->with('success', __('Media updated successfully'));
    }

    public function destroy(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        $this->mediaService->delete($media);

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', __('Media deleted successfully'));
    }

    private function serializeMedia(Media $media): array
    {
        return [
            'id' => $media->id,
            'url' => $media->url(),
            'thumbnail_url' => $media->variantUrl('sm') ?? $media->url(),
            'variants' => $media->variantUrls(),
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
            'collection' => $media->collection,
            'locale' => $media->locale,
            'created_at' => optional($media->created_at)->toDateTimeString(),
        ];
    }
}
