<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AdvertisementRequest;
use App\Services\AdvertisementService;
use Illuminate\Http\JsonResponse;
use App\Services\AdvertisingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Advertisements",
 *     description="API Endpoints for managing advertisements"
 * )
 */
class AdvertisementController extends Controller
{
    protected $advertisementService;
    protected AdvertisingService $advertisingService;

    public function __construct(AdvertisementService $advertisementService, AdvertisingService $advertisingService)
    {
        $this->advertisementService = $advertisementService;
        $this->advertisingService = $advertisingService;
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/advertisements",
     *     summary="Get list of advertisements",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Advertisement")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'advertisements:' . md5(json_encode($request->all()));

        return CacheService::tags(['advertisements'], $cacheKey, function () use ($request) {
            $query = Advertisement::query()
                ->with(['user', 'category', 'favoritedBy', 'ratings'])
                ->when($request->filled('title'), function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->title . '%');
                })
                ->when($request->filled('description'), function ($query) use ($request) {
                    $query->where('description', 'like', '%' . $request->description . '%');
                })
                ->when($request->filled('min_price'), function ($query) use ($request) {
                    $query->where('price', '>=', $request->min_price);
                })
                ->when($request->filled('max_price'), function ($query) use ($request) {
                    $query->where('price', '<=', $request->max_price);
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                })
                ->when($request->filled('category_id'), function ($query) use ($request) {
                    $query->where('category_id', $request->category_id);
                })
                ->when($request->filled('user_id'), function ($query) use ($request) {
                    $query->where('user_id', $request->user_id);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('is_vip'), function ($query) use ($request) {
                    $query->where('is_vip', $request->boolean('is_vip'));
                });

            // Применяем сортировку
            $sortField = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            // Добавляем сортировку по популярности
            if ($sortField === 'popularity') {
                $query->orderBy('views_count', $sortDirection)
                      ->orderBy('created_at', 'desc');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }

            // Получаем курсор из запроса
            $cursor = $request->input('cursor');
            $perPage = $request->input('per_page', 10);

            if ($cursor) {
                // Декодируем курсор
                $cursorData = json_decode(base64_decode($cursor), true);
                $query->where(function ($q) use ($cursorData, $sortField, $sortDirection) {
                    if ($sortDirection === 'asc') {
                        $q->where($sortField, '>', $cursorData[$sortField])
                            ->orWhere(function ($q) use ($cursorData, $sortField) {
                                $q->where($sortField, '=', $cursorData[$sortField])
                                    ->where('id', '>', $cursorData['id']);
                            });
                    } else {
                        $q->where($sortField, '<', $cursorData[$sortField])
                            ->orWhere(function ($q) use ($cursorData, $sortField) {
                                $q->where($sortField, '=', $cursorData[$sortField])
                                    ->where('id', '<', $cursorData['id']);
                            });
                    }
                });
            }

            // Получаем результаты
            $advertisements = $query->limit($perPage + 1)->get();
            
            // Проверяем, есть ли следующая страница
            $hasMorePages = $advertisements->count() > $perPage;
            
            // Удаляем лишний элемент, если он есть
            if ($hasMorePages) {
                $advertisements->pop();
            }

            // Формируем курсор для следующей страницы
            $nextCursor = null;
            if ($hasMorePages && $advertisements->isNotEmpty()) {
                $lastItem = $advertisements->last();
                $nextCursor = base64_encode(json_encode([
                    'id' => $lastItem->id,
                    $sortField => $lastItem->$sortField
                ]));
            }

            return response()->json([
                'data' => $advertisements,
                'next_cursor' => $nextCursor,
                'has_more_pages' => $hasMorePages
            ]);
        }, 300); // Кэшируем на 5 минут
    }

    /**
     * @OA\Post(
     *     path="/api/advertisements",
     *     summary="Create new advertisement",
     *     tags={"Advertisements"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdvertisementRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Advertisement")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(AdvertisementRequest $request): JsonResponse
    {
        $advertisement = $this->advertisementService->create($request->validated());
        return response()->json($advertisement, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/advertisements/{id}",
     *     summary="Get advertisement by ID",
     *     tags={"Advertisements"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Advertisement")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     * )
     */
    public function show(Advertisement $advertisement): JsonResponse
    {
        $advertisement->increment('views_count');
        
        $cacheKey = CacheService::getModelCacheKey($advertisement, 'show');
        
        return CacheService::tags(['advertisements'], $cacheKey, function () use ($advertisement) {
            return $advertisement->load(['user', 'category', 'favoritedBy', 'ratings']);
        });
    }

    /**
     * @OA\Put(
     *     path="/api/advertisements/{id}",
     *     summary="Update advertisement",
     *     tags={"Advertisements"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdvertisementRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Advertisement")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(AdvertisementRequest $request, Advertisement $advertisement): JsonResponse
    {
        $advertisement = $this->advertisementService->update($advertisement, $request->validated());
        return response()->json($advertisement);
    }

    /**
     * @OA\Delete(
     *     path="/api/advertisements/{id}",
     *     summary="Delete advertisement",
     *     tags={"Advertisements"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     )
     * )
     */
    public function destroy(Advertisement $advertisement): JsonResponse
    {
        $this->advertisementService->delete($advertisement);
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $cacheKey = 'advertisements:search:' . md5($query);

        return CacheService::tags(['advertisements'], $cacheKey, function () use ($query) {
            $advertisements = Advertisement::query()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->select('id', 'title', 'price')
                ->limit(5)
                ->get();

            return response()->json([
                'data' => $advertisements
            ]);
        }, 300); // Кэшируем на 5 минут
    }

    public function indexUser()
    {
        $advertisements = Advertisement::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('advertisements.index', compact('advertisements'));
    }

    public function createUser()
    {
        $prices = [
            'banner' => [
                'min' => 999,
                'unit' => 'день'
            ],
            'category' => [
                'min' => 499,
                'unit' => 'день'
            ],
            'email' => [
                'min' => 1999,
                'unit' => 'рассылка'
            ]
        ];

        return view('advertisements.create', compact('prices'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'type' => 'required|in:banner,category,email',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'target_url' => 'nullable|url',
            'targeting' => 'nullable|array',
            'starts_at' => 'required|date|after:today',
            'ends_at' => 'required|date|after:starts_at',
            'budget' => 'required|numeric|min:0',
        ]);

        // Проверка минимального бюджета
        $minBudgets = [
            'banner' => 999,
            'category' => 499,
            'email' => 1999
        ];

        if ($request->budget < $minBudgets[$request->type]) {
            return back()
                ->withInput()
                ->withErrors(['budget' => "Минимальный бюджет для типа {$request->type} составляет {$minBudgets[$request->type]}₽"]);
        }

        try {
            $advertisement = $this->advertisingService->createAdvertisement([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'title' => $request->title,
                'content' => $request->content,
                'image_url' => $request->image_url,
                'target_url' => $request->target_url,
                'targeting' => $request->targeting,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'budget' => $request->budget,
            ]);

            return redirect()
                ->route('advertisements.show', $advertisement)
                ->with('success', 'Рекламное объявление успешно создано и отправлено на модерацию');
        } catch (\Exception $e) {
            Log::error('Failed to create advertisement: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Не удалось создать рекламное объявление');
        }
    }

    public function showUser(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->load('user');
        return view('advertisements.show', compact('advertisement'));
    }

    public function statistics()
    {
        $stats = [
            'total_ads' => Advertisement::where('user_id', Auth::id())->count(),
            'active_ads' => Advertisement::where('user_id', Auth::id())
                ->where('status', 'active')
                ->count(),
            'total_spent' => Advertisement::where('user_id', Auth::id())
                ->sum('spent'),
            'by_type' => Advertisement::where('user_id', Auth::id())
                ->selectRaw('type, count(*) as count, sum(spent) as total_spent')
                ->groupBy('type')
                ->get(),
            'recent_impressions' => Advertisement::where('user_id', Auth::id())
                ->where('status', 'active')
                ->sum('statistics->impressions'),
            'recent_clicks' => Advertisement::where('user_id', Auth::id())
                ->where('status', 'active')
                ->sum('statistics->clicks'),
        ];

        return view('advertisements.statistics', compact('stats'));
    }
}
