@extends('layouts.admin')

@section('title', 'Edit User - Velora Admin')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Edit User Information</h3>
            <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2"></i>Full Name
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
            </div>

            <!-- Phone -->
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2"></i>Phone Number
                </label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                >
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-2"></i>User Role
                </label>
                <select 
                    id="role" 
                    name="role" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                    <option value="guide" {{ old('role', $user->role) === 'guide' ? 'selected' : '' }}>Guide</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <!-- Language -->
            <div class="mb-6">
                <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-language mr-2"></i>Preferred Language
                </label>
                <select 
                    id="language" 
                    name="language" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
                    <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    <option value="ar" {{ old('language', $user->language) === 'ar' ? 'selected' : '' }}>🇵🇸 Arabic</option>
                </select>
            </div>

            <!-- User Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>User Information
                </h4>
                <div class="space-y-1 text-sm text-blue-700">
                    <p><strong>User ID:</strong> {{ $user->id }}</p>
                    <p><strong>Joined:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3">
                <a 
                    href="{{ route('admin.users') }}" 
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
