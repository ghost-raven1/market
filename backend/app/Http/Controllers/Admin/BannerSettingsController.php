<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerSettingsController extends Controller
{
    public function edit(Banner $banner)
    {
        return view('admin.banners.settings', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'device_settings' => 'nullable|array',
            'schedule_settings' => 'nullable|array',
            'daily_impression_limit' => 'nullable|integer|min:1',
            'browser_settings' => 'nullable|array',
            'is_adaptive' => 'boolean',
            'adaptive_settings' => 'nullable|array'
        ]);

        try {
            $banner->update($request->only([
                'device_settings',
                'schedule_settings',
                'daily_impression_limit',
                'browser_settings',
                'is_adaptive',
                'adaptive_settings'
            ]));

            return redirect()
                ->route('admin.banners.show', $banner)
                ->with('success', 'Настройки баннера успешно обновлены');
        } catch (\Exception $e) {
            Log::error('Failed to update banner settings: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Не удалось обновить настройки баннера');
        }
    }
} 