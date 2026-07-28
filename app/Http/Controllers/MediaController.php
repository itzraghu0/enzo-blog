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

    public function index(Request $request): View
    {
        return view('admin.blog.media.index', [
            'mediaItems' => $this->mediaService->paginate(),
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse|RedirectResponse
    {
        $media = $this->mediaService->store($request->file('file'), $request->user(), $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Media uploaded successfully'),
                'data' => $media->load('creator'),
            ], 201);
        }

        return back()->with('success', __('Media uploaded successfully'));
    }

    public function show(Media $media): JsonResponse
    {
        return response()->json([
            'data' => $media->load('creator', 'mediable'),
        ]);
    }

    public function destroy(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        $this->mediaService->delete($media);

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', __('Media deleted successfully'));
    }
}
