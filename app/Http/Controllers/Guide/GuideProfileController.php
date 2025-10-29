<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class GuideProfileController extends Controller
{
    /**
     * Display guide profile
     */
    public function index()
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();
        
        if (!$guide) {
            return redirect()->route('guide.login')->with('error', 'Guide profile not found.');
        }
        
        // Load additional statistics
        $guide->tours_count = 0; // Set to 0 for now
        $guide->reviews_count = \App\Models\Review::count();
        
        return view('guide.profile', compact('guide'));
    }
    
    /**
     * Update personal information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'languages' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000'
        ]);
        
        try {
            // Update user table
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
            
            // Update guide table - only existing columns
            $updateData = [];
            
            // Check which columns exist in the guides table
            if (Schema::hasColumn('guides', 'phone')) {
                $updateData['phone'] = $request->phone;
            }
            if (Schema::hasColumn('guides', 'languages')) {
                $updateData['languages'] = $request->languages;
            }
            if (Schema::hasColumn('guides', 'bio')) {
                $updateData['bio'] = $request->bio;
            }
            
            if (!empty($updateData)) {
                $guide->update($updateData);
            }
            
            return redirect()->route('guide.profile')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('guide.profile')
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        try {
            // Check if current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            
            return redirect()->route('guide.profile')
                ->with('success', 'Password updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('guide.profile')
                ->with('error', 'Error updating password: ' . $e->getMessage());
        }
    }
    
    /**
     * Update expertise and certifications
     */
    public function updateExpertise(Request $request)
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->firstOrFail();
        
        $request->validate([
            'specializations' => 'nullable|string|max:500',
            'certifications' => 'nullable|string|max:1000',
            'experience_years' => 'nullable|integer|min:0|max:50'
        ]);
        
        try {
            // Only update fields that exist in the database
            $updateData = [];
            
            if (Schema::hasColumn('guides', 'specializations')) {
                $updateData['specializations'] = $request->specializations;
            }
            if (Schema::hasColumn('guides', 'certifications')) {
                $updateData['certifications'] = $request->certifications;
            }
            if (Schema::hasColumn('guides', 'experience_years')) {
                $updateData['experience_years'] = $request->experience_years;
            }
            
            if (!empty($updateData)) {
                $guide->update($updateData);
                
                return redirect()->route('guide.profile')
                    ->with('success', 'Expertise information updated successfully!');
            } else {
                return redirect()->route('guide.profile')
                    ->with('error', 'No fields available to update. Please contact administrator.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('guide.profile')
                ->with('error', 'Error updating expertise: ' . $e->getMessage());
        }
    }
}