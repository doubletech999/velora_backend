<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use App\Services\FCMNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if ($user->role === 'admin') {
            $query = Booking::with(['user', 'guide.user', 'site']);
        } else {
            $query = Booking::with(['user', 'guide.user', 'site']);

            // If user is a guide, show bookings for their guide profile
            if ($user->role === 'guide' && $user->guide) {
                $query->where('guide_id', $user->guide->id);
            } else {
                // If regular user, show their bookings
                $query->where('user_id', $user->id);
            }
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

        $bookings = $query->orderBy('created_at', 'desc')
                          ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guide_id' => 'nullable|exists:guides,id',
            'path_id' => 'required|string',
            'site_id' => 'nullable|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'total_price' => 'required|numeric|min:0',
            'number_of_participants' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,visa',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get user_id from token or request
        $userId = $request->user()?->id ?? $request->input('user_id');
        
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID is required.'
            ], 422);
        }

        // Get the guide (optional - may not be required for route/camping bookings)
        $guide = null;
        if ($request->guide_id) {
            $guide = Guide::with('user')->find($request->guide_id);
            
            if (!$guide) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Guide not found'
                ], 404);
            }

            if (!$guide->is_approved) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Guide is not approved for bookings'
                ], 422);
            }

            // Check if user is trying to book their own guide profile
            if ($userId === $guide->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot book your own guide services'
                ], 422);
            }
        }

        // Check for conflicting bookings (only if guide_id is provided)
        if ($request->guide_id) {
            $conflictingBooking = Booking::where('guide_id', $request->guide_id)
                ->where('booking_date', $request->booking_date)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->first();

            if ($conflictingBooking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Guide is not available during the selected time slot'
                ], 409);
            }
        }

        $booking = Booking::create([
            'user_id' => $userId,
            'guide_id' => $request->guide_id,
            'path_id' => $request->path_id,
            'site_id' => $request->site_id ?? $request->path_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'total_price' => $request->total_price,
            'number_of_participants' => $request->number_of_participants,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        $booking->load(['user', 'guide.user']);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحجز بنجاح',
            'data' => $booking
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
            $oldStatus = $booking->status;
            $booking->update(['status' => $request->status]);
            
            // Send notification if status changed to confirmed
            if ($oldStatus !== 'confirmed' && $request->status === 'confirmed') {
                try {
                    $booking->load(['user']);
                    $notificationService = app(FCMNotificationService::class);
                    $notificationService->sendBookingConfirmationNotification($booking);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification for trip acceptance: ' . $e->getMessage());
                }
            }
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
     * Update booking status (Admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        // Check if user is admin
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,rejected,cancelled,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::with('user')->findOrFail($id);
        $oldStatus = $booking->status;
        $booking->status = $request->status;
        $booking->save();

        // Send FCM notification when booking is confirmed
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            try {
                $notificationService = app(FCMNotificationService::class);
                $notificationService->sendBookingConfirmationNotification($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send booking confirmation notification: ' . $e->getMessage());
            }
        }

        // Send FCM notification when booking is rejected
        if ($request->status === 'rejected' && $oldStatus !== 'rejected') {
            try {
                $notificationService = app(FCMNotificationService::class);
                $notificationService->sendBookingRejectionNotification($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send booking rejection notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة الحجز بنجاح',
            'data' => $booking
        ]);
    }

    /**
     * Get my bookings
     */
    public function myBookings(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'guide.user', 'site'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $bookings
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
        $booking->load(['user', 'guide.user', 'trip']);

        // Send notification to user when booking is confirmed
        try {
            $notificationService = app(FCMNotificationService::class);
            $notificationService->sendBookingConfirmationNotification($booking);
        } catch (\Exception $e) {
            Log::error('Failed to send notification for trip acceptance: ' . $e->getMessage());
        }

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

