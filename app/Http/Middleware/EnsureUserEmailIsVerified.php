<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure email verification for regular users only.
 * Guides and admins are not required to verify their email.
 */
class EnsureUserEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // Only enforce verification for regular users
        if ($user->role === 'user' && !$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Your email address is not verified. Please verify your email to access this resource.',
                'email_verified' => false,
                'requires_verification' => true,
                'verification_url' => url('/email/verify')
            ], 403);
        }
        
        return $next($request);
    }
}


