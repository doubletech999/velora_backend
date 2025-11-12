<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'in:user,guide',
            'language' => 'in:en,ar'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'user',
                'language' => $request->language ?? 'en'
            ]);

            // Auto-verify admins and guides, but require verification for regular users
            if ($user->role !== 'user') {
                $user->email_verified_at = now();
                $user->save();
            } else {
                // Fire Registered event to trigger email verification for regular users
                // Wrap in try-catch so registration doesn't fail if email sending fails
                try {
                    event(new Registered($user));
                } catch (\Exception $e) {
                    // Log the error but don't fail registration
                    Log::error('Failed to send verification email: ' . $e->getMessage());
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }

        // Determine message based on user role
        $message = $user->role === 'user' 
            ? 'User registered successfully. Please verify your email address to access all features.'
            : 'User registered successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'user' => $user,
            'token' => $token,
            'email_verified' => $user->hasVerifiedEmail(),
            'requires_verification' => $user->role === 'user' && !$user->hasVerifiedEmail()
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Check email verification for regular users only
        if ($user->role === 'user' && !$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before logging in. A verification link has been sent to your email.',
                'email_verified' => false,
                'requires_verification' => true
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'email_verified' => $user->hasVerifiedEmail()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();
        
        // Only send verification for regular users
        if ($user->role !== 'user') {
            return response()->json([
                'success' => false,
                'message' => 'Email verification is not required for this account type.'
            ], 400);
        }
        
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification link has been sent to your email address.'
        ]);
    }
}
