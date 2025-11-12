<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * Display a listing of user's trips.
     */
    public function index(Request $request)
    {
        $query = Trip::where('user_id', Auth::id())->with('user');

        // Filter by status (upcoming, ongoing, completed)
        if ($request->has('status')) {
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

        // Search by trip name
        if ($request->has('search')) {
            $query->where('trip_name', 'like', '%' . $request->search . '%');
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $trips = $query->orderBy('start_date', 'desc')->paginate(10);

        // Add calculated fields for each trip
        $trips->getCollection()->transform(function ($trip) {
            return $this->enhanceTripData($trip);
        });

        return response()->json([
            'success' => true,
            'data' => $trips
        ]);
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:1000',
            'sites' => 'required|array|min:1',
            'sites.*' => 'integer|exists:sites,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Remove duplicate site IDs
        $siteIds = array_unique($request->sites);

        // Verify all sites exist
        $existingSites = Site::whereIn('id', $siteIds)->pluck('id')->toArray();
        $invalidSites = array_diff($siteIds, $existingSites);

        if (!empty($invalidSites)) {
            return response()->json([
                'success' => false,
                'message' => 'Some sites do not exist: ' . implode(', ', $invalidSites)
            ], 422);
        }

        $trip = Trip::create([
            'user_id' => Auth::id(),
            'trip_name' => $request->trip_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'sites' => $siteIds
        ]);

        $trip->load('user');
        $enhancedTrip = $this->enhanceTripData($trip);

        return response()->json([
            'success' => true,
            'message' => 'Trip created successfully',
            'data' => $enhancedTrip
        ], 201);
    }

    /**
     * Display the specified trip.
     */
    public function show(string $id)
    {
        $trip = Trip::where('user_id', Auth::id())->with('user')->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        $enhancedTrip = $this->enhanceTripData($trip, true);

        return response()->json([
            'success' => true,
            'data' => $enhancedTrip
        ]);
    }

    /**
     * Update the specified trip.
     */
    public function update(Request $request, string $id)
    {
        $trip = Trip::where('user_id', Auth::id())->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        // Check if trip can be modified (only future trips can be fully modified)
        $now = now();
        $isUpcoming = Carbon::parse($trip->start_date)->greaterThan($now);

        if (!$isUpcoming && $request->has(['start_date', 'end_date', 'sites'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify dates or sites for ongoing or completed trips'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'trip_name' => 'string|max:255',
            'start_date' => $isUpcoming ? 'date|after_or_equal:today' : 'prohibited',
            'end_date' => $isUpcoming ? 'date|after_or_equal:start_date' : 'prohibited',
            'description' => 'nullable|string|max:1000',
            'sites' => $isUpcoming ? 'array|min:1' : 'prohibited',
            'sites.*' => $isUpcoming ? 'integer|exists:sites,id' : 'prohibited'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only(['trip_name', 'description']);

        if ($isUpcoming) {
            if ($request->has('start_date')) {
                $updateData['start_date'] = $request->start_date;
            }
            
            if ($request->has('end_date')) {
                $updateData['end_date'] = $request->end_date;
            }
            
            if ($request->has('sites')) {
                $siteIds = array_unique($request->sites);
                $existingSites = Site::whereIn('id', $siteIds)->pluck('id')->toArray();
                $invalidSites = array_diff($siteIds, $existingSites);

                if (!empty($invalidSites)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some sites do not exist: ' . implode(', ', $invalidSites)
                    ], 422);
                }

                $updateData['sites'] = $siteIds;
            }
        }

        $trip->update($updateData);
        $trip->load('user');
        $enhancedTrip = $this->enhanceTripData($trip);

        return response()->json([
            'success' => true,
            'message' => 'Trip updated successfully',
            'data' => $enhancedTrip
        ]);
    }

    /**
     * Remove the specified trip.
     */
    public function destroy(string $id)
    {
        $trip = Trip::where('user_id', Auth::id())->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        // Check if trip can be deleted (only upcoming trips)
        $now = now();
        $isUpcoming = Carbon::parse($trip->start_date)->greaterThan($now);

        if (!$isUpcoming) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete ongoing or completed trips'
            ], 422);
        }

        $trip->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trip deleted successfully'
        ]);
    }

    /**
     * Add a site to the trip.
     */
    public function addSite(Request $request, string $id)
    {
        $trip = Trip::where('user_id', Auth::id())->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        // Check if trip can be modified
        $now = now();
        $isUpcoming = Carbon::parse($trip->start_date)->greaterThan($now);

        if (!$isUpcoming) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify ongoing or completed trips'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer|exists:sites,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $siteId = $request->site_id;
        $currentSites = $trip->sites;

        if (in_array($siteId, $currentSites)) {
            return response()->json([
                'success' => false,
                'message' => 'Site is already in the trip'
            ], 409);
        }

        $currentSites[] = $siteId;
        $trip->update(['sites' => $currentSites]);

        $enhancedTrip = $this->enhanceTripData($trip);

        return response()->json([
            'success' => true,
            'message' => 'Site added to trip successfully',
            'data' => $enhancedTrip
        ]);
    }

    /**
     * Remove a site from the trip.
     */
    public function removeSite(Request $request, string $id)
    {
        $trip = Trip::where('user_id', Auth::id())->find($id);

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        // Check if trip can be modified
        $now = now();
        $isUpcoming = Carbon::parse($trip->start_date)->greaterThan($now);

        if (!$isUpcoming) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify ongoing or completed trips'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'site_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $siteId = $request->site_id;
        $currentSites = $trip->sites;

        if (!in_array($siteId, $currentSites)) {
            return response()->json([
                'success' => false,
                'message' => 'Site is not in the trip'
            ], 409);
        }

        // Must have at least one site in trip
        if (count($currentSites) <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Trip must have at least one site'
            ], 422);
        }

        $currentSites = array_values(array_filter($currentSites, function($id) use ($siteId) {
            return $id != $siteId;
        }));

        $trip->update(['sites' => $currentSites]);

        $enhancedTrip = $this->enhanceTripData($trip);

        return response()->json([
            'success' => true,
            'message' => 'Site removed from trip successfully',
            'data' => $enhancedTrip
        ]);
    }

    /**
     * Get trip statistics for current user.
     */
    public function stats()
    {
        $userId = Auth::id();
        $now = now();

        $totalTrips = Trip::where('user_id', $userId)->count();
        $upcomingTrips = Trip::where('user_id', $userId)
            ->where('start_date', '>', $now->format('Y-m-d'))
            ->count();
        
        $ongoingTrips = Trip::where('user_id', $userId)
            ->where('start_date', '<=', $now->format('Y-m-d'))
            ->where('end_date', '>=', $now->format('Y-m-d'))
            ->count();
        
        $completedTrips = Trip::where('user_id', $userId)
            ->where('end_date', '<', $now->format('Y-m-d'))
            ->count();

        // Get most visited site types
        $allTrips = Trip::where('user_id', $userId)->get();
        $allSiteIds = [];
        foreach ($allTrips as $trip) {
            $allSiteIds = array_merge($allSiteIds, $trip->sites);
        }

        $siteTypes = Site::whereIn('id', $allSiteIds)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $stats = [
            'total_trips' => $totalTrips,
            'upcoming_trips' => $upcomingTrips,
            'ongoing_trips' => $ongoingTrips,
            'completed_trips' => $completedTrips,
            'total_sites_visited' => count(array_unique($allSiteIds)),
            'favorite_site_types' => $siteTypes
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recommended sites based on user's trip history.
     */
    public function recommendations()
    {
        $userId = Auth::id();
        
        // Get user's previously visited sites
        $userTrips = Trip::where('user_id', $userId)->get();
        $visitedSiteIds = [];
        $visitedTypes = [];

        foreach ($userTrips as $trip) {
            $visitedSiteIds = array_merge($visitedSiteIds, $trip->sites);
        }

        $visitedSiteIds = array_unique($visitedSiteIds);

        // Get types of visited sites
        if (!empty($visitedSiteIds)) {
            $visitedSites = Site::whereIn('id', $visitedSiteIds)->get();
            $visitedTypes = $visitedSites->pluck('type')->unique()->toArray();
        }

        // Recommend sites of similar types that user hasn't visited
        $recommendedSites = Site::whereNotIn('id', $visitedSiteIds)
            ->when(!empty($visitedTypes), function($query) use ($visitedTypes) {
                return $query->whereIn('type', $visitedTypes);
            })
            ->limit(10)
            ->get();

        // If not enough recommendations, add popular sites
        if ($recommendedSites->count() < 5) {
            $additionalSites = Site::whereNotIn('id', array_merge($visitedSiteIds, $recommendedSites->pluck('id')->toArray()))
                ->limit(5)
                ->get();
            
            $recommendedSites = $recommendedSites->merge($additionalSites);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'recommended_sites' => $recommendedSites,
                'visited_sites_count' => count($visitedSiteIds),
                'favorite_types' => $visitedTypes
            ]
        ]);
    }

    /**
     * Duplicate an existing trip.
     */
    public function duplicate(string $id)
    {
        $originalTrip = Trip::where('user_id', Auth::id())->find($id);

        if (!$originalTrip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found'
            ], 404);
        }

        // Create new trip with modified name and future dates
        $newStartDate = now()->addDays(7)->format('Y-m-d');
        $originalDuration = Carbon::parse($originalTrip->start_date)->diffInDays(Carbon::parse($originalTrip->end_date));
        $newEndDate = now()->addDays(7 + $originalDuration)->format('Y-m-d');

        $newTrip = Trip::create([
            'user_id' => Auth::id(),
            'trip_name' => $originalTrip->trip_name . ' (Copy)',
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
            'description' => $originalTrip->description,
            'sites' => $originalTrip->sites
        ]);

        $newTrip->load('user');
        $enhancedTrip = $this->enhanceTripData($newTrip);

        return response()->json([
            'success' => true,
            'message' => 'Trip duplicated successfully',
            'data' => $enhancedTrip
        ], 201);
    }

    /**
     * Enhance trip data with calculated fields and site details.
     */
    private function enhanceTripData($trip, $includeFullSiteDetails = false)
    {
        $tripData = $trip->toArray();
        
        // Calculate trip status
        $now = now();
        $startDate = Carbon::parse($trip->start_date);
        $endDate = Carbon::parse($trip->end_date);

        if ($startDate->greaterThan($now)) {
            $status = 'upcoming';
        } elseif ($startDate->lessOrEqualTo($now) && $endDate->greaterOrEqualTo($now)) {
            $status = 'ongoing';
        } else {
            $status = 'completed';
        }

        $tripData['status'] = $status;
        $tripData['duration_days'] = $startDate->diffInDays($endDate) + 1;
        $tripData['sites_count'] = count($trip->sites);

        // Add site details if requested
        if ($includeFullSiteDetails && !empty($trip->sites)) {
            $sites = Site::whereIn('id', $trip->sites)->get();
            $tripData['site_details'] = $sites->toArray();
            
            // Group sites by type
            $tripData['sites_by_type'] = $sites->groupBy('type')->map(function($typeSites) {
                return $typeSites->count();
            })->toArray();
        } else {
            // Just add basic site info
            $tripData['sites_by_type'] = Site::whereIn('id', $trip->sites ?? [])
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        }

        // Calculate days until trip starts (if upcoming)
        if ($status === 'upcoming') {
            $tripData['days_until_start'] = $now->diffInDays($startDate);
        }

        return $tripData;
    }
}