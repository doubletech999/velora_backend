@extends('layouts.guide')

@section('title', 'My Profile - Velora Guide')
@section('page-title', 'My Profile')

@section('content')

<!-- Success/Error Messages -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif

@if($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
        <div>
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <div class="relative inline-block">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-4xl font-bold">{{ substr($guide->user->name ?? $guide->name ?? 'G', 0, 1) }}</span>
                    </div>
                    <button class="absolute bottom-2 right-2 w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="fas fa-camera text-gray-600"></i>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900">{{ $guide->user->name ?? $guide->name ?? 'Guide' }}</h3>
                <p class="text-gray-500">{{ $guide->user->email ?? 'N/A' }}</p>
                
                <div class="flex items-center justify-center mt-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-yellow-400"></i>
                    @endfor
                    <span class="ml-2 text-sm font-semibold text-gray-700">{{ number_format($guide->rating ?? 5.0, 1) }}</span>
                </div>
            </div>
            
            <!-- Basic Stats -->
            <div class="space-y-3 border-t pt-4 mb-4">
                <div class="flex items-center text-sm">
                    <i class="fas fa-calendar-check w-8 text-blue-600"></i>
                    <span class="text-gray-600">Total Tours: <strong class="text-gray-900">{{ $guide->tours_count ?? 0 }}</strong></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-star w-8 text-yellow-500"></i>
                    <span class="text-gray-600">Reviews: <strong class="text-gray-900">{{ $guide->reviews_count ?? 0 }}</strong></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-language w-8 text-green-600"></i>
                    <span class="text-gray-600">Languages: <strong class="text-gray-900">{{ $guide->languages ?? 'English' }}</strong></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-briefcase w-8 text-purple-600"></i>
                    <span class="text-gray-600">Experience: <strong class="text-gray-900">{{ $guide->experience_years ?? 0 }} years</strong></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-clock w-8 text-orange-600"></i>
                    <span class="text-gray-600">Joined: <strong class="text-gray-900">{{ $guide->created_at ? $guide->created_at->format('M Y') : 'N/A' }}</strong></span>
                </div>
            </div>

            <!-- Specializations -->
            @if(isset($guide->specializations) && $guide->specializations)
            <div class="border-t pt-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Specializations
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $guide->specializations) as $spec)
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ trim($spec) }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Certifications -->
            @if(isset($guide->certifications) && $guide->certifications)
            <div class="border-t pt-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-certificate text-green-600 mr-2"></i>Certifications
                </h4>
                <div class="space-y-2">
                    @foreach(array_slice(explode("\n", $guide->certifications), 0, 3) as $cert)
                        @if(trim($cert))
                        <div class="flex items-start text-xs">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                            <span class="text-gray-700 leading-relaxed">{{ trim($cert) }}</span>
                        </div>
                        @endif
                    @endforeach
                    
                    @php
                        $allCerts = array_filter(explode("\n", $guide->certifications), 'trim');
                        $remainingCerts = count($allCerts) - 3;
                    @endphp
                    
                    @if($remainingCerts > 0)
                    <p class="text-xs text-gray-500 italic mt-2">+ {{ $remainingCerts }} more certification(s)</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Profile Information -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-blue-600"></i>Personal Information
            </h3>
            
            <form method="POST" action="{{ route('guide.profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>Full Name *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $guide->user->name ?? $guide->name ?? '') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>Email Address *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $guide->user->email ?? '') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-1"></i>Phone Number
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $guide->phone ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="+970 XXX XXXX">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-language mr-1"></i>Languages
                        </label>
                        <input type="text" name="languages" value="{{ old('languages', $guide->languages ?? 'English') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="English, Arabic">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>Bio / Description
                    </label>
                    <textarea name="bio" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tell us about yourself...">{{ old('bio', $guide->bio ?? '') }}</textarea>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-lock mr-2 text-blue-600"></i>Change Password
            </h3>
            
            <form method="POST" action="{{ route('guide.password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-1"></i>Current Password *
                        </label>
                        <input type="password" name="current_password" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter current password">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>New Password *
                        </label>
                        <input type="password" name="new_password" required minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Minimum 8 characters">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Confirm New Password *
                        </label>
                        <input type="password" name="new_password_confirmation" required minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Re-enter new password">
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Expertise & Certifications -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-certificate mr-2 text-blue-600"></i>Expertise & Certifications
            </h3>
            
            <form method="POST" action="{{ route('guide.expertise.update') }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star mr-1"></i>Specializations
                        </label>
                        <input type="text" name="specializations" value="{{ old('specializations', $guide->specializations ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Historical Tours, Adventure, Cultural">
                        <p class="text-xs text-gray-500 mt-1">Separate multiple specializations with commas</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-award mr-1"></i>Certifications
                        </label>
                        <textarea name="certifications" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="List your certifications (one per line)...">{{ old('certifications', $guide->certifications ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Add each certification on a new line</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-1"></i>Years of Experience
                        </label>
                        <input type="number" name="experience_years" value="{{ old('experience_years', $guide->experience_years ?? 0) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="5" min="0" max="50">
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<script>
// Auto-hide success/error messages
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>

@endsection