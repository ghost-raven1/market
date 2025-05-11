<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite advertisements.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for($request->user()->favorites())
            ->allowedFilters([
                'title',
                'description',
                'price',
                'category_id',
                'status',
                'is_vip',
                AllowedFilter::exact('price'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('is_vip'),
            ])
            ->allowedSorts(['price', 'created_at', 'updated_at'])
            ->with(['user', 'category', 'ratings']);

        return $query->paginate(10);
    }

    /**
     * Add an advertisement to user's favorites.
     */
    public function store(Request $request, Advertisement $advertisement)
    {
        $request->user()->favorites()->attach($advertisement->id);

        return response()->json([
            'message' => 'Advertisement added to favorites',
            'advertisement' => $advertisement->load(['user', 'category', 'ratings'])
        ], 201);
    }

    /**
     * Remove an advertisement from user's favorites.
     */
    public function destroy(Request $request, Advertisement $advertisement)
    {
        $request->user()->favorites()->detach($advertisement->id);

        return response()->json([
            'message' => 'Advertisement removed from favorites'
        ], 200);
    }

    /**
     * Check if an advertisement is in user's favorites.
     */
    public function check(Request $request, Advertisement $advertisement)
    {
        $isFavorite = $request->user()->favorites()->where('advertisement_id', $advertisement->id)->exists();

        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }
}
