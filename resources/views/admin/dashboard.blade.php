@extends('layouts.admin')

@section('title', 'Dashboard - Velora Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Users Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Sites Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-map-marker-alt text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Tourist Sites</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['sites'] }}</p>
            </div>
        </div>
    </div>

    <!-- Guides Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Tour Guides</h3>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['guides'] }}</p>
            </div>
        </div>
    </div>

    <!-- Trips Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i class="fas fa-route text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Planned Trips</h3>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['trips'] }}</p>
            </div>
        </div>
    </div>

    <!-- Reviews Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-star text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Reviews</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['reviews'] }}</p>
            </div>
        </div>
    </div>

    <!-- Bookings Card -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Bookings</h3>
                <p class="text-3xl font-bold text-red-600">{{ $stats['bookings'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Welcome to Velora Admin Panel</h3>
    <p class="text-gray-600 mb-4">
        This is your central hub for managing the Velora tourism application. From here, you can:
    </p>
    <ul class="list-disc list-inside text-gray-600 space-y-2">
        <li>Manage user accounts and permissions</li>
        <li>Add, edit, and remove tourist sites</li>
        <li>Approve and manage tour guides</li>
        <li>Monitor trip planning activities</li>
        <li>Review user feedback and ratings</li>
        <li>Oversee booking management</li>
    </ul>
    
    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-green-600 mr-2"></i>
            <span class="text-green-800 font-medium">System Status: All services are running normally</span>
        </div>
    </div>
</div>
@endsection

