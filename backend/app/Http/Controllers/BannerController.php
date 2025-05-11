<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    protected $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        $banners = Banner::with('advertisement')
            ->latest()
            ->paginate(10);

        return view('banners.index', compact('banners'));
    }

    public function create(Advertisement $advertisement)
    {
        return view('banners.create', compact('advertisement'));
    }

    public function store(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'alt_text' => 'nullable|string|max:255',
            'target_url' => 'required|url',
            'position' => 'required|in:top,bottom,sidebar'
        ]);

        try {
            $imagePath = $request->file('image')->store('banners', 'public');
            
            $banner = $this->bannerService->createBanner($advertisement, [
                'image_path' => $imagePath,
                'width' => $request->width,
                'height' => $request->height,
                'alt_text' => $request->alt_text,
                'target_url' => $request->target_url,
                'position' => $request->position
            ]);

            return redirect()
                ->route('banners.show', $banner)
                ->with('success', 'Баннер успешно создан');
        } catch (\Exception $e) {
            Log::error('Failed to create banner: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Не удалось создать баннер. Пожалуйста, попробуйте снова.');
        }
    }

    public function show(Banner $banner)
    {
        $statistics = $this->bannerService->getBannerStatistics($banner);
        return view('banners.show', compact('banner', 'statistics'));
    }

    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'alt_text' => 'nullable|string|max:255',
            'target_url' => 'required|url',
            'position' => 'required|in:top,bottom,sidebar',
            'is_active' => 'boolean'
        ]);

        try {
            $data = $request->except('image');
            
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('banners', 'public');
            }

            $this->bannerService->updateBanner($banner, $data);

            return redirect()
                ->route('banners.show', $banner)
                ->with('success', 'Баннер успешно обновлен');
        } catch (\Exception $e) {
            Log::error('Failed to update banner: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Не удалось обновить баннер. Пожалуйста, попробуйте снова.');
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            $this->bannerService->deleteBanner($banner);
            return redirect()
                ->route('advertisements.show', $banner->advertisement)
                ->with('success', 'Баннер успешно удален');
        } catch (\Exception $e) {
            Log::error('Failed to delete banner: ' . $e->getMessage());
            return back()->with('error', 'Не удалось удалить баннер. Пожалуйста, попробуйте снова.');
        }
    }

    public function trackImpression(Banner $banner)
    {
        $this->bannerService->trackImpression($banner);
        return response()->json(['success' => true]);
    }

    public function trackClick(Banner $banner)
    {
        $this->bannerService->trackClick($banner);
        return redirect($banner->target_url);
    }
} 