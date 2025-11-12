<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuideReviewController extends Controller
{
    /**
     * Display all reviews for the authenticated guide
     */
    public function index(Request $request)
    {
        // Get authenticated guide
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        if (!$guide) {
            return redirect()->route('guide.login')->with('error', 'Guide profile not found.');
        }
        
        // Get reviews for sites (not tours) - based on your database structure
        $query = Review::with(['user', 'site']);
        
        // Filter by rating if provided
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }
        
        // Sort reviews
        $sortBy = $request->input('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Get reviews with pagination
        $reviews = $query->paginate(10);
        
        // Calculate statistics
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating') ?? 0;
        $fiveStarReviews = Review::where('rating', 5)->count();
        $monthReviews = Review::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Rating distribution
        $ratingCounts = [];
        $ratingDistribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('rating', $i)->count();
            $ratingCounts[$i] = $count;
            $ratingDistribution[$i] = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        }
        
        return view('guide.reviews', compact(
            'reviews',
            'totalReviews',
            'averageRating',
            'fiveStarReviews',
            'monthReviews',
            'ratingCounts',
            'ratingDistribution'
        ));
    }
    
    /**
     * Respond to a review
     */
    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:1000'
        ]);
        
        $review = Review::findOrFail($id);
        
        // Check if response column exists, if not use a workaround
        try {
            $review->update([
                'response' => $request->response,
                'response_date' => now()
            ]);
        } catch (\Exception $e) {
            // If columns don't exist, just show success message
            // You'll need to add these columns later
            return redirect()->route('guide.reviews')
                ->with('error', 'Response feature not available. Please contact admin.');
        }
        
        return redirect()->route('guide.reviews')
            ->with('success', 'Response submitted successfully!');
    }
}