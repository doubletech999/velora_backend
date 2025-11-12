@extends('layouts.admin')

@section('title', 'Trips Management - Velora Admin')
@section('page-title', 'Trip Planning Management')

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
            <h3 class="text-lg font-semibold text-gray-800">All Planned Trips</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.trips') }}" class="flex space-x-2">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Trips</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Trips Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Traveler</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sites</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($trips as $trip)
                <tr class="hover:bg-gray-50" data-trip-id="{{ $trip->id }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $trip->id }}</td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900" data-name="{{ $trip->trip_name }}">{{ $trip->trip_name }}</div>
                            @if($trip->description)
                            <div class="text-sm text-gray-500" data-description="{{ $trip->description }}">{{ Str::limit($trip->description, 60) }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-medium">{{ substr($trip->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900" data-user-name="{{ $trip->user->name }}">{{ $trip->user->name }}</div>
                                <div class="text-xs text-gray-500" data-user-email="{{ $trip->user->email }}">{{ $trip->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <div class="font-medium" data-start="{{ $trip->start_date->format('Y-m-d') }}">{{ $trip->start_date->format('M d, Y') }}</div>
                            <div class="text-gray-500" data-end="{{ $trip->end_date->format('Y-m-d') }}">to {{ $trip->end_date->format('M d, Y') }}</div>
                            <div class="text-xs text-blue-600">
                                {{ $trip->start_date->diffInDays($trip->end_date) + 1 }} day(s)
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap" data-sites="{{ json_encode($trip->sites) }}">
                        <div class="flex items-center">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ count($trip->sites) }} sites
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $now = now();
                            $status = 'upcoming';
                            $statusColor = 'bg-blue-100 text-blue-800';
                            
                            if ($now->isAfter($trip->end_date)) {
                                $status = 'completed';
                                $statusColor = 'bg-gray-100 text-gray-800';
                            } elseif ($now->isBetween($trip->start_date, $trip->end_date)) {
                                $status = 'ongoing';
                                $statusColor = 'bg-green-100 text-green-800';
                            }
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}" data-status="{{ $status }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewTrip({{ $trip->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="confirmDeleteTrip({{ $trip->id }}, '{{ $trip->trip_name }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-route text-4xl mb-2 text-gray-300"></i>
                        <p>No trips found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($trips->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $trips->links() }}
    </div>
    @endif
</div>

<!-- Quick Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Upcoming Trips</p>
                <p class="text-lg font-semibold">{{ $trips->where('start_date', '>', now())->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                <i class="fas fa-play"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ongoing Trips</p>
                <p class="text-lg font-semibold">{{ $trips->filter(function($trip) { return now()->isBetween($trip->start_date, $trip->end_date); })->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-full bg-gray-100 text-gray-600 mr-3">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Completed Trips</p>
                <p class="text-lg font-semibold">{{ $trips->where('end_date', '<', now())->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- View Trip Modal -->
<div id="viewTripModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Trip Details</h3>
                <button onclick="closeViewTripModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewTripContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteTripModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete Trip</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteTripMessage">
                    Are you sure you want to delete this trip?
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteTripModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteTripForm" method="POST" class="inline">
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
function viewTrip(tripId) {
    const modal = document.getElementById('viewTripModal');
    const content = document.getElementById('viewTripContent');
    const row = document.querySelector(`tr[data-trip-id="${tripId}"]`);
    
    const name = row.querySelector('[data-name]').getAttribute('data-name');
    const description = row.querySelector('[data-description]')?.getAttribute('data-description') || 'N/A';
    const userName = row.querySelector('[data-user-name]').getAttribute('data-user-name');
    const userEmail = row.querySelector('[data-user-email]').getAttribute('data-user-email');
    const startDate = row.querySelector('[data-start]').getAttribute('data-start');
    const endDate = row.querySelector('[data-end]').getAttribute('data-end');
    const sites = row.querySelector('[data-sites]').getAttribute('data-sites');
    const sitesCount = JSON.parse(sites).length;
    const status = row.querySelector('[data-status]').getAttribute('data-status');
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const duration = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    
    content.innerHTML = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Trip Name</p>
            <p class="text-sm font-medium">${name}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Description</p>
            <p class="text-sm font-medium">${description}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Traveler</p>
            <p class="text-sm font-medium">${userName}</p>
            <p class="text-xs text-gray-400">${userEmail}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Duration</p>
            <p class="text-sm font-medium">${startDate} to ${endDate}</p>
            <p class="text-xs text-blue-600">${duration} day(s)</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Sites</p>
            <p class="text-sm font-medium">${sitesCount} tourist sites</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-sm font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeViewTripModal() {
    document.getElementById('viewTripModal').classList.add('hidden');
}

function confirmDeleteTrip(tripId, tripName) {
    const modal = document.getElementById('deleteTripModal');
    const message = document.getElementById('deleteTripMessage');
    const form = document.getElementById('deleteTripForm');
    
    message.textContent = `Are you sure you want to delete trip "${tripName}"? This action cannot be undone.`;
    form.action = `/admin/trips/${tripId}`;
    
    modal.classList.remove('hidden');
}

function closeDeleteTripModal() {
    document.getElementById('deleteTripModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewTripModal');
    const deleteModal = document.getElementById('deleteTripModal');
    
    if (event.target === viewModal) closeViewTripModal();
    if (event.target === deleteModal) closeDeleteTripModal();
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