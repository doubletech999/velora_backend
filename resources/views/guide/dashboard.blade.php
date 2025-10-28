@extends('layouts.guide')

@section('title', 'Guide Dashboard - Velora')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-blue-100">Here's what's happening with your tours today.</p>
        </div>
        <div class="hidden md:block">
            <i class="fas fa-compass text-6xl opacity-20"></i>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Bookings -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Total Bookings</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_bookings'] }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_bookings'] }}</p>
            </div>
        </div>
    </div>

    <!-- Confirmed Bookings -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Confirmed</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['confirmed_bookings'] }}</p>
            </div>
        </div>
    </div>

    <!-- Average Rating -->
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i class="fas fa-star text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Rating</h3>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['average_rating'], 1) }}/5</p>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Bookings -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Upcoming Bookings
            </h3>
            <a href="{{ route('guide.bookings') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="p-6">
        @forelse($upcomingBookings as $booking)
        <div class="flex items-center justify-between p-4 mb-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                    <span class="text-white font-medium">{{ substr($booking->user->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $booking->user->name }}</p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>{{ $booking->booking_date->format('M d, Y') }}
                        <i class="fas fa-clock ml-2 mr-1"></i>{{ date('g:i A', strtotime($booking->start_time)) }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($booking->status) }}
                </span>
                <p class="text-sm text-gray-600 mt-1">${{ number_format($booking->total_price, 2) }}</p>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-2 text-gray-300"></i>
            <p>No upcoming bookings</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Recent Reviews -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-star mr-2 text-yellow-500"></i>Recent Reviews
            </h3>
            <a href="{{ route('guide.reviews') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="p-6">
        @forelse($recentReviews as $review)
        <div class="p-4 mb-3 bg-gray-50 rounded-lg">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white text-sm font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $review->user->name }}</p>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                @else
                                    <i class="fas fa-star text-gray-300 text-xs"></i>
                                @endif
                            @endfor
                            <span class="ml-2 text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @if($review->comment)
            <p class="text-sm text-gray-600 ml-13">{{ $review->comment }}</p>
            @endif
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-star text-4xl mb-2 text-gray-300"></i>
            <p>No reviews yet</p>
        </div>
        @endforelse
    </div>
</div>
@endsection