<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideBookingController extends Controller
{
    /**
     * Display all bookings for the authenticated guide
     */
    public function index(Request $request)
    {
        // Get authenticated guide
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        if (!$guide) {
            return redirect()->route('guide.login')->with('error', 'Guide profile not found.');
        }
        
        // Get bookings query
        $query = Booking::where('guide_id', $guide->id)->with('user');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date if provided
        if ($request->has('date_filter') && $request->date_filter != '') {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('booking_date', today());
                    break;
                case 'week':
                    $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('booking_date', now()->month)
                          ->whereYear('booking_date', now()->year);
                    break;
            }
        }
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Get bookings with pagination
        $bookings = $query->orderBy('booking_date', 'desc')->paginate(10);
        
        // Calculate statistics
        $totalBookings = Booking::where('guide_id', $guide->id)->count();
        $pendingBookings = Booking::where('guide_id', $guide->id)->where('status', 'pending')->count();
        $confirmedBookings = Booking::where('guide_id', $guide->id)->where('status', 'confirmed')->count();
        $completedBookings = Booking::where('guide_id', $guide->id)->where('status', 'completed')->count();
        
        return view('guide.bookings', compact(
            'bookings',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings'
        ));
    }
    
    /**
     * View booking details
     */
    public function view($id)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        $booking = Booking::where('guide_id', $guide->id)
            ->with('user')
            ->findOrFail($id);
        
        return view('guide.booking-details', compact('booking'));
    }
    
    /**
     * Confirm a booking
     */
    public function confirm($id)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        $booking = Booking::where('guide_id', $guide->id)
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $booking->update(['status' => 'confirmed']);
        $booking->load(['user', 'trip']);
        
        // Send notification to user when booking is confirmed
        if ($booking->trip) {
            try {
                $notificationService = app(\App\Services\FirebaseNotificationService::class);
                $notificationService->notifyTripAccepted($booking->user, $booking->trip);
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send notification for trip acceptance: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('guide.bookings')
            ->with('success', 'Booking confirmed successfully!');
    }
    
    /**
     * Mark booking as completed
     */
    public function complete($id)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        $booking = Booking::where('guide_id', $guide->id)
            ->where('status', 'confirmed')
            ->findOrFail($id);
        
        $booking->update(['status' => 'completed']);
        
        return redirect()->route('guide.bookings')
            ->with('success', 'Booking marked as completed!');
    }
    
    /**
     * Cancel a booking
     */
    public function cancel(Request $request, $id)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        $booking = Booking::where('guide_id', $guide->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);
        
        $booking->update(['status' => 'cancelled']);
        
        return redirect()->route('guide.bookings')
            ->with('success', 'Booking cancelled successfully!');
    }
}