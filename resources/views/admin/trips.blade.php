@extends('layouts.admin')

@section('title', 'Trips Management - Velora Admin')
@section('page-title', 'Trip Planning Management')

@section('content')

<script>
// Define modal functions immediately at the top of the page
window.openAddTripModal = function() {
    try {
        console.log('openAddTripModal called');
        const modal = document.getElementById('addTripModal');
        console.log('Modal element:', modal);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.zIndex = '9999';
            document.body.style.overflow = 'hidden';
            console.log('Trip modal opened successfully');
        } else {
            console.error('Modal addTripModal not found');
            alert('Modal not found. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error opening trip modal:', error);
        alert('Error opening modal: ' + error.message);
    }
};

window.closeAddTripModal = function() {
    try {
        const modal = document.getElementById('addTripModal');
        const form = document.getElementById('addTripForm');
        if (form) {
            form.reset();
            const defaultRadio = document.querySelector('input[name="sites_mode"][value="existing"]');
            if (defaultRadio) {
                defaultRadio.checked = true;
            }
            if (typeof toggleTripContent === 'function') {
                toggleTripContent('existing');
            }
        }
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    } catch (error) {
        console.error('Error closing trip modal:', error);
    }
};

window.openAddRouteModal = function() {
    try {
        console.log('openAddRouteModal called');
        const modal = document.getElementById('addRouteModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.zIndex = '9999';
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Modal addRouteModal not found');
            alert('Modal not found. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error opening route modal:', error);
        alert('Error opening modal: ' + error.message);
    }
};

window.closeAddRouteModal = function() {
    try {
        const modal = document.getElementById('addRouteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    } catch (error) {
        console.error('Error closing route modal:', error);
    }
};

console.log('Modal functions defined at page top');
</script>

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

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="p-5 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-900">All Planned Trips</h3>
            <div class="flex space-x-2">
                <button onclick="window.openAddTripModal(); return false;" type="button"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm font-medium {{ $canCreateTrip ? '' : 'opacity-50 cursor-not-allowed' }}"
                    {{ $canCreateTrip ? '' : 'disabled title="Add at least one user before creating a trip."' }}>
                    <i class="fas fa-plus mr-2"></i>Add New Trip
                </button>
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
        <table class="w-full divide-y divide-gray-200">
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
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($trips as $trip)
                <tr class="table-row-hover" data-trip-id="{{ $trip->id }}"
                    data-price="{{ $trip->price !== null ? $trip->price : '' }}"
                    data-guide-name="{{ $trip->guide_name ?? optional(optional($trip->guide)->user)->name ?? '' }}"
                    data-guide-id="{{ $trip->guide_id ?? '' }}"
                    data-distance="{{ $trip->distance ?? '' }}"
                    data-duration="{{ $trip->duration ?? '' }}"
                    data-activities='@json($trip->activities ?? [])'
                    data-sites='@json($trip->sites ?? [])'
                    data-site-names='@json(collect($trip->sites ?? [])->map(fn($id) => $siteNameMap[$id] ?? null)->filter()->values())'
                    data-custom-sites="{{ e($trip->custom_sites ?? '') }}">
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
                    @php
                        $siteCollection = collect($trip->sites ?? []);
                        $sitesCount = $siteCollection->count();
                        $siteNames = $siteCollection->map(fn($id) => $siteNameMap[$id] ?? null)->filter();
                    @endphp
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            @if($sitesCount > 0)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                                      title="{{ $siteNames->implode(', ') }}">
                                    {{ $sitesCount }} {{ Str::plural('site', $sitesCount) }}
                                </span>
                            @endif
                            @if($trip->custom_sites)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                                    Custom route
                                </span>
                            @endif
                            @if(!$sitesCount && !$trip->custom_sites)
                                <span class="text-xs text-gray-500">No associated sites</span>
                            @endif
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
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-md {{ $statusColor }}" data-status="{{ $status }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewTrip({{ $trip->id }})" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="confirmDeleteTrip({{ $trip->id }}, '{{ $trip->trip_name }}')" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
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
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Upcoming Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $trips->where('start_date', '>', now())->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-plus text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Ongoing Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $trips->filter(function($trip) { return now()->isBetween($trip->start_date, $trip->end_date); })->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-play text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Completed Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $trips->where('end_date', '<', now())->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check text-gray-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Add Route/Camping Modal -->
<div id="addRouteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-route mr-2 text-blue-600"></i>Add New Route/Camping
                </h3>
                <button type="button" onclick="window.closeAddRouteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            @if($errors->any() && old('route_type'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form id="addRouteForm" method="POST" action="{{ route('admin.sites.create') }}">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-2"></i>Name *
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter route/camping name">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-list mr-2"></i>Type *
                        </label>
                        <select name="type" id="route-type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="route" {{ old('type') == 'route' ? 'selected' : '' }}>🗺️ Route</option>
                            <option value="camping" {{ old('type') == 'camping' ? 'selected' : '' }}>⛺ Camping</option>
                        </select>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left mr-2"></i>Description *
                        </label>
                        <textarea name="description" required rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter route/camping description">{{ old('description') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Latitude *
                        </label>
                        <input type="number" name="latitude" step="0.000001" min="-90" max="90" value="{{ old('latitude') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="31.7040">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Longitude *
                        </label>
                        <input type="number" name="longitude" step="0.000001" min="-180" max="180" value="{{ old('longitude') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="35.2066">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Old City, Jerusalem">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-image mr-2"></i>Image URL
                        </label>
                        <input type="url" name="image_url" value="{{ old('image_url') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-route mr-2"></i>Distance (km)
                        </label>
                        <input type="number" name="distance" step="0.01" min="0" value="{{ old('distance') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-clock mr-2"></i>Duration
                        </label>
                        <input type="text" name="duration" value="{{ old('duration') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., 2 days, Weekend trip">
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="window.closeAddRouteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Add Route/Camping
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Trip Modal -->
<div id="addTripModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Trip</h3>
                <button type="button" onclick="window.closeAddTripModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @if(!$canCreateTrip || $availableSites->isEmpty())
                @php
                    $tripWarnings = [];
                    if ($users->isEmpty()) {
                        $tripWarnings[] = 'Please add at least one user to assign as the traveler for the trip.';
                    }
                    if ($availableSites->isEmpty()) {
                        $tripWarnings[] = 'You can still create a trip by writing a custom route description, but linking existing sites is currently not available.';
                    }
                @endphp
                <div class="mb-4 p-3 rounded-md bg-yellow-50 border border-yellow-200 text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ implode(' ', $tripWarnings) }}
                </div>
            @endif

            @if($errors->any() && old('trip_name'))
                <div class="mb-4 p-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="addTripForm" method="POST" action="{{ route('admin.trips.create') }}">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-2"></i>Trip Name *
                        </label>
                        <input type="text" name="trip_name" value="{{ old('trip_name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter trip name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Traveler (User) *
                        </label>
                        <select name="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Traveler</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-plus mr-2"></i>Start Date *
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-check mr-2"></i>End Date *
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left mr-2"></i>Description
                        </label>
                        <textarea name="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Describe the trip plan, highlights, and what travelers should expect.">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-layer-group mr-2"></i>Trip Content Source *
                        </label>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-700">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="sites_mode" value="existing" {{ old('sites_mode', 'existing') === 'existing' ? 'checked' : '' }}>
                                Use existing content (Hotels / Restaurants / Tourist Sites)
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="sites_mode" value="custom" {{ old('sites_mode') === 'custom' ? 'checked' : '' }}>
                                Add custom route / camping description
                            </label>
                        </div>
                    </div>

                    <div class="col-span-2 {{ old('sites_mode', 'existing') === 'custom' ? 'hidden' : '' }}" id="existing-sites-wrapper">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marked-alt mr-2"></i>Select Sites (optional)
                        </label>
                        <select name="sites[]" id="trip-sites-select" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            style="min-height: 140px;">
                            @foreach($availableSites as $site)
                                <option value="{{ $site->id }}" {{ (collect(old('sites', []))->contains($site->id)) ? 'selected' : '' }}>
                                    {{ $site->name }} @if($site->type) ({{ ucfirst($site->type) }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple sites.</p>
                    </div>

                    <div class="col-span-2 {{ old('sites_mode') === 'custom' ? '' : 'hidden' }}" id="custom-route-wrapper">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-route mr-2"></i>Custom Route / Camping Description *
                        </label>
                        <textarea name="custom_sites" id="custom_sites_textarea" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Describe the route, meeting points, contact details, and any special notes.">{{ old('custom_sites') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Use this option when the trip is not tied to existing sites in the system.</p>
                        @error('custom_sites')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-dollar-sign mr-2"></i>Price (USD)
                        </label>
                        <input type="number" name="price" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('price') }}"
                            placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tie mr-2"></i>Select Guide (Optional)
                        </label>
                        <select name="guide_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select Guide --</option>
                            @foreach($guides as $guide)
                                <option value="{{ $guide->id }}" {{ old('guide_id') == $guide->id ? 'selected' : '' }}>
                                    {{ $guide->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Guide Name (if not in list)
                        </label>
                        <input type="text" name="guide_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('guide_name') }}"
                            placeholder="Enter guide name manually">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-route mr-2"></i>Total Distance (km)
                        </label>
                        <input type="number" name="distance" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('distance') }}"
                            placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-clock mr-2"></i>Duration
                        </label>
                        <input type="text" name="duration"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('duration') }}"
                            placeholder="e.g., 2 days, Weekend trip">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tasks mr-2"></i>Included Activities
                        </label>
                        <div class="border border-gray-300 rounded-lg p-3 bg-gray-50" style="max-height: 200px; overflow-y-auto;">
                            @php
                                $activityOptions = [
                                    'hiking' => 'Hiking',
                                    'camping' => 'Camping',
                                    'climbing' => 'Climbing',
                                    'nature' => 'Nature',
                                    'cultural' => 'Cultural',
                                    'archaeological' => 'Archaeological',
                                    'dining' => 'Dining',
                                    'photography' => 'Photography'
                                ];
                                $selectedActivities = collect(old('activities', []));
                            @endphp
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($activityOptions as $value => $label)
                                    <label class="flex items-center space-x-2 p-2 hover:bg-white rounded cursor-pointer">
                                        <input type="checkbox" name="activities[]" value="{{ $value }}" 
                                            {{ $selectedActivities->contains($value) ? 'checked' : '' }}
                                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Select multiple activities by clicking on the checkboxes. You can choose as many as needed.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="window.closeAddTripModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Create Trip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="viewTripModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Trip Details</h3>
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
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mt-4 text-center">Delete Trip</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteTripMessage">
                    Are you sure you want to delete this trip?
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 px-4 py-3">
                <button onclick="closeDeleteTripModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form id="deleteTripForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
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
    const status = row.querySelector('[data-status]').getAttribute('data-status');
    const price = row.getAttribute('data-price');
    const guideName = row.getAttribute('data-guide-name');
    const distance = row.getAttribute('data-distance');
    const durationText = row.getAttribute('data-duration');
    const customSites = row.getAttribute('data-custom-sites') || '';

    let activities = [];
    try {
        const parsedActivities = JSON.parse(row.getAttribute('data-activities') || '[]');
        activities = Array.isArray(parsedActivities) ? parsedActivities : [];
    } catch (error) {
        activities = [];
    }

    let siteList = [];
    try {
        const parsedSites = JSON.parse(row.getAttribute('data-sites') || '[]');
        siteList = Array.isArray(parsedSites) ? parsedSites : [];
    } catch (error) {
        siteList = [];
    }
    const sitesCount = siteList.length;

    let siteNames = [];
    try {
        const parsedNames = JSON.parse(row.getAttribute('data-site-names') || '[]');
        siteNames = Array.isArray(parsedNames) ? parsedNames : [];
    } catch (error) {
        siteNames = [];
    }

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
        ${sitesCount ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Sites</p>
            <p class="text-sm font-medium">${sitesCount} tourist sites</p>
            ${siteNames.length ? `<p class="text-xs text-gray-500 mt-1">${siteNames.join(', ')}</p>` : ''}
        </div>
        ` : ''}
        ${customSites ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Route Overview</p>
            <p class="text-sm font-medium whitespace-pre-line">${customSites}</p>
        </div>
        ` : ''}
        ${price ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Price</p>
            <p class="text-sm font-medium">$${parseFloat(price).toFixed(2)}</p>
        </div>
        ` : ''}
        ${guideName ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Guide</p>
            <p class="text-sm font-medium">${guideName}</p>
        </div>
        ` : ''}
        ${distance ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Total Distance</p>
            <p class="text-sm font-medium">${distance} km</p>
        </div>
        ` : ''}
        ${durationText ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Duration Notes</p>
            <p class="text-sm font-medium">${durationText}</p>
        </div>
        ` : ''}
        ${activities.length ? `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Activities</p>
            <div class="flex flex-wrap gap-2 mt-1">
                ${activities.map(activity => {
                    const label = activity.toString().replace(/_/g, ' ');
                    return `<span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">${label}</span>`;
                }).join('')}
            </div>
        </div>
        ` : ''}
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

function toggleTripContent(mode) {
    const existingWrapper = document.getElementById('existing-sites-wrapper');
    const customWrapper = document.getElementById('custom-route-wrapper');
    const sitesSelect = document.getElementById('trip-sites-select');
    const customTextarea = document.getElementById('custom_sites_textarea');

    if (!existingWrapper || !customWrapper) return;

    if (mode === 'custom') {
        customWrapper.classList.remove('hidden');
        existingWrapper.classList.add('hidden');
        if (sitesSelect) {
            sitesSelect.disabled = true;
            Array.from(sitesSelect.options).forEach(option => option.selected = false);
        }
        if (customTextarea) {
            customTextarea.disabled = false;
            customTextarea.required = true;
        }
    } else {
        existingWrapper.classList.remove('hidden');
        customWrapper.classList.add('hidden');
        if (sitesSelect) {
            sitesSelect.disabled = false;
        }
        if (customTextarea) {
            customTextarea.disabled = true;
            customTextarea.required = false;
            customTextarea.value = '';
        }
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewTripModal');
    const deleteModal = document.getElementById('deleteTripModal');
    const addModal = document.getElementById('addTripModal');
    const routeModal = document.getElementById('addRouteModal');
    
    if (event.target === viewModal) closeViewTripModal();
    if (event.target === deleteModal) closeDeleteTripModal();
    if (event.target === addModal) window.closeAddTripModal();
    if (event.target === routeModal) window.closeAddRouteModal();
}

// Auto-hide success/error messages
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing trip page...');
    
    // Add event listeners for Add Trip button
    const addTripBtn = document.querySelector('button[onclick*="openAddTripModal"]');
    if (addTripBtn) {
        addTripBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add Trip button clicked via event listener');
            if (typeof window.openAddTripModal === 'function') {
                window.openAddTripModal();
            } else {
                console.error('openAddTripModal function not found');
                alert('Function not found. Please refresh the page.');
            }
        });
        console.log('Add Trip button event listener attached');
    } else {
        console.log('Add Trip button not found (may be disabled)');
    }
    
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    const modeRadios = document.querySelectorAll('input[name="sites_mode"]');
    modeRadios.forEach(radio => {
        radio.addEventListener('change', () => toggleTripContent(radio.value));
    });
    const activeMode = document.querySelector('input[name="sites_mode"]:checked');
    if (activeMode) {
        toggleTripContent(activeMode.value);
    }

    @if($errors->any() && old('trip_name'))
        setTimeout(function() {
            window.openAddTripModal();
        }, 100);
    @endif

    console.log('Trip page initialization complete');
});
</script>
@endsection