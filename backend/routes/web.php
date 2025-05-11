<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PremiumFeatureController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdvertisementController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BannerSettingsController;

Route::get('/', function () {
    return view('welcome');
});

// Пользовательские маршруты для рекламы
Route::middleware(['auth'])->group(function () {
    Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
    Route::get('/advertisements/create', [AdvertisementController::class, 'create'])->name('advertisements.create');
    Route::post('/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    Route::get('/advertisements/{advertisement}', [AdvertisementController::class, 'show'])->name('advertisements.show');
    Route::get('/advertisements/statistics', [AdvertisementController::class, 'statistics'])->name('advertisements.statistics');
});

// Жалобы
Route::middleware(['auth'])->group(function () {
    Route::post('/advertisements/{advertisement}/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
});

// Админские маршруты
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Платежи
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/statistics', [PaymentController::class, 'statistics'])->name('payments.statistics');
    Route::get('/payments/{transaction}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{transaction}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/{transaction}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

    // Модерация
    Route::get('/moderations', [ModerationController::class, 'index'])->name('moderations.index');
    Route::get('/moderations/statistics', [ModerationController::class, 'statistics'])->name('moderations.statistics');
    Route::get('/moderations/{moderation}', [ModerationController::class, 'show'])->name('moderations.show');
    Route::post('/moderations/{moderation}/approve', [ModerationController::class, 'approve'])->name('moderations.approve');
    Route::post('/moderations/{moderation}/reject', [ModerationController::class, 'reject'])->name('moderations.reject');

    // Premium Features Routes
    Route::get('/premium-features', [PremiumFeatureController::class, 'index'])->name('premium-features.index');
    Route::get('/premium-features/statistics', [PremiumFeatureController::class, 'statistics'])->name('premium-features.statistics');
    Route::get('/premium-features/{feature}', [PremiumFeatureController::class, 'show'])->name('premium-features.show');
    Route::post('/advertisements/{advertisement}/premium-features', [PremiumFeatureController::class, 'activate'])->name('premium-features.activate');
    Route::post('/premium-features/{feature}/deactivate', [PremiumFeatureController::class, 'deactivate'])->name('premium-features.deactivate');

    // Advertisement Routes
    Route::get('/advertisements', [AdminAdvertisementController::class, 'index'])->name('advertisements.index');
    Route::get('/advertisements/create', [AdminAdvertisementController::class, 'create'])->name('advertisements.create');
    Route::post('/advertisements', [AdminAdvertisementController::class, 'store'])->name('advertisements.store');
    Route::get('/advertisements/{advertisement}', [AdminAdvertisementController::class, 'show'])->name('advertisements.show');
    Route::post('/advertisements/{advertisement}/approve', [AdminAdvertisementController::class, 'approve'])->name('advertisements.approve');
    Route::post('/advertisements/{advertisement}/reject', [AdminAdvertisementController::class, 'reject'])->name('advertisements.reject');
    Route::get('/advertisements/statistics', [AdminAdvertisementController::class, 'statistics'])->name('advertisements.statistics');

    // Жалобы
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('admin.complaints.show');
    Route::post('/complaints/{complaint}/resolve', [ComplaintController::class, 'resolve'])->name('admin.complaints.resolve');

    // Banner settings routes
    Route::get('/banners/{banner}/settings', [BannerSettingsController::class, 'edit'])->name('banners.settings.edit');
    Route::put('/banners/{banner}/settings', [BannerSettingsController::class, 'update'])->name('banners.settings.update');
});

// Чат поддержки
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    Route::get('/chat/{room}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{room}/messages', [ChatController::class, 'store'])->name('chat.messages.store');
    Route::post('/chat/{room}/close', [ChatController::class, 'close'])->name('chat.close');
    Route::post('/chat/{room}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
});

// Banner routes
Route::middleware(['auth'])->group(function () {
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/advertisements/{advertisement}/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/advertisements/{advertisement}/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}', [BannerController::class, 'show'])->name('banners.show');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
});

// Banner tracking routes
Route::get('/banners/{banner}/impression', [BannerController::class, 'trackImpression'])->name('banners.impression');
Route::get('/banners/{banner}/click', [BannerController::class, 'trackClick'])->name('banners.click');
