<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Site;
use App\Models\Guide;
use App\Models\Trip;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // ========================================
    // DASHBOARD
    // ========================================
    
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'sites' => Site::count(),
            'guides' => Guide::count(),
            'trips' => Trip::count(),
            'reviews' => Review::count(),
            'bookings' => Booking::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // ========================================
    // USERS MANAGEMENT
    // ========================================
    
    public function users(Request $request)
    {
        $query = User::query();
        
        // Filter by role if provided
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }
    
    public function createUser(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,guide,admin',
            'language' => 'required|in:en,ar',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'role.required' => 'Role is required',
            'language.required' => 'Language is required',
        ]);
        
        try {
            // Create new user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'language' => $validated['language'],
                'email_verified_at' => now(),
            ]);
            
            return redirect()->route('admin.users')
                ->with('success', 'User "' . $user->name . '" created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
    
    public function showUser($id)
    {
        try {
            $user = User::with(['guide', 'trips', 'reviews', 'bookings'])->findOrFail($id);
            return view('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->with('error', 'User not found');
        }
    }
    
    public function editUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->with('error', 'User not found');
        }
    }
    
    public function updateUser(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,guide,admin',
            'language' => 'required|in:en,ar',
        ]);
        
        // تخزين الـ role القديم
        $oldRole = $user->role;
        $newRole = $validated['role'];
        
        // تحديث بيانات المستخدم
        $user->update($validated);
        
        // إذا تم تحويل المستخدم من user إلى guide
        if ($oldRole !== 'guide' && $newRole === 'guide') {
            
            // التحقق من وجود ملف guide للمستخدم
            $guide = Guide::where('user_id', $user->id)->first();
            
            if (!$guide) {
                // إنشاء ملف guide جديد بمعلومات افتراضية
                $guide = Guide::create([
                    'user_id' => $user->id,
                    'bio' => 'Professional tour guide',
                    'languages' => 'Arabic, English',
                    'phone' => '+970599000000',
                    'hourly_rate' => 50.00,
                    'is_approved' => false, // يحتاج موافقة الأدمن
                ]);
                
                return redirect()->route('admin.guides')
                    ->with('success', 'User converted to Guide successfully! Please review and approve the guide profile.');
            } else {
                // إذا كان لديه ملف guide مسبقاً
                return redirect()->route('admin.guides')
                    ->with('success', 'User role updated to Guide successfully!');
            }
        }
        
        // إذا تم تحويل المستخدم من guide إلى user
        if ($oldRole === 'guide' && $newRole !== 'guide') {
            // حذف ملف الـ guide (اختياري)
            // أو يمكنك الإبقاء عليه وتعطيله فقط
            $guide = Guide::where('user_id', $user->id)->first();
            if ($guide) {
                // خيار 1: حذف الملف
                // $guide->delete();
                
                // خيار 2: تعطيل الموافقة فقط
                $guide->update(['is_approved' => false]);
            }
        }
        
        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully!');
            
    } catch (ValidationException $e) {
        return redirect()->back()
            ->withInput()
            ->withErrors($e->errors());
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update user: ' . $e->getMessage());
    }
}
    
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting admin users
            if ($user->role === 'admin') {
                return redirect()->back()
                    ->with('error', 'Cannot delete admin users!');
            }
            
            $userName = $user->name;
            $user->delete();
            
            return redirect()->route('admin.users')
                ->with('success', 'User "' . $userName . '" deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    // ========================================
    // SITES MANAGEMENT
    // ========================================
    
    public function sites(Request $request)
    {
        $query = Site::query();
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        $sites = $query->orderBy('id', 'desc')->paginate(15);
        $guides = Guide::with('user')->where('is_approved', true)->get();
        return view('admin.sites', compact('sites', 'guides'));
    }
    
    public function createSite(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'type' => 'required|in:historical,natural,cultural',
                'image_url' => 'nullable|url',
                'price' => 'nullable|numeric|min:0',
                'guide_name' => 'nullable|string|max:255',
                'guide_id' => 'nullable|exists:guides,id',
                'distance' => 'nullable|numeric|min:0',
                'duration' => 'nullable|string|max:255',
                'activities' => 'nullable|array',
                'activities.*' => 'string|max:255'
            ]);
            
            $site = Site::create($validated);
            
            return redirect()->route('admin.sites')
                ->with('success', 'Site "' . $site->name . '" created successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create site: ' . $e->getMessage());
        }
    }
    
    public function showSite($id)
    {
        try {
            $site = Site::with('reviews')->findOrFail($id);
            return view('admin.sites.show', compact('site'));
        } catch (\Exception $e) {
            return redirect()->route('admin.sites')
                ->with('error', 'Site not found');
        }
    }
    
    public function editSite($id)
    {
        try {
            $site = Site::findOrFail($id);
            $guides = Guide::with('user')->where('is_approved', true)->get();
            return view('admin.sites.edit', compact('site', 'guides'));
        } catch (\Exception $e) {
            return redirect()->route('admin.sites')
                ->with('error', 'Site not found');
        }
    }
    
    public function updateSite(Request $request, $id)
    {
        try {
            $site = Site::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'type' => 'required|in:historical,natural,cultural',
                'image_url' => 'nullable|url',
                'price' => 'nullable|numeric|min:0',
                'guide_name' => 'nullable|string|max:255',
                'guide_id' => 'nullable|exists:guides,id',
                'distance' => 'nullable|numeric|min:0',
                'duration' => 'nullable|string|max:255',
                'activities' => 'nullable|array',
                'activities.*' => 'string|max:255'
            ]);
            
            $site->update($validated);
            
            return redirect()->route('admin.sites')
                ->with('success', 'Site updated successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update site: ' . $e->getMessage());
        }
    }
    
    public function deleteSite($id)
    {
        try {
            $site = Site::findOrFail($id);
            $siteName = $site->name;
            $site->delete();
            
            return redirect()->route('admin.sites')
                ->with('success', 'Site "' . $siteName . '" deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete site: ' . $e->getMessage());
        }
    }

    // ========================================
    // GUIDES MANAGEMENT
    // ========================================
    
    public function guides(Request $request)
    {
        $query = Guide::with('user');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }
        
        $guides = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.guides', compact('guides'));
    }
    
    public function createGuide(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'bio' => 'required|string|max:1000',
                'languages' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'hourly_rate' => 'required|numeric|min:0|max:999.99',
                'is_approved' => 'boolean'
            ]);
            
            $guide = Guide::create($validated);
            
            // Update user role
            $guide->user->update(['role' => 'guide']);
            
            return redirect()->route('admin.guides')
                ->with('success', 'Guide created successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create guide: ' . $e->getMessage());
        }
    }
    
    public function showGuide($id)
    {
        try {
            $guide = Guide::with(['user', 'bookings', 'reviews'])->findOrFail($id);
            return view('admin.guides.show', compact('guide'));
        } catch (\Exception $e) {
            return redirect()->route('admin.guides')
                ->with('error', 'Guide not found');
        }
    }
    
    public function editGuide($id)
    {
        try {
            $guide = Guide::with('user')->findOrFail($id);
            return view('admin.guides.edit', compact('guide'));
        } catch (\Exception $e) {
            return redirect()->route('admin.guides')
                ->with('error', 'Guide not found');
        }
    }
    
    public function updateGuide(Request $request, $id)
    {
        try {
            $guide = Guide::findOrFail($id);
            
            $validated = $request->validate([
                'bio' => 'required|string|max:1000',
                'languages' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'hourly_rate' => 'required|numeric|min:0|max:999.99',
                'is_approved' => 'boolean'
            ]);
            
            $guide->update($validated);
            
            return redirect()->route('admin.guides')
                ->with('success', 'Guide updated successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update guide: ' . $e->getMessage());
        }
    }
    
    public function approveGuide($id)
    {
        try {
            $guide = Guide::findOrFail($id);
            $guide->update(['is_approved' => true]);
            
            return redirect()->route('admin.guides')
                ->with('success', 'Guide approved successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve guide: ' . $e->getMessage());
        }
    }
    
    public function deleteGuide($id)
    {
        try {
            $guide = Guide::findOrFail($id);
            
            // Update user role back to regular user
            $guide->user->update(['role' => 'user']);
            
            $guideName = $guide->user->name;
            $guide->delete();
            
            return redirect()->route('admin.guides')
                ->with('success', 'Guide "' . $guideName . '" deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete guide: ' . $e->getMessage());
        }
    }

    // ========================================
    // TRIPS MANAGEMENT
    // ========================================
    
    public function trips(Request $request)
    {
        $query = Trip::with('user');
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $now = now();
            
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_date', '>', $now->format('Y-m-d'));
                    break;
                case 'ongoing':
                    $query->where('start_date', '<=', $now->format('Y-m-d'))
                          ->where('end_date', '>=', $now->format('Y-m-d'));
                    break;
                case 'completed':
                    $query->where('end_date', '<', $now->format('Y-m-d'));
                    break;
            }
        }
        
        $trips = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.trips', compact('trips'));
    }
    
    public function showTrip($id)
    {
        try {
            $trip = Trip::with(['user'])->findOrFail($id);
            
            // Get site details
            $sites = Site::whereIn('id', $trip->sites)->get();
            
            return view('admin.trips.show', compact('trip', 'sites'));
        } catch (\Exception $e) {
            return redirect()->route('admin.trips')
                ->with('error', 'Trip not found');
        }
    }
    
    public function editTrip($id)
    {
        try {
            $trip = Trip::with('user')->findOrFail($id);
            $sites = Site::all();
            
            return view('admin.trips.edit', compact('trip', 'sites'));
        } catch (\Exception $e) {
            return redirect()->route('admin.trips')
                ->with('error', 'Trip not found');
        }
    }
    
    public function updateTrip(Request $request, $id)
    {
        try {
            $trip = Trip::findOrFail($id);
            
            $validated = $request->validate([
                'trip_name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string',
                'sites' => 'required|array|min:1',
                'sites.*' => 'exists:sites,id',
                'price' => 'nullable|numeric|min:0',
                'guide_name' => 'nullable|string|max:255',
                'guide_id' => 'nullable|exists:guides,id',
                'distance' => 'nullable|numeric|min:0',
                'duration' => 'nullable|string|max:255',
                'activities' => 'nullable|array',
                'activities.*' => 'string|max:255'
            ]);
            
            $trip->update($validated);
            
            return redirect()->route('admin.trips')
                ->with('success', 'Trip updated successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update trip: ' . $e->getMessage());
        }
    }
    
    public function deleteTrip($id)
    {
        try {
            $trip = Trip::findOrFail($id);
            $tripName = $trip->trip_name;
            $trip->delete();
            
            return redirect()->route('admin.trips')
                ->with('success', 'Trip "' . $tripName . '" deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete trip: ' . $e->getMessage());
        }
    }

    // ========================================
    // REVIEWS MANAGEMENT
    // ========================================
    
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'site', 'guide.user']);
        
        // Filter by type if provided
        if ($request->has('type') && $request->type != '') {
            if ($request->type === 'site') {
                $query->whereNotNull('site_id');
            } elseif ($request->type === 'guide') {
                $query->whereNotNull('guide_id');
            }
        }
        
        // Filter by rating if provided
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }
        
        $reviews = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.reviews', compact('reviews'));
    }
    
    public function showReview($id)
    {
        try {
            $review = Review::with(['user', 'site', 'guide.user'])->findOrFail($id);
            return view('admin.reviews.show', compact('review'));
        } catch (\Exception $e) {
            return redirect()->route('admin.reviews')
                ->with('error', 'Review not found');
        }
    }
    
    public function deleteReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            
            return redirect()->route('admin.reviews')
                ->with('success', 'Review deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete review: ' . $e->getMessage());
        }
    }

    // ========================================
    // BOOKINGS MANAGEMENT
    // ========================================
    
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'guide.user', 'trip']);
        
        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date if provided
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('booking_date', $request->date);
        }
        
        // Filter by trip if provided
        if ($request->has('trip_id') && $request->trip_id != '') {
            $query->where('trip_id', $request->trip_id);
        }
        
        $bookings = $query->orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->paginate(15);
        
        return view('admin.bookings', compact('bookings'));
    }
    
    public function showBooking($id)
    {
        try {
            $booking = Booking::with(['user', 'guide.user', 'trip'])->findOrFail($id);
            return view('admin.bookings.show', compact('booking'));
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings')
                ->with('error', 'Booking not found');
        }
    }
    
    public function updateBookingStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,cancelled,completed'
            ]);
            
            $booking = Booking::findOrFail($id);
            $booking->update(['status' => $validated['status']]);
            
            return redirect()->route('admin.bookings')
                ->with('success', 'Booking status updated successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update booking: ' . $e->getMessage());
        }
    }
    
    public function deleteBooking($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();
            
            return redirect()->route('admin.bookings')
                ->with('success', 'Booking deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }

    // ========================================
    // GUIDE VELORA PAGE
    // ========================================
    
    public function guideVelora()
    {
        // Gather statistics for the guide page
        $stats = [
            'total_guides' => Guide::count(),
            'approved_guides' => Guide::where('is_approved', true)->count(),
            'pending_guides' => Guide::where('is_approved', false)->count(),
            'total_bookings' => Booking::count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'total_reviews' => Review::whereNotNull('guide_id')->count(),
            'average_rating' => Review::whereNotNull('guide_id')->avg('rating') ?? 0,
        ];
        
        return view('admin.guide-velora', compact('stats'));
    }
}
