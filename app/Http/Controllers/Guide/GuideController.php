<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideController extends Controller
{
    /**
     * Show guide dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $guide = $user->guide;

        // Get guide statistics
        $stats = [
            'total_bookings' => Booking::where('guide_id', $guide->id)->count(),
            'pending_bookings' => Booking::where('guide_id', $guide->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('guide_id', $guide->id)->where('status', 'confirmed')->count(),
            'completed_bookings' => Booking::where('guide_id', $guide->id)->where('status', 'completed')->count(),
            'total_reviews' => Review::where('guide_id', $guide->id)->count(),
            'average_rating' => Review::where('guide_id', $guide->id)->avg('rating') ?? 0,
        ];

        // Get upcoming bookings
        $upcomingBookings = Booking::where('guide_id', $guide->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now())
            ->orderBy('booking_date', 'asc')
            ->limit(5)
            ->get();

        // Get recent reviews
        $recentReviews = Review::where('guide_id', $guide->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('guide.dashboard', compact('guide', 'stats', 'upcomingBookings', 'recentReviews'));
    }

    /**
     * Show all guide bookings
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $guide = $user->guide;

        $query = Booking::where('guide_id', $guide->id);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(10);

        return view('guide.bookings', compact('bookings'));
    }

    /**
     * Show guide reviews
     */
    public function reviews()
    {
        $user = Auth::user();
        $guide = $user->guide;

        $reviews = Review::where('guide_id', $guide->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = Review::where('guide_id', $guide->id)->avg('rating') ?? 0;

        return view('guide.reviews', compact('reviews', 'averageRating'));
    }

    /**
     * Show guide profile
     */
    public function profile()
    {
        $user = Auth::user();
        $guide = $user->guide;

        return view('guide.profile', compact('guide'));
    }

    /**
     * Update guide profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $guide = $user->guide;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'languages' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update guide
        $guide->update([
            'bio' => $validated['bio'] ?? $guide->bio,
            'phone' => $validated['phone'] ?? $guide->phone,
            'languages' => $validated['languages'] ?? $guide->languages,
            'hourly_rate' => $validated['hourly_rate'] ?? $guide->hourly_rate,
        ]);

        return redirect()->route('guide.profile')->with('success', 'Profile updated successfully!');
    }
}