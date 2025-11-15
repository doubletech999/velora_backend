<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    /**
     * Update user profile
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'preferred_language' => 'sometimes|in:ar,en',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 20 characters.',
            'preferred_language.in' => 'The preferred language must be either ar or en.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update user data
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
                // If email changed, reset email verification
                if ($user->isDirty('email') && $user->role === 'user') {
                    $user->email_verified_at = null;
                }
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('preferred_language')) {
                $user->language = $request->preferred_language;
            }

            $user->save();

            // Load relationships and calculate stats
            $user->load(['trips', 'bookings']);
            
            // Calculate completed trips
            $completedTrips = $user->trips()->count();
            
            // Calculate saved trips (you might have a saved_trips table or relationship)
            $savedTrips = 0; // Adjust based on your implementation
            
            // Calculate achievements (you might have an achievements table or relationship)
            $achievements = 0; // Adjust based on your implementation

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_image_url' => null, // Add if you have profile images
                    'completed_trips' => $completedTrips,
                    'saved_trips' => $savedTrips,
                    'achievements' => $achievements,
                    'preferred_language' => $user->language,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'email_verified_at' => $user->email_verified_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the profile. Please try again later.'
            ], 500);
        }
    }

    /**
     * Update user password
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ], [
            'current_password.required' => 'The current password field is required.',
            'new_password.required' => 'The new password field is required.',
            'new_password.confirmed' => 'The password confirmation does not match.',
            'new_password.min' => 'The password must be at least 8 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'current_password' => ['The current password is incorrect.']
                    ]
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the password. Please try again later.'
            ], 500);
        }
    }
}


