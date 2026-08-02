<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertSiteSettingsRequest;
use App\Services\SiteSettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SiteSettingController extends Controller
{
    public function __construct(private readonly SiteSettingService $siteSettingService)
    {
    }

    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => $this->siteSettingService->all(),
        ]);
    }

    public function update(UpsertSiteSettingsRequest $request): RedirectResponse
    {
        $this->siteSettingService->upsertMany($request->validated('settings', []));

        return back()->with('success', __('Settings updated successfully'));
    }
}
