@extends('layouts.guide')

@section('title', 'My Bookings - Velora Guide')
@section('page-title', 'My Bookings')

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Bookings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Bookings</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalBookings }}</h3>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Pending</p>
                <h3 class="text-3xl font-bold text-yellow-600">{{ $pendingBookings }}</h3>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Confirmed -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Confirmed</p>
                <h3 class="text-3xl font-bold text-green-600">{{ $confirmedBookings }}</h3>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Completed -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Completed</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ $completedBookings }}</h3>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-flag-checkered text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('guide.bookings') }}">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" id="searchBooking" value="{{ request('search') }}" 
                    placeholder="Search by customer..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <select name="status" id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="date_filter" id="dateFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Dates</option>
                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ $booking->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-medium">{{ substr($booking->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <i class="far fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                        </div>
                        @if(isset($booking->start_time))
                        <div class="text-xs text-gray-500">
                            <i class="far fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}
                            @if(isset($booking->end_time))
                                - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                            @endif
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        ${{ number_format($booking->total_price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($booking->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @elseif($booking->status === 'confirmed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Confirmed
                            </span>
                        @elseif($booking->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-flag-checkered mr-1"></i>Completed
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Cancelled
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewBooking({{ $booking->id }})" 
                                class="text-blue-600 hover:text-blue-900" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            @if($booking->status === 'pending')
                            <a href="{{ route('guide.bookings.confirm', $booking->id) }}" 
                                onclick="return confirm('Confirm this booking?')"
                                class="text-green-600 hover:text-green-900" title="Confirm">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                            
                            @if($booking->status === 'confirmed')
                            <a href="{{ route('guide.bookings.complete', $booking->id) }}" 
                                onclick="return confirm('Mark this booking as completed?')"
                                class="text-purple-600 hover:text-purple-900" title="Mark as Completed">
                                <i class="fas fa-flag-checkered"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No bookings found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $bookings->links() }}
    </div>
    @endif
</div>

<!-- View Booking Modal -->
<div id="viewBookingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4 pb-4 border-b">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>Booking Details
            </h3>
            <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="bookingDetailsContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function viewBooking(bookingId) {
    // Show modal and load booking details
    document.getElementById('viewBookingModal').classList.remove('hidden');
    // You can use AJAX to load booking details
}

function closeViewModal() {
    document.getElementById('viewBookingModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('viewBookingModal');
    if (event.target === modal) {
        closeViewModal();
    }
}
</script>

@endsection