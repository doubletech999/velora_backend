<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager load relationships
        $query = Site::with(['guide.user', 'reviews']);

        // Only show sites, hotels, and restaurants (not routes or camping)
        $query->whereIn('type', ['site', 'hotel', 'restaurant']);

        // Filter by type if provided (site, hotel, restaurant)
        if ($request->has('type') && $request->type) {
            $validTypes = ['site', 'hotel', 'restaurant'];
            if (in_array($request->type, $validTypes)) {
                $query->where('type', $request->type);
            }
        }

        // Filter by activity if provided
        if ($request->has('activity') && $request->activity) {
            $query->whereJsonContains('activities', $request->activity);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description_ar', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location_ar', 'like', '%' . $searchTerm . '%');
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $sites = $query->paginate($perPage);

        // Format response
        $formattedSites = $sites->map(function ($site) {
            return $this->formatSiteResponse($site);
        });

        return response()->json([
            'data' => $formattedSites,
            'current_page' => $sites->currentPage(),
            'last_page' => $sites->lastPage(),
            'per_page' => $sites->perPage(),
            'total' => $sites->total(),
        ]);
    }

    /**
     * Format site response for Flutter app.
     */
    private function formatSiteResponse($site)
    {
        // Calculate rating from reviews if not set
        $rating = $site->rating ?? 0;
        $reviewCount = $site->review_count ?? $site->reviews->count();

        // Get guide information
        $guide = $site->guide;
        $guideName = null;
        $guideNameAr = null;
        $guideData = null;

        if ($guide && $guide->user) {
            $guideName = $guide->user->name;
            $guideNameAr = $guide->user->name_ar ?? $guide->user->name;
            $guideData = [
                'id' => $guide->id,
                'name' => $guideName,
                'name_ar' => $guideNameAr,
                'route_price' => (float) ($guide->hourly_rate ?? 0),
                'user' => [
                    'name' => $guide->user->name,
                    'name_ar' => $guide->user->name_ar ?? $guide->user->name,
                ],
            ];
        } else {
            // Fallback to guide_name if guide relationship doesn't exist
            $guideName = $site->guide_name;
            $guideNameAr = $site->guide_name;
        }

        // Format images
        $images = [];
        
        // Get images from database
        $siteImages = $site->getRawOriginal('images') ?? $site->images;
        
        if ($siteImages) {
            if (is_string($siteImages)) {
                $decoded = json_decode($siteImages, true);
                $images = $decoded ?: [];
            } elseif (is_array($siteImages)) {
                $images = $siteImages;
            }
        }
        
        // Fallback to image_url if images is empty
        if (empty($images) && $site->image_url) {
            $images = [$site->image_url];
        }

        // Convert image URLs to full URLs if they are relative
        $images = array_map(function ($image) {
            if ($image && is_string($image)) {
                // If it's a relative path, make it absolute
                if (!filter_var($image, FILTER_VALIDATE_URL)) {
                    return asset('storage/' . ltrim($image, '/'));
                }
            }
            return $image;
        }, array_filter($images)); // Filter out null/empty values

        // Get duration (prefer estimated_duration, fallback to duration)
        $duration = $site->estimated_duration ?? null;
        if (!$duration && $site->duration) {
            // Try to extract hours from duration string (e.g., "2 hours" -> 2)
            preg_match('/(\d+)/', $site->duration, $matches);
            $duration = isset($matches[1]) ? (int)$matches[1] : null;
        }

        // Get distance (prefer distance, fallback to length)
        $distance = $site->distance ?? $site->length ?? null;

        return [
            'id' => $site->id,
            'name' => $site->name,
            'name_ar' => $site->name_ar ?? $site->name,
            'description' => $site->description,
            'description_ar' => $site->description_ar ?? $site->description,
            'type' => $site->type,
            'location' => $site->location ?? '',
            'location_ar' => $site->location_ar ?? $site->location ?? '',
            'address' => $site->address ?? '',
            'city' => $site->city ?? '',
            'contact_phone' => $site->contact_phone ?? '',
            'contact_email' => $site->contact_email ?? '',
            'website' => $site->website ?? '',
            'working_hours' => $site->working_hours ?? '',
            'latitude' => (string) $site->latitude,
            'longitude' => (string) $site->longitude,
            'images' => $images,
            'length' => $distance ? (float) $distance : null,
            'distance' => $distance ? (float) $distance : null,
            'distance_km' => $distance ? (float) $distance : null,
            'estimated_duration' => $duration,
            'duration' => $duration,
            'duration_hours' => $duration,
            'difficulty' => $site->difficulty ?? null,
            'activities' => $site->activities ?? [],
            'rating' => (float) $rating,
            'review_count' => (int) $reviewCount,
            'reviews_count' => (int) $reviewCount,
            'price' => $site->price ? (float) $site->price : null,
            'guide_id' => $site->guide_id,
            'guide_name' => $guideName,
            'guide_name_ar' => $guideNameAr,
            'guide' => $guideData,
        ];
    }

    /**

     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:site,hotel,restaurant',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'working_hours' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'activities' => 'nullable|array',
            'activities.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $site = Site::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Site created successfully',
            'data' => $site
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $site = Site::with(['guide.user', 'reviews'])->find($id);
        $site = Site::find($id);

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatSiteResponse($site)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $site = Site::find($id);

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'string',
            'description_ar' => 'nullable|string',
            'latitude' => 'numeric|between:-90,90',
            'longitude' => 'numeric|between:-180,180',
            'type' => 'in:site,hotel,restaurant',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'working_hours' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'activities' => 'nullable|array',
            'activities.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $site->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Site updated successfully',
            'data' => $site
        ]);
    }

    /**
     * Get list of activities with count of sites for each activity.
     */
    public function activities()
    {
        // قائمة الأنشطة المتاحة
        $availableActivities = [
            'hiking' => ['name' => 'Hiking', 'name_ar' => 'المشي', 'icon' => 'walking'],
            'camping' => ['name' => 'Camping', 'name_ar' => 'التخييم', 'icon' => 'campground'],
            'climbing' => ['name' => 'Climbing', 'name_ar' => 'التسلق', 'icon' => 'mountain'],
            'religious' => ['name' => 'Religious', 'name_ar' => 'ديني', 'icon' => 'mosque'],
            'cultural' => ['name' => 'Cultural', 'name_ar' => 'ثقافي', 'icon' => 'landmark'],
            'nature' => ['name' => 'Nature', 'name_ar' => 'طبيعة', 'icon' => 'tree'],
            'archaeological' => ['name' => 'Archaeological', 'name_ar' => 'أثري', 'icon' => 'monument'],
        ];

        // حساب عدد المواقع لكل نشاط
        $activitiesWithCount = [];
        
        foreach ($availableActivities as $activityKey => $activityInfo) {
            // حساب عدد المواقع التي تحتوي على هذا النشاط
            $count = Site::whereJsonContains('activities', $activityKey)->count();
            
            $activitiesWithCount[] = [
                'id' => $activityKey,
                'name' => $activityInfo['name'],
                'name_ar' => $activityInfo['name_ar'],
                'icon' => $activityInfo['icon'],
                'count' => $count,
                'count_text' => $count . ' مسار متوفر', // أو "مكان متوفر" حسب النوع
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $activitiesWithCount,
        ]);
    }

    /**

     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $site = Site::find($id);

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found'
            ], 404);
        }

        $site->delete();

        return response()->json([
            'success' => true,
            'message' => 'Site deleted successfully'
        ]);
    }
}