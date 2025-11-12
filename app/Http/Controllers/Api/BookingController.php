<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'guide.user']);

        // If user is a guide, show bookings for their guide profile
        if ($user->role === 'guide' && $user->guide) {
            $query->where('guide_id', $user->guide->id);
        } else {
            // If regular user, show their bookings
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('booking_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('booking_date', '<=', $request->end_date);
        }

        // Filter by upcoming bookings
        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('booking_date', '>=', now()->format('Y-m-d'));
        }

        $bookings = $query->orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guide_id' => 'required|exists:guides,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:([0-5][0-9]))?$/'],
            'end_time' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:([0-5][0-9]))?$/', 'after:start_time'],
            'total_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the guide
        $guide = Guide::with('user')->find($request->guide_id);

        if (!$guide->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Guide is not approved for bookings'
            ], 422);
        }

        // Check if user is trying to book their own guide profile
        if (Auth::id() === $guide->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot book your own guide services'
            ], 422);
        }

        // Normalize time format (remove seconds if present)
        $startTimeStr = strlen($request->start_time) > 5 
            ? substr($request->start_time, 0, 5) 
            : $request->start_time;
        $endTimeStr = strlen($request->end_time) > 5 
            ? substr($request->end_time, 0, 5) 
            : $request->end_time;
        
        // Validate booking time (must be during business hours: 9 AM - 6 PM)
        $startHour = Carbon::createFromFormat('H:i', $startTimeStr)->hour;
        $endHour = Carbon::createFromFormat('H:i', $endTimeStr)->hour;

        if ($startHour < 9 || $endHour > 18 || $startHour >= $endHour) {
            return response()->json([
                'success' => false,
                'message' => 'Booking time must be between 9:00 AM and 6:00 PM'
            ], 422);
        }

        // Check for conflicting bookings
        $conflictingBooking = Booking::where('guide_id', $request->guide_id)
            ->where('booking_date', $request->booking_date)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($startTimeStr, $endTimeStr) {
                $query->whereBetween('start_time', [$startTimeStr, $endTimeStr])
                      ->orWhereBetween('end_time', [$startTimeStr, $endTimeStr])
                      ->orWhere(function($q) use ($startTimeStr, $endTimeStr) {
                          $q->where('start_time', '<=', $startTimeStr)
                            ->where('end_time', '>=', $endTimeStr);
                      });
            })
            ->first();

        if ($conflictingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Guide is not available during the selected time slot'
            ], 409);
        }

        // Calculate total hours and price
        $startTime = Carbon::createFromFormat('H:i', $startTimeStr);
        $endTime = Carbon::createFromFormat('H:i', $endTimeStr);
        $duration = $endTime->diffInHours($startTime);
        
        // Use provided total_price if available, otherwise calculate from hourly_rate
        $totalPrice = $request->has('total_price') && $request->total_price > 0 
            ? $request->total_price 
            : ($duration * $guide->hourly_rate);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'guide_id' => $request->guide_id,
            'booking_date' => $request->booking_date,
            'start_time' => $startTimeStr . ':00', // Store as H:i:s format
            'end_time' => $endTimeStr . ':00', // Store as H:i:s format
            'total_price' => $totalPrice,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        $booking->load(['user', 'guide.user']);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => [
                'booking' => $booking,
                'duration_hours' => $duration,
                'hourly_rate' => $guide->hourly_rate
            ]
        ], 201);
    }

    /**
     * Display the specified booking.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['user', 'guide.user'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $user = Auth::user();

        // Check if user has access to this booking
        if ($booking->user_id !== $user->id && 
            (!$user->guide || $booking->guide_id !== $user->guide->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this booking'
            ], 403);
        }

        // Calculate additional booking info
        $startTime = Carbon::createFromFormat('H:i:s', $booking->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $booking->end_time);
        $duration = $endTime->diffInHours($startTime);

        $bookingData = $booking->toArray();
        $bookingData['duration_hours'] = $duration;
        $bookingData['can_cancel'] = $this->canCancelBooking($booking);
        $bookingData['can_confirm'] = $this->canConfirmBooking($booking, $user);

        return response()->json([
            'success' => true,
            'data' => $bookingData
        ]);
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $user = Auth::user();

        // Check permissions
        $canEdit = false;
        if ($booking->user_id === $user->id && in_array($booking->status, ['pending'])) {
            $canEdit = true; // Customer can edit pending bookings
        } elseif ($user->guide && $booking->guide_id === $user->guide->id) {
            $canEdit = true; // Guide can update status
        }

        if (!$canEdit) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this booking'
            ], 403);
        }

        // Validation rules differ based on user role
        if ($booking->user_id === $user->id) {
            // Customer updating booking details
            $validator = Validator::make($request->all(), [
                'booking_date' => 'date|after_or_equal:today',
                'start_time' => 'date_format:H:i',
                'end_time' => 'date_format:H:i|after:start_time',
                'notes' => 'nullable|string|max:500'
            ]);
        } else {
            // Guide updating booking status
            $validator = Validator::make($request->all(), [
                'status' => 'in:confirmed,cancelled,completed'
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle different update scenarios
        if ($booking->user_id === $user->id && $booking->status === 'pending') {
            // Customer updating booking details
            $updateData = $request->only(['booking_date', 'start_time', 'end_time', 'notes']);
            
            if ($request->has(['start_time', 'end_time'])) {
                // Recalculate price if time changed
                $guide = $booking->guide;
                $startTime = Carbon::createFromFormat('H:i', $request->start_time);
                $endTime = Carbon::createFromFormat('H:i', $request->end_time);
                $duration = $endTime->diffInHours($startTime);
                $updateData['total_price'] = $duration * $guide->hourly_rate;
            }

            $booking->update($updateData);
        } elseif ($user->guide && $booking->guide_id === $user->guide->id) {
            // Guide updating status
            $booking->update(['status' => $request->status]);
        }

        $booking->load(['user', 'guide.user']);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking
        ]);
    }

    /**
     * Remove the specified booking (cancel).
     */
    public function destroy(string $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $user = Auth::user();

        // Check if user can cancel this booking
        if ($booking->user_id !== $user->id && 
            (!$user->guide || $booking->guide_id !== $user->guide->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to cancel this booking'
            ], 403);
        }

        if (!$this->canCancelBooking($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled at this time'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }

    /**
     * Confirm a booking (guide only).
     */
    public function confirm(string $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $user = Auth::user();

        if (!$this->canConfirmBooking($booking, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to confirm this booking or booking cannot be confirmed'
            ], 403);
        }

        $booking->update(['status' => 'confirmed']);
        $booking->load(['user', 'guide.user']);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed successfully',
            'data' => $booking
        ]);
    }

    /**
     * Get booking statistics for current user.
     */
    public function stats()
    {
        $user = Auth::user();
        
        if ($user->role === 'guide' && $user->guide) {
            // Guide statistics
            $bookings = Booking::where('guide_id', $user->guide->id);
            
            $stats = [
                'total_bookings' => $bookings->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                'completed_bookings' => $bookings->where('status', 'completed')->count(),
                'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                'total_earnings' => $bookings->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
                'upcoming_bookings' => $bookings->where('booking_date', '>=', now()->format('Y-m-d'))->count()
            ];
        } else {
            // Customer statistics
            $bookings = Booking::where('user_id', $user->id);
            
            $stats = [
                'total_bookings' => $bookings->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                'completed_bookings' => $bookings->where('status', 'completed')->count(),
                'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                'total_spent' => $bookings->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
                'upcoming_bookings' => $bookings->where('booking_date', '>=', now()->format('Y-m-d'))->count()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Check if booking can be cancelled.
     */
    private function canCancelBooking($booking)
    {
        // Can cancel if status is pending or confirmed and booking is at least 24 hours away
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return false;
        }

        $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $booking->booking_date . ' ' . $booking->start_time);
        
        return $bookingDateTime->greaterThan(now()->addHours(24));
    }

    /**
     * Check if booking can be confirmed.
     */
    private function canConfirmBooking($booking, $user)
    {
        return $user->guide && 
               $booking->guide_id === $user->guide->id && 
               $booking->status === 'pending';
    }
}
