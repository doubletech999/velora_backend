@extends('layouts.admin')

@section('title', 'Bookings Management - Velora Admin')
@section('page-title', 'Bookings Management')

@section('content')

<!-- Success/Error Messages -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

<div class="bg-white rounded-lg shadow-md">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">All Guide Bookings</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.bookings') }}" class="flex space-x-2">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Filter by date" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guide</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50" data-booking-id="{{ $booking->id }}"
                    data-trip-id="{{ $booking->trip_id ?? '' }}"
                    data-trip-name="{{ $booking->trip->trip_name ?? '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-medium">{{ substr($booking->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900" data-customer-name="{{ $booking->user->name }}">{{ $booking->user->name }}</div>
                                <div class="text-xs text-gray-500" data-customer-email="{{ $booking->user->email }}">{{ $booking->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($booking->trip)
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-route text-white text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->trip->trip_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $booking->trip->start_date->format('M d') }} - {{ $booking->trip->end_date->format('M d, Y') }}
                                    </div>
                                    @if($booking->trip->sites)
                                        <div class="text-xs text-gray-400">{{ count($booking->trip->sites) }} sites</div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($booking->guide)
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-medium">{{ substr($booking->guide->user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900" data-guide-name="{{ $booking->guide->user->name }}">{{ $booking->guide->user->name }}</div>
                                    <div class="text-xs text-gray-500" data-guide-rate="{{ $booking->guide->hourly_rate }}">${{ number_format($booking->guide->hourly_rate, 2) }}/hour</div>
                                </div>
                            </div>
                        @elseif($booking->trip && $booking->trip->guide_name)
                            <div class="text-sm text-gray-700">{{ $booking->trip->guide_name }}</div>
                            <div class="text-xs text-gray-400">(Not registered guide)</div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900" data-booking-date="{{ $booking->booking_date->format('Y-m-d') }}">
                                {{ $booking->booking_date->format('M d, Y') }}
                            </div>
                            <div class="text-xs text-gray-500" data-start-time="{{ $booking->start_time }}" data-end-time="{{ $booking->end_time }}">
                                {{ date('g:i A', strtotime($booking->start_time)) }} - 
                                {{ date('g:i A', strtotime($booking->end_time)) }}
                            </div>
                            @php
                                $isToday = $booking->booking_date->isToday();
                                $isPast = $booking->booking_date->isPast();
                                $isFuture = $booking->booking_date->isFuture();
                            @endphp
                            @if($isToday)
                                <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">Today</span>
                            @elseif($isFuture)
                                <span class="inline-flex px-1 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Upcoming</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @php
                            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                            $duration = $startTime->diffInHours($endTime);
                        @endphp
                        <span data-duration="{{ $duration }}">{{ $duration }} hour{{ $duration > 1 ? 's' : '' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900" data-price="{{ $booking->total_price }}">${{ number_format($booking->total_price, 2) }}</div>
                        <div class="text-xs text-gray-500">Total Amount</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800 @endif" data-status="{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewBooking({{ $booking->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($booking->status === 'pending')
                            <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')" class="text-green-600 hover:text-green-900 transition-colors" title="Confirm">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <button onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')" class="text-red-600 hover:text-red-900 transition-colors" title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                            <button onclick="confirmDeleteBooking({{ $booking->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-calendar-check text-4xl mb-2 text-gray-300"></i>
                        <p>No bookings found</p>
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

<!-- Bookings Statistics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Pending Bookings</p>
                <p class="text-lg font-semibold">{{ $bookings->where('status', 'pending')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Confirmed Bookings</p>
                <p class="text-lg font-semibold">{{ $bookings->where('status', 'confirmed')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Today's Bookings</p>
                <p class="text-lg font-semibold">{{ $bookings->filter(function($booking) { return $booking->booking_date->isToday(); })->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Revenue</p>
                <p class="text-lg font-semibold">${{ number_format($bookings->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- View Booking Modal -->
<div id="viewBookingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Booking Details</h3>
                <button onclick="closeViewBookingModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewBookingContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBookingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete Booking</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">
                    Are you sure you want to delete this booking? This action cannot be undone.
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteBookingForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function viewBooking(bookingId) {
    const modal = document.getElementById('viewBookingModal');
    const content = document.getElementById('viewBookingContent');
    const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
    
    if (!row) {
        alert('Booking row not found');
        return;
    }
    
    const customerName = row.querySelector('[data-customer-name]')?.getAttribute('data-customer-name') || 'N/A';
    const customerEmail = row.querySelector('[data-customer-email]')?.getAttribute('data-customer-email') || 'N/A';
    const guideNameEl = row.querySelector('[data-guide-name]');
    const guideName = guideNameEl ? guideNameEl.getAttribute('data-guide-name') : null;
    const guideRateEl = row.querySelector('[data-guide-rate]');
    const guideRate = guideRateEl ? guideRateEl.getAttribute('data-guide-rate') : null;
    const tripId = row.getAttribute('data-trip-id');
    const tripName = row.getAttribute('data-trip-name');
    const bookingDate = row.querySelector('[data-booking-date]')?.getAttribute('data-booking-date') || 'N/A';
    const startTime = row.querySelector('[data-start-time]')?.getAttribute('data-start-time') || 'N/A';
    const endTime = row.querySelector('[data-end-time]')?.getAttribute('data-end-time') || 'N/A';
    const duration = row.querySelector('[data-duration]')?.getAttribute('data-duration') || 'N/A';
    const price = row.querySelector('[data-price]')?.getAttribute('data-price') || '0';
    const status = row.querySelector('[data-status]')?.getAttribute('data-status') || 'pending';
    
    // Format time display
    const formatTime = (timeStr) => {
        if (timeStr === 'N/A') return 'N/A';
        try {
            const time = timeStr.split(':');
            const hours = parseInt(time[0]);
            const minutes = time[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        } catch (e) {
            return timeStr;
        }
    };
    
    let tripSection = '';
    if (tripId && tripName) {
        tripSection = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Trip</p>
            <p class="text-sm font-medium">${tripName}</p>
            <p class="text-xs text-gray-400">Trip ID: #${tripId}</p>
        </div>`;
    }
    
    let guideSection = '';
    if (guideName && guideRate) {
        guideSection = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Guide</p>
            <p class="text-sm font-medium">${guideName}</p>
            <p class="text-xs text-gray-400">$${parseFloat(guideRate).toFixed(2)}/hour</p>
        </div>`;
    } else {
        guideSection = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Guide</p>
            <p class="text-sm font-medium text-gray-400">No guide assigned</p>
        </div>`;
    }
    
    content.innerHTML = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Booking ID</p>
            <p class="text-sm font-medium">#${String(bookingId).padStart(4, '0')}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Customer</p>
            <p class="text-sm font-medium">${customerName}</p>
            <p class="text-xs text-gray-400">${customerEmail}</p>
        </div>
        ${tripSection}
        ${guideSection}
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Date & Time</p>
            <p class="text-sm font-medium">${bookingDate}</p>
            <p class="text-xs text-gray-400">${formatTime(startTime)} - ${formatTime(endTime)}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Duration</p>
            <p class="text-sm font-medium">${duration} hour${parseInt(duration) !== 1 ? 's' : ''}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Total Price</p>
            <p class="text-sm font-medium">$${parseFloat(price).toFixed(2)}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-sm font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeViewBookingModal() {
    document.getElementById('viewBookingModal').classList.add('hidden');
}

function updateBookingStatus(bookingId, status) {
    if (confirm(`Are you sure you want to ${status} this booking?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/bookings/${bookingId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        form.appendChild(methodField);
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDeleteBooking(bookingId) {
    const modal = document.getElementById('deleteBookingModal');
    const form = document.getElementById('deleteBookingForm');
    
    form.action = `/admin/bookings/${bookingId}`;
    modal.classList.remove('hidden');
}

function closeDeleteBookingModal() {
    document.getElementById('deleteBookingModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewBookingModal');
    const deleteModal = document.getElementById('deleteBookingModal');
    
    if (event.target === viewModal) closeViewBookingModal();
    if (event.target === deleteModal) closeDeleteBookingModal();
}

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
