<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('guide.login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        // Check if user is a guide
        if ($user->role !== 'guide') {
            Auth::logout();
            return redirect()->route('guide.login')->with('error', 'Access denied. Guide account required.');
        }

        // Check if guide profile exists
        $guide = $user->guide;
        if (!$guide) {
            Auth::logout();
            return redirect()->route('guide.login')->with('error', 'Guide profile not found. Please contact admin.');
        }

        // Check if guide is approved
        if (!$guide->is_approved) {
            Auth::logout();
            return redirect()->route('guide.login')->with('status', 'pending');
        }

        return $next($request);
    }
}