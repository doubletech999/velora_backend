<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Guide\GuideAuthController;
use App\Http\Controllers\Guide\GuideController;
use App\Http\Controllers\Guide\GuideBookingController;
use App\Http\Controllers\Guide\GuideReviewController;
use App\Http\Controllers\Guide\GuideProfileController;
use App\Http\Controllers\GuidePublicController;
use App\Http\Controllers\Auth\VerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// ========================================
// DEBUG & TEST ROUTES
// ========================================

// Test 1: Check Database Connection
Route::get('/check-db-connection', function () {
    try {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        
        $info = [
            'status' => 'Connected ✓',
            'database_name' => $dbName,
            'driver' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'username' => config('database.connections.mysql.username'),
        ];
        
        $counts = [
            'users' => DB::table('users')->count(),
            'sites' => DB::table('sites')->count(),
            'guides' => DB::table('guides')->count(),
            'trips' => DB::table('trips')->count(),
            'reviews' => DB::table('reviews')->count(),
            'bookings' => DB::table('bookings')->count(),
        ];
        
        $latestSites = DB::table('sites')->orderBy('id', 'desc')->limit(3)->get();
        
        return response()->json([
            'connection_info' => $info,
            'table_counts' => $counts,
            'latest_sites' => $latestSites,
            'message' => 'Check phpMyAdmin -> Database: ' . $dbName . ' -> Table: sites'
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test 2: Add Test Site
Route::get('/test-add-site', function () {
    try {
        $site = \App\Models\Site::create([
            'name' => 'موقع تجريبي ' . now()->format('H:i:s'),
            'description' => 'تم إنشاؤه في ' . now(),
            'latitude' => 31.5000,
            'longitude' => 35.2000,
            'type' => 'historical',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Site created successfully!',
            'site' => $site,
            'database' => DB::connection()->getDatabaseName(),
            'total_sites' => \App\Models\Site::count(),
            'instruction' => 'Now check phpMyAdmin -> ' . DB::connection()->getDatabaseName() . ' -> sites table'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Test 3: Test All Tables
Route::get('/test-all-tables', function () {
    $results = [];
    
    try {
        // معلومات الاتصال
        $results['database'] = DB::connection()->getDatabaseName();
        $results['connection'] = '✅ Connected';
        
        // عدد السجلات في كل جدول
        $results['counts_before'] = [
            'users' => DB::table('users')->count(),
            'sites' => DB::table('sites')->count(),
            'guides' => DB::table('guides')->count(),
            'trips' => DB::table('trips')->count(),
            'reviews' => DB::table('reviews')->count(),
            'bookings' => DB::table('bookings')->count(),
        ];
        
        // آخر 3 سجلات من كل جدول
        $results['latest_records'] = [
            'users' => DB::table('users')->orderBy('id', 'desc')->limit(3)->get(),
            'sites' => DB::table('sites')->orderBy('id', 'desc')->limit(3)->get(),
            'guides' => DB::table('guides')->orderBy('id', 'desc')->limit(3)->get(),
            'trips' => DB::table('trips')->orderBy('id', 'desc')->limit(3)->get(),
            'reviews' => DB::table('reviews')->orderBy('id', 'desc')->limit(3)->get(),
            'bookings' => DB::table('bookings')->orderBy('id', 'desc')->limit(3)->get(),
        ];
        
        // اختبار الكتابة لكل جدول
        $writeTests = [];
        
        // Test 1: Add User
        try {
            $user = DB::table('users')->insertGetId([
                'name' => 'Test User ' . time(),
                'email' => 'test' . time() . '@test.com',
                'password' => bcrypt('12345678'),
                'role' => 'user',
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $writeTests['users'] = '✅ Created (ID: ' . $user . ')';
        } catch (\Exception $e) {
            $writeTests['users'] = '❌ ' . $e->getMessage();
        }
        
        // Test 2: Add Site
        try {
            $site = DB::table('sites')->insertGetId([
                'name' => 'Test Site ' . time(),
                'description' => 'Test Description',
                'latitude' => 31.5,
                'longitude' => 35.2,
                'type' => 'historical',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $writeTests['sites'] = '✅ Created (ID: ' . $site . ')';
        } catch (\Exception $e) {
            $writeTests['sites'] = '❌ ' . $e->getMessage();
        }
        
        // Test 3: Add Trip (needs user_id)
        try {
            $userId = DB::table('users')->first()->id ?? 1;
            $trip = DB::table('trips')->insertGetId([
                'user_id' => $userId,
                'trip_name' => 'Test Trip ' . time(),
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(7),
                'description' => 'Test',
                'sites' => json_encode([1, 2]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $writeTests['trips'] = '✅ Created (ID: ' . $trip . ')';
        } catch (\Exception $e) {
            $writeTests['trips'] = '❌ ' . $e->getMessage();
        }
        
        $results['write_tests'] = $writeTests;
        
        // عدد السجلات بعد الإضافة
        $results['counts_after'] = [
            'users' => DB::table('users')->count(),
            'sites' => DB::table('sites')->count(),
            'guides' => DB::table('guides')->count(),
            'trips' => DB::table('trips')->count(),
            'reviews' => DB::table('reviews')->count(),
            'bookings' => DB::table('bookings')->count(),
        ];
        
        $results['instruction'] = 'الآن افحص phpMyAdmin → Database: ' . $results['database'] . ' → تحقق من الجداول!';
        
    } catch (\Exception $e) {
        $results['error'] = $e->getMessage();
    }
    
    return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

// Test 4: Insert to Specific Table
Route::get('/test-insert/{table}', function ($table) {
    try {
        $id = null;
        
        switch ($table) {
            case 'users':
                $id = DB::table('users')->insertGetId([
                    'name' => 'User ' . time(),
                    'email' => 'user' . time() . '@test.com',
                    'password' => bcrypt('12345678'),
                    'role' => 'user',
                    'language' => 'en',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            case 'sites':
                $id = DB::table('sites')->insertGetId([
                    'name' => 'Site ' . time(),
                    'description' => 'Test Description',
                    'latitude' => 31.5,
                    'longitude' => 35.2,
                    'type' => 'historical',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            case 'trips':
                $userId = DB::table('users')->first()->id ?? 1;
                $id = DB::table('trips')->insertGetId([
                    'user_id' => $userId,
                    'trip_name' => 'Trip ' . time(),
                    'start_date' => now()->addDays(5),
                    'end_date' => now()->addDays(7),
                    'description' => 'Test trip',
                    'sites' => json_encode([1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            case 'guides':
                $userId = DB::table('users')->insertGetId([
                    'name' => 'Guide User ' . time(),
                    'email' => 'guide' . time() . '@test.com',
                    'password' => bcrypt('12345678'),
                    'role' => 'guide',
                    'language' => 'en',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $id = DB::table('guides')->insertGetId([
                    'user_id' => $userId,
                    'bio' => 'Test guide bio',
                    'languages' => 'Arabic,English',
                    'phone' => '+970599123456',
                    'hourly_rate' => 50.00,
                    'is_approved' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            case 'reviews':
                $userId = DB::table('users')->first()->id ?? 1;
                $siteId = DB::table('sites')->first()->id ?? 1;
                
                $id = DB::table('reviews')->insertGetId([
                    'user_id' => $userId,
                    'site_id' => $siteId,
                    'rating' => 5,
                    'comment' => 'Test review',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            case 'bookings':
                $userId = DB::table('users')->first()->id ?? 1;
                $guideId = DB::table('guides')->first()->id ?? null;
                
                if (!$guideId) {
                    throw new \Exception('No guide found. Please create a guide first using /test-insert/guides');
                }
                
                $id = DB::table('bookings')->insertGetId([
                    'user_id' => $userId,
                    'guide_id' => $guideId,
                    'booking_date' => now()->addDays(3),
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'total_price' => 150.00,
                    'status' => 'pending',
                    'notes' => 'Test booking',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                break;
                
            default:
                return response()->json(['error' => 'Invalid table. Use: users, sites, trips, guides, reviews, bookings'], 400);
        }
        
        return response()->json([
            'success' => true,
            'table' => $table,
            'inserted_id' => $id,
            'database' => DB::connection()->getDatabaseName(),
            'total_records' => DB::table($table)->count(),
            'message' => '✅ تم إضافة سجل جديد! افحص phpMyAdmin الآن',
            'instruction' => 'افتح phpMyAdmin → Database: ' . DB::connection()->getDatabaseName() . ' → Table: ' . $table
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'table' => $table
        ], 500);
    }
});

// Test 5: View All Data
Route::get('/view-data/{table}', function ($table) {
    try {
        $validTables = ['users', 'sites', 'guides', 'trips', 'reviews', 'bookings'];
        
        if (!in_array($table, $validTables)) {
            return response()->json(['error' => 'Invalid table'], 400);
        }
        
        $data = DB::table($table)->orderBy('id', 'desc')->limit(10)->get();
        
        return response()->json([
            'table' => $table,
            'database' => DB::connection()->getDatabaseName(),
            'total_records' => DB::table($table)->count(),
            'latest_10_records' => $data
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// ========================================
// ADMIN ROUTES
// ========================================

Route::prefix('admin')->name('admin.')->group(function () {
    
    // ========================================
    // Authentication Routes (No Auth Required)
    // ========================================
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    
    // Logout route - needs to be accessible
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // ========================================
    // Protected Admin Routes (Require Auth)
    // ========================================
    Route::middleware(['web', 'admin'])->group(function () {
        
        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // ========================================
        // Users Management
        // ========================================
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'createUser'])->name('users.create');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        
        // ========================================
        // Sites Management
        // ========================================
        Route::get('/sites', [AdminController::class, 'sites'])->name('sites');
        Route::post('/sites', [AdminController::class, 'createSite'])->name('sites.create');
        Route::get('/sites/{id}', [AdminController::class, 'showSite'])->name('sites.show');
        Route::get('/sites/{id}/edit', [AdminController::class, 'editSite'])->name('sites.edit');
        Route::put('/sites/{id}', [AdminController::class, 'updateSite'])->name('sites.update');
        Route::delete('/sites/{id}', [AdminController::class, 'deleteSite'])->name('sites.delete');
        
        // ========================================
        // Guides Management
        // ========================================
        Route::get('/guides', [AdminController::class, 'guides'])->name('guides');
        Route::post('/guides', [AdminController::class, 'createGuide'])->name('guides.create');
        Route::get('/guides/{id}', [AdminController::class, 'showGuide'])->name('guides.show');
        Route::get('/guides/{id}/edit', [AdminController::class, 'editGuide'])->name('guides.edit');
        Route::put('/guides/{id}', [AdminController::class, 'updateGuide'])->name('guides.update');
        Route::post('/guides/{id}/approve', [AdminController::class, 'approveGuide'])->name('guides.approve');
        Route::delete('/guides/{id}', [AdminController::class, 'deleteGuide'])->name('guides.delete');
        
        // ========================================
        // Trips Management
        // ========================================
        Route::get('/trips', [AdminController::class, 'trips'])->name('trips');
        Route::post('/trips', [AdminController::class, 'createTrip'])->name('trips.create');
        Route::get('/trips/{id}', [AdminController::class, 'showTrip'])->name('trips.show');
        Route::get('/trips/{id}/edit', [AdminController::class, 'editTrip'])->name('trips.edit');
        Route::put('/trips/{id}', [AdminController::class, 'updateTrip'])->name('trips.update');
        Route::delete('/trips/{id}', [AdminController::class, 'deleteTrip'])->name('trips.delete');
        
        // ========================================
        // Reviews Management
        // ========================================
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::get('/reviews/{id}', [AdminController::class, 'showReview'])->name('reviews.show');
        Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->name('reviews.delete');
        
        // ========================================
        // Bookings Management
        // ========================================
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{id}', [AdminController::class, 'showBooking'])->name('bookings.show');
        Route::put('/bookings/{id}/status', [AdminController::class, 'updateBookingStatus'])->name('bookings.updateStatus');
        Route::delete('/bookings/{id}', [AdminController::class, 'deleteBooking'])->name('bookings.delete');
        
        // ========================================
        // Guide Velora Page
        // ========================================
        Route::get('/guide-velora', [AdminController::class, 'guideVelora'])->name('guide-velora');
    });
});

// ========================================
// GUIDE ROUTES
// ========================================

Route::prefix('guide')->name('guide.')->group(function () {
    
    // ========================================
    // Authentication Routes (No Auth Required)
    // ========================================
    Route::get('/login', [GuideAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [GuideAuthController::class, 'login'])->name('login.submit');
    
    // ========================================
    // Protected Guide Routes (Require Auth)
    // ========================================
    Route::middleware(['web', 'guide'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [GuideController::class, 'dashboard'])->name('dashboard');
        
        // ========================================
        // Bookings Management
        // ========================================
        Route::get('/bookings', [GuideBookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/{id}/view', [GuideBookingController::class, 'view'])->name('bookings.view');
        Route::get('/bookings/{id}/confirm', [GuideBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::get('/bookings/{id}/complete', [GuideBookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{id}/cancel', [GuideBookingController::class, 'cancel'])->name('bookings.cancel');
        
        // ========================================
        // Reviews Management
        // ========================================
        Route::get('/reviews', [GuideReviewController::class, 'index'])->name('reviews');
        Route::post('/reviews/{id}/respond', [GuideReviewController::class, 'respond'])->name('reviews.respond');
        
        // ========================================
        // Profile Management
        // ========================================
        Route::get('/profile', [GuideProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [GuideProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [GuideProfileController::class, 'updatePassword'])->name('password.update');
        Route::put('/expertise', [GuideProfileController::class, 'updateExpertise'])->name('expertise.update');
        
        // Public Guide Profile (accessible to everyone)
        Route::get('/guides/{id}', [App\Http\Controllers\GuidePublicController::class, 'show'])->name('guides.show');

        // ========================================
        // Logout
        // ========================================
        Route::post('/logout', [GuideAuthController::class, 'logout'])->name('logout');
    });
});

// ========================================
// EMAIL VERIFICATION ROUTES
// ========================================
// These routes handle email verification for regular users

// Verification notice page (requires authentication)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])
        ->name('verification.notice');
    
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->name('verification.send');
});

// Verification link (can be accessed without authentication)
Route::middleware(['web', 'signed'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify');
});