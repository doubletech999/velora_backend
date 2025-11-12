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
        $query = Site::query();

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sites = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $sites
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:historical,natural,cultural',
            'image_url' => 'nullable|url'
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
        $site = Site::find($id);

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $site
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
            'description' => 'string',
            'latitude' => 'numeric|between:-90,90',
            'longitude' => 'numeric|between:-180,180',
            'type' => 'in:historical,natural,cultural',
            'image_url' => 'nullable|url'
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
