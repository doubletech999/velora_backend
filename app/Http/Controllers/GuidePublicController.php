<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Review;
use Illuminate\Http\Request;

class GuidePublicController extends Controller
{
    /**
     * Display the public guide profile
     */
    public function show($id)
    {
        // Get guide with user relationship
        $guide = Guide::with('user')->findOrFail($id);
        
        // Get statistics
        $guide->tours_count = 0; // Update this when you have tours
        $guide->reviews_count = Review::count(); // Update to filter by guide
        
        // Get recent reviews (limit to 5)
        $reviews = Review::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('guide.public-profile', compact('guide', 'reviews'));
    }
}