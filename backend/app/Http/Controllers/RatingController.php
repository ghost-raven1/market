<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class RatingController extends Controller
{
    /**
     * Display a listing of ratings for a user.
     */
    public function index(Request $request, User $user)
    {
        $query = QueryBuilder::for($user->ratings())
            ->allowedFilters([
                'rating',
                'comment',
                'rater_id',
                AllowedFilter::exact('rating'),
                AllowedFilter::exact('rater_id'),
            ])
            ->allowedSorts(['rating', 'created_at', 'updated_at'])
            ->with(['rater', 'rated']);

        return $query->paginate(10);
    }

    /**
     * Store a newly created rating.
     */
    public function store(Request $request, User $user)
    {
        // Prevent self-rating
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot rate yourself'
            ], 403);
        }

        // Check if user has already rated
        $existingRating = Rating::where('rater_id', $request->user()->id)
            ->where('rated_id', $user->id)
            ->first();

        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this user'
            ], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating = Rating::create([
            'rater_id' => $request->user()->id,
            'rated_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'Rating created successfully',
            'data' => $rating->load(['rater', 'rated'])
        ], 201);
    }

    /**
     * Update the specified rating.
     */
    public function update(Request $request, Rating $rating)
    {
        $this->authorize('update', $rating);

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating->update($validated);

        return response()->json([
            'message' => 'Rating updated successfully',
            'data' => $rating->load(['rater', 'rated'])
        ]);
    }

    /**
     * Remove the specified rating.
     */
    public function destroy(Rating $rating)
    {
        $this->authorize('delete', $rating);
        
        $rating->delete();

        return response()->json(null, 204);
    }

    /**
     * Get average rating for a user.
     */
    public function average(User $user)
    {
        $averageRating = $user->ratings()->avg('rating');
        $totalRatings = $user->ratings()->count();

        return response()->json([
            'average_rating' => round($averageRating, 2),
            'total_ratings' => $totalRatings
        ]);
    }
}
