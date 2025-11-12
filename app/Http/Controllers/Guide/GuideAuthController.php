<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GuideAuthController extends Controller
{
    /**
     * Show guide login form
     */
    public function showLoginForm()
    {
        return view('guide.auth.login');
    }

    /**
     * Handle guide login
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt to authenticate
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Check if user is a guide
            if ($user->role !== 'guide') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'This account is not a guide account.',
                ]);
            }

            // Check if guide profile exists
            $guide = $user->guide;
            if (!$guide) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Guide profile not found. Please contact admin.',
                ]);
            }

            // Check guide approval status
            if (!$guide->is_approved) {
                Auth::logout();
                return back()->with('status', 'pending');
            }

            // Regenerate session
            $request->session()->regenerate();

            // Redirect to guide dashboard
            return redirect()->intended(route('guide.dashboard'));
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle guide logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('guide.login')
            ->with('success', 'You have been logged out successfully.');
    }
}