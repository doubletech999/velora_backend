<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a User instance
        if (!$user instanceof User) {
            return redirect()->intended('/');
        }
        
        // If user is already verified, redirect
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        // Only show verification notice for users that must verify their email
        if (!$user->needsEmailVerification()) {
            return redirect()->intended('/');
        }

        return view('auth.verify-email', [
            'user' => $user,
            'language' => $user->language ?? 'en'
        ]);
    }

    /**
     * Mark the user's email address as verified.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        
        // Only verify regular users
        if (!$user->needsEmailVerification()) {
            return redirect('/')->with('message', 'Email verification is not required for this account type.');
        }
        
        // Verify the hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('verified', true);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Optionally log the user in
        Auth::login($user);

        return redirect('/')->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $user = $request->user();
        
        // Ensure user is authenticated and is a User instance
        if (!$user instanceof User) {
            return back()->with('status', 'verification-link-sent');
        }
        
        // Only send verification for regular users
        if (!$user->needsEmailVerification()) {
            return back()->with('status', 'verification-link-sent');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

