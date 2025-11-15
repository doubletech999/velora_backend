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

        // Filter by type if provided (site, hotel, restaurant, route, camping)
        if ($request->has('type') && $request->type) {
            $validTypes = ['site', 'hotel', 'restaurant', 'route', 'camping'];
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
                'route_price' => (float) ($site->price ?? $guide->hourly_rate ?? 0),
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

        // Get distance (prefer length, fallback to distance)
        $distance = $site->length ?? $site->distance ?? null;

        // Base response with common fields
        $response = [
            'id' => $site->id,
            'name' => $site->name,
            'name_ar' => $site->name_ar ?? $site->name,
            'description' => $site->description,
            'description_ar' => $site->description_ar ?? $site->description,
            'type' => $site->type,
            'location' => $site->location ?? '',
            'location_ar' => $site->location_ar ?? $site->location ?? '',
            'latitude' => (string) $site->latitude,
            'longitude' => (string) $site->longitude,
            'images' => $images,
            'rating' => (float) $rating,
            'review_count' => (int) $reviewCount,
            'coordinates' => $site->coordinates ?? [],
        ];

        // Add type-specific fields
        switch ($site->type) {
            case 'route':
            case 'camping':
                $response = array_merge($response, [
                    'length' => $distance ? (float) $distance : 0,
                    'estimated_duration' => $duration,
                    'difficulty' => $site->difficulty ?? 'medium',
                    'activities' => $site->activities ?? [],
                    'price' => $site->price ? (float) $site->price : null,
                    'guide_id' => $site->guide_id,
                    'guide' => $guideData,
                    'warnings' => $site->warnings ?? [],
                    'warnings_ar' => $site->warnings_ar ?? [],
                ]);
                
                // Add camping-specific fields
                if ($site->type === 'camping') {
                    $response['amenities'] = $site->camping_amenities ?? [];
                    $response['amenities_ar'] = $site->camping_amenities_ar ?? [];
                    $response['capacity'] = $site->capacity ?? null;
                }
                break;

            case 'hotel':
                $response = array_merge($response, [
                    'star_rating' => $site->star_rating ?? null,
                    'price_per_night' => $site->price_per_night ? (float) $site->price_per_night : null,
                    'amenities' => $site->hotel_amenities ?? [],
                    'amenities_ar' => $site->hotel_amenities_ar ?? [],
                    'room_count' => $site->room_count ?? null,
                    'check_in_time' => $site->check_in_time ?? null,
                    'check_out_time' => $site->check_out_time ?? null,
                    'contact_phone' => $site->contact_phone ?? '',
                    'contact_email' => $site->contact_email ?? '',
                ]);
                break;

            case 'restaurant':
                $response = array_merge($response, [
                    'cuisine_type' => $site->cuisine_type ?? '',
                    'cuisine_type_ar' => $site->cuisine_type_ar ?? '',
                    'average_price' => $site->average_price ? (float) $site->average_price : null,
                    'price_range' => $site->price_range ?? null,
                    'opening_hours' => $site->opening_hours ?? [],
                    'opening_hours_ar' => $site->opening_hours_ar ?? [],
                    'contact_phone' => $site->contact_phone ?? '',
                    'contact_email' => $site->contact_email ?? '',
                    'menu_url' => $site->menu_url ?? null,
                ]);
                break;

            case 'site':
            default:
                $response = array_merge($response, [
                    'historical_period' => $site->historical_period ?? null,
                    'historical_period_ar' => $site->historical_period_ar ?? null,
                    'entrance_fee' => $site->entrance_fee ? (float) $site->entrance_fee : null,
                    'opening_hours' => $site->opening_hours ?? [],
                    'best_time_to_visit' => $site->best_time_to_visit ?? null,
                    'best_time_to_visit_ar' => $site->best_time_to_visit_ar ?? null,
                    'activities' => $site->activities ?? [],
                ]);
                break;
        }

        return $response;
    }

    /**

     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:site,hotel,restaurant,route,camping',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'url',
        ];

        // Type-specific validation rules
        $type = $request->input('type');
        
        if (in_array($type, ['route', 'camping'])) {
            $rules['length'] = 'nullable|numeric|min:0';
            $rules['estimated_duration'] = 'required|integer|min:1';
            $rules['difficulty'] = 'required|in:easy,medium,hard';
            $rules['activities'] = 'required|array|min:1';
            $rules['activities.*'] = 'string|max:255';
            $rules['price'] = 'required|numeric|min:0';
            $rules['guide_id'] = 'required|exists:guides,id';
            $rules['warnings'] = 'nullable|array';
            $rules['warnings_ar'] = 'nullable|array';
            $rules['coordinates'] = 'nullable|array';
            
            if ($type === 'camping') {
                $rules['camping_amenities'] = 'nullable|array';
                $rules['camping_amenities_ar'] = 'nullable|array';
                $rules['capacity'] = 'nullable|integer|min:1';
            }
        } elseif ($type === 'hotel') {
            $rules['star_rating'] = 'required|integer|between:1,5';
            $rules['price_per_night'] = 'required|numeric|min:0';
            $rules['hotel_amenities'] = 'required|array|min:1';
            $rules['hotel_amenities_ar'] = 'required|array|min:1';
            $rules['room_count'] = 'nullable|integer|min:1';
            $rules['check_in_time'] = 'nullable|date_format:H:i';
            $rules['check_out_time'] = 'nullable|date_format:H:i';
            $rules['contact_phone'] = 'nullable|string|max:20';
            $rules['contact_email'] = 'nullable|email|max:255';
        } elseif ($type === 'restaurant') {
            $rules['cuisine_type'] = 'required|string|max:100';
            $rules['cuisine_type_ar'] = 'required|string|max:100';
            $rules['average_price'] = 'required|numeric|min:0';
            $rules['price_range'] = 'nullable|in:$,$$,$$$,$$$$';
            $rules['opening_hours'] = 'required|array';
            $rules['opening_hours_ar'] = 'nullable|array';
            $rules['contact_phone'] = 'nullable|string|max:20';
            $rules['contact_email'] = 'nullable|email|max:255';
            $rules['menu_url'] = 'nullable|url|max:500';
        } elseif ($type === 'site') {
            $rules['historical_period'] = 'nullable|string|max:100';
            $rules['historical_period_ar'] = 'nullable|string|max:100';
            $rules['entrance_fee'] = 'nullable|numeric|min:0';
            $rules['opening_hours'] = 'nullable|array';
            $rules['best_time_to_visit'] = 'nullable|string|max:50';
            $rules['best_time_to_visit_ar'] = 'nullable|string|max:50';
            $rules['activities'] = 'nullable|array';
        }

        $validator = Validator::make($request->all(), $rules);

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
            'data' => $this->formatSiteResponse($site)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $site = Site::with(['guide.user', 'reviews'])->find($id);

        if (!$site) {
            return response()->json([
                'status' => false,
                'message' => 'Site not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
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

        $rules = [
            'name' => 'sometimes|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'sometimes|string',
            'description_ar' => 'nullable|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'type' => 'sometimes|in:site,hotel,restaurant,route,camping',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'url',
        ];

        // Type-specific validation rules
        $type = $request->input('type', $site->type);
        
        if (in_array($type, ['route', 'camping'])) {
            $rules['length'] = 'nullable|numeric|min:0';
            $rules['estimated_duration'] = 'sometimes|integer|min:1';
            $rules['difficulty'] = 'sometimes|in:easy,medium,hard';
            $rules['activities'] = 'sometimes|array|min:1';
            $rules['activities.*'] = 'string|max:255';
            $rules['price'] = 'sometimes|numeric|min:0';
            $rules['guide_id'] = 'sometimes|exists:guides,id';
            $rules['warnings'] = 'nullable|array';
            $rules['warnings_ar'] = 'nullable|array';
            $rules['coordinates'] = 'nullable|array';
            
            if ($type === 'camping') {
                $rules['camping_amenities'] = 'nullable|array';
                $rules['camping_amenities_ar'] = 'nullable|array';
                $rules['capacity'] = 'nullable|integer|min:1';
            }
        } elseif ($type === 'hotel') {
            $rules['star_rating'] = 'sometimes|integer|between:1,5';
            $rules['price_per_night'] = 'sometimes|numeric|min:0';
            $rules['hotel_amenities'] = 'sometimes|array|min:1';
            $rules['hotel_amenities_ar'] = 'sometimes|array|min:1';
            $rules['room_count'] = 'nullable|integer|min:1';
            $rules['check_in_time'] = 'nullable|date_format:H:i';
            $rules['check_out_time'] = 'nullable|date_format:H:i';
            $rules['contact_phone'] = 'nullable|string|max:20';
            $rules['contact_email'] = 'nullable|email|max:255';
        } elseif ($type === 'restaurant') {
            $rules['cuisine_type'] = 'sometimes|string|max:100';
            $rules['cuisine_type_ar'] = 'sometimes|string|max:100';
            $rules['average_price'] = 'sometimes|numeric|min:0';
            $rules['price_range'] = 'nullable|in:$,$$,$$$,$$$$';
            $rules['opening_hours'] = 'sometimes|array';
            $rules['opening_hours_ar'] = 'nullable|array';
            $rules['contact_phone'] = 'nullable|string|max:20';
            $rules['contact_email'] = 'nullable|email|max:255';
            $rules['menu_url'] = 'nullable|url|max:500';
        } elseif ($type === 'site') {
            $rules['historical_period'] = 'nullable|string|max:100';
            $rules['historical_period_ar'] = 'nullable|string|max:100';
            $rules['entrance_fee'] = 'nullable|numeric|min:0';
            $rules['opening_hours'] = 'nullable|array';
            $rules['best_time_to_visit'] = 'nullable|string|max:50';
            $rules['best_time_to_visit_ar'] = 'nullable|string|max:50';
            $rules['activities'] = 'nullable|array';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $site->update($request->all());
        $site->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Site updated successfully',
            'data' => $this->formatSiteResponse($site)
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