<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Site;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'site', 'guide']);

        // Filter by type (site or guide reviews)
        if ($request->has('type')) {
            if ($request->type === 'site') {
                $query->whereNotNull('site_id');
            } elseif ($request->type === 'guide') {
                $query->whereNotNull('guide_id');
            }
        }

        // Filter by specific site
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Filter by specific guide
        if ($request->has('guide_id')) {
            $query->where('guide_id', $request->guide_id);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Get current user's reviews only
        if ($request->has('my_reviews') && $request->my_reviews) {
            $query->where('user_id', Auth::id());
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'nullable|exists:sites,id',
            'guide_id' => 'nullable|exists:guides,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Must review either a site or a guide, but not both
        if ((!$request->site_id && !$request->guide_id) || 
            ($request->site_id && $request->guide_id)) {
            return response()->json([
                'success' => false,
                'message' => 'You must review either a site or a guide, not both'
            ], 422);
        }

        // Check if user already reviewed this site/guide
        $existingReview = Review::where('user_id', Auth::id())
            ->where(function($query) use ($request) {
                if ($request->site_id) {
                    $query->where('site_id', $request->site_id);
                } else {
                    $query->where('guide_id', $request->guide_id);
                }
            })
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this ' . ($request->site_id ? 'site' : 'guide')
            ], 409);
        }

        // Verify that the site/guide exists and is valid for review
        if ($request->site_id) {
            $site = Site::find($request->site_id);
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found'
                ], 404);
            }
        }

        if ($request->guide_id) {
            $guide = Guide::where('id', $request->guide_id)
                          ->where('is_approved', true)
                          ->first();
            if (!$guide) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guide not found or not approved'
                ], 404);
            }
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'site_id' => $request->site_id,
            'guide_id' => $request->guide_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        $review->load(['user', 'site', 'guide']);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show(string $id)
    {
        $review = Review::with(['user', 'site', 'guide'])->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if the authenticated user owns this review
        if (Auth::id() !== $review->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this review'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only(['rating', 'comment']));
        $review->load(['user', 'site', 'guide']);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if the authenticated user owns this review
        if (Auth::id() !== $review->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this review'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get reviews statistics for a specific site or guide.
     */
    public function stats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'nullable|exists:sites,id',
            'guide_id' => 'nullable|exists:guides,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if ((!$request->site_id && !$request->guide_id) || 
            ($request->site_id && $request->guide_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide either site_id or guide_id, not both'
            ], 422);
        }

        $query = Review::query();

        if ($request->site_id) {
            $query->where('site_id', $request->site_id);
            $type = 'site';
            $entity_id = $request->site_id;
        } else {
            $query->where('guide_id', $request->guide_id);
            $type = 'guide';
            $entity_id = $request->guide_id;
        }

        $reviews = $query->get();
        $totalReviews = $reviews->count();

        if ($totalReviews === 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'entity_id' => $entity_id,
                    'total_reviews' => 0,
                    'average_rating' => 0,
                    'rating_distribution' => [
                        '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0
                    ]
                ]
            ]);
        }

        $averageRating = $reviews->avg('rating');
        $ratingDistribution = [
            '5' => $reviews->where('rating', 5)->count(),
            '4' => $reviews->where('rating', 4)->count(),
            '3' => $reviews->where('rating', 3)->count(),
            '2' => $reviews->where('rating', 2)->count(),
            '1' => $reviews->where('rating', 1)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'entity_id' => $entity_id,
                'total_reviews' => $totalReviews,
                'average_rating' => round($averageRating, 1),
                'rating_distribution' => $ratingDistribution,
                'recent_reviews' => $reviews->sortByDesc('created_at')->take(5)->values()
            ]
        ]);
    }

    /**
     * Get current user's reviews.
     */
    public function myReviews()
    {
        $reviews = Review::with(['site', 'guide'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Check if user can review a specific site or guide.
     */
    public function canReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'nullable|exists:sites,id',
            'guide_id' => 'nullable|exists:guides,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if ((!$request->site_id && !$request->guide_id) || 
            ($request->site_id && $request->guide_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Provide either site_id or guide_id, not both'
            ], 422);
        }

        // Check if user already reviewed this site/guide
        $existingReview = Review::where('user_id', Auth::id())
            ->where(function($query) use ($request) {
                if ($request->site_id) {
                    $query->where('site_id', $request->site_id);
                } else {
                    $query->where('guide_id', $request->guide_id);
                }
            })
            ->first();

        $canReview = !$existingReview;
        $reason = $existingReview ? 'Already reviewed' : null;

        return response()->json([
            'success' => true,
            'data' => [
                'can_review' => $canReview,
                'reason' => $reason,
                'existing_review' => $existingReview
            ]
        ]);
    }
}