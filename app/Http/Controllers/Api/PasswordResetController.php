<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link
     * إرسال رابط إعادة تعيين كلمة المرور
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Send password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => true,
                    'message' => 'We have emailed your password reset link!'
                ], 200);
            }

            // Handle rate limiting
            if ($status === Password::RESET_THROTTLED) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many password reset requests. Please try again later.'
                ], 429);
            }

            return response()->json([
                'status' => false,
                'message' => 'Unable to send reset link. Please try again later.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Password reset link error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while sending the reset link. Please try again later.'
            ], 500);
        }
    }

    /**
     * Reset password
     * إعادة تعيين كلمة المرور
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'status' => true,
                    'message' => 'Your password has been reset!'
                ], 200);
            }

            // Handle invalid token
            if ($status === Password::INVALID_TOKEN) {
                return response()->json([
                    'status' => false,
                    'message' => 'This password reset token is invalid or has expired.'
                ], 400);
            }

            // Handle invalid user
            if ($status === Password::INVALID_USER) {
                return response()->json([
                    'status' => false,
                    'message' => "We can't find a user with that email address."
                ], 404);
            }

            return response()->json([
                'status' => false,
                'message' => 'This password reset token is invalid or has expired.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while resetting the password. Please try again later.'
            ], 500);
        }
    }
}


