<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ========================================
// 🧪 TEST ENDPOINT - يمكنني حذفه لاحقاً
// ========================================
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working perfectly! 🚀',
        'app_name' => config('app.name'),
        'timestamp' => now()->toDateTimeString(),
        'laravel_version' => app()->version()
    ]);
});

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resendVerification']);

// Password Reset Routes (Public)
Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])
    ->name('password.update');

// ========================================
// Sites Routes (Public - يمكن للجميع عرض المواقع)
// ========================================
Route::get('/sites', [SiteController::class, 'index']); // عرض جميع المواقع (public)
Route::get('/sites/{id}', [SiteController::class, 'show']); // عرض موقع محدد (public)
Route::get('/activities', [SiteController::class, 'activities']); // عرض قائمة الأنشطة مع العدد (public)

// Protected routes (authentication required)
// Note: user.verified middleware only enforces verification for regular users (role='user')
// Guides and admins are not required to verify their email
Route::middleware(['auth:sanctum', 'user.verified'])->group(function () {
    
    // ========================================
    // Authentication Routes
    // ========================================
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // ========================================
    // User Profile Routes
    // ========================================
    Route::put('/user/profile', [UserProfileController::class, 'updateProfile']);
    Route::put('/user/password', [UserProfileController::class, 'updatePassword']);
    
    // ========================================
    // Notification Routes
    // ========================================
    Route::post('/notifications/update-token', [NotificationController::class, 'updateToken']);
    
    // ========================================
    // Sites Routes (Protected - فقط للمستخدمين المسجلين)
    // ========================================
    Route::post('/sites', [SiteController::class, 'store']); // إضافة موقع (protected)
    Route::put('/sites/{id}', [SiteController::class, 'update']); // تحديث موقع (protected)
    Route::delete('/sites/{id}', [SiteController::class, 'destroy']); // حذف موقع (protected)
    Route::post('/email/resend-verification', [AuthController::class, 'resendVerification'])
        ->name('api.verification.resend');
    
    // ========================================
    // Sites Routes
    // ========================================
    Route::apiResource('sites', SiteController::class);
    
    // ========================================
    // Guides Routes
    // ========================================
    Route::apiResource('guides', GuideController::class);
    
    // Additional Guide Routes
    Route::prefix('guides')->group(function () {
        Route::get('my/profile', [GuideController::class, 'myProfile']);
        Route::get('{id}/availability', [GuideController::class, 'availability']);
    });
    
    // ========================================
    // Reviews Routes
    // ========================================
    Route::apiResource('reviews', ReviewController::class);
    
    // Additional Review Routes
    Route::prefix('reviews')->group(function () {
        Route::get('stats', [ReviewController::class, 'stats']);
        Route::get('my', [ReviewController::class, 'myReviews']);
        Route::get('can-review', [ReviewController::class, 'canReview']);
    });
    
    // ========================================
    // Bookings Routes
    // ========================================
    Route::apiResource('bookings', BookingController::class);
    
    // Additional Booking Routes
    Route::prefix('bookings')->group(function () {
        Route::get('my', [BookingController::class, 'myBookings']);
        Route::post('{id}/confirm', [BookingController::class, 'confirm']);
        Route::get('stats', [BookingController::class, 'stats']);
    });
    
    // Admin booking routes
    Route::prefix('bookings')->group(function () {
        Route::put('{id}/status', [BookingController::class, 'updateStatus']);
    });
});

// Public booking route (optional auth for guests)
Route::post('/bookings', [BookingController::class, 'store']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum', 'user.verified'])->group(function () {
    
    // ========================================
    // Trips Routes
    // ========================================
    Route::apiResource('trips', TripController::class);
    
    // Additional Trip Routes
    Route::prefix('trips')->group(function () {
        Route::get('stats', [TripController::class, 'stats']);
        Route::get('recommendations', [TripController::class, 'recommendations']);
        Route::post('{id}/duplicate', [TripController::class, 'duplicate']);
        Route::post('{id}/sites', [TripController::class, 'addSite']);
        Route::delete('{id}/sites', [TripController::class, 'removeSite']);
    });
    
});

/*
|--------------------------------------------------------------------------
| API Routes Summary
|--------------------------------------------------------------------------
|
| PUBLIC ENDPOINTS:
| POST   /api/test                               - Test API connection
| POST   /api/register                           - User registration
| POST   /api/login                              - User login
|
| PROTECTED ENDPOINTS (require Bearer Token):
|
| Authentication:
| POST   /api/logout                             - User logout
| GET    /api/user                               - Get current user
|
| Sites:
| GET    /api/sites                              - List all sites
| POST   /api/sites                              - Create new site
| GET    /api/sites/{id}                         - Get site details
| PUT    /api/sites/{id}                         - Update site
| DELETE /api/sites/{id}                         - Delete site
|
| Guides:
| GET    /api/guides                             - List approved guides
| POST   /api/guides                             - Create guide profile
| GET    /api/guides/{id}                        - Get guide details
| PUT    /api/guides/{id}                        - Update guide profile
| DELETE /api/guides/{id}                        - Delete guide profile
| GET    /api/guides/my/profile                  - Get my guide profile
| GET    /api/guides/{id}/availability           - Get guide availability
|
| Reviews:
| GET    /api/reviews                            - List reviews
| POST   /api/reviews                            - Create review
| GET    /api/reviews/{id}                       - Get review details
| PUT    /api/reviews/{id}                       - Update review
| DELETE /api/reviews/{id}                       - Delete review
| GET    /api/reviews/stats                      - Get review statistics
| GET    /api/reviews/my                         - Get my reviews
| GET    /api/reviews/can-review                 - Check if can review
|
| Bookings:
| GET    /api/bookings                           - List bookings
| POST   /api/bookings                           - Create booking
| GET    /api/bookings/{id}                      - Get booking details
| PUT    /api/bookings/{id}                      - Update booking
| DELETE /api/bookings/{id}                      - Cancel booking
| POST   /api/bookings/{id}/confirm              - Confirm booking (guides)
| GET    /api/bookings/stats                     - Get booking statistics
|
| Trips:
| GET    /api/trips                              - List my trips
| POST   /api/trips                              - Create trip
| GET    /api/trips/{id}                         - Get trip details
| PUT    /api/trips/{id}                         - Update trip
| DELETE /api/trips/{id}                         - Delete trip
| GET    /api/trips/stats                        - Get trip statistics
| GET    /api/trips/recommendations              - Get recommended sites
| POST   /api/trips/{id}/duplicate               - Duplicate trip
| POST   /api/trips/{id}/sites                   - Add site to trip
| DELETE /api/trips/{id}/sites                   - Remove site from trip
|
*/
