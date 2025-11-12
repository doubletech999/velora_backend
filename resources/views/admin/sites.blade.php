@extends('layouts.admin')

@section('title', 'Sites Management - Velora Admin')
@section('page-title', 'Tourist Sites Management')

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
            <h3 class="text-lg font-semibold text-gray-800">All Tourist Sites</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.sites') }}" class="flex space-x-2">
                    <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="historical" {{ request('type') == 'historical' ? 'selected' : '' }}>Historical</option>
                        <option value="natural" {{ request('type') == 'natural' ? 'selected' : '' }}>Natural</option>
                        <option value="cultural" {{ request('type') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                    </select>
                </form>
                <button onclick="openAddSiteModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Site
                </button>
            </div>
        </div>
    </div>

    <!-- Sites Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sites as $site)
                <tr class="hover:bg-gray-50" data-site-id="{{ $site->id }}"
                    data-name="{{ $site->name }}"
                    data-description="{{ $site->description }}"
                    data-type="{{ $site->type }}"
                    data-latitude="{{ $site->latitude }}"
                    data-longitude="{{ $site->longitude }}"
                    data-image-url="{{ $site->image_url ?? '' }}"
                    data-price="{{ $site->price ?? '' }}"
                    data-guide-name="{{ $site->guide_name ?? '' }}"
                    data-guide-id="{{ $site->guide_id ?? '' }}"
                    data-distance="{{ $site->distance ?? '' }}"
                    data-duration="{{ $site->duration ?? '' }}"
                    data-activities="{{ json_encode($site->activities ?? []) }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $site->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $site->name }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($site->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($site->type === 'historical') bg-blue-100 text-blue-800
                            @elseif($site->type === 'natural') bg-green-100 text-green-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ ucfirst($site->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($site->latitude, 4) }}, {{ number_format($site->longitude, 4) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $site->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewSite({{ $site->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editSite({{ $site->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDeleteSite({{ $site->id }}, '{{ $site->name }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-map-marker-alt text-4xl mb-2 text-gray-300"></i>
                        <p>No sites found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($sites->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $sites->links() }}
    </div>
    @endif
</div>

<!-- Add Site Modal -->
<div id="addSiteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>Add New Site
                </h3>
                <button onclick="closeAddSiteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form id="addSiteForm" method="POST" action="{{ route('admin.sites.create') }}">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-2"></i>Site Name *
                        </label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter site name">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left mr-2"></i>Description *
                        </label>
                        <textarea name="description" required rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter site description"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Latitude *
                        </label>
                        <input type="number" name="latitude" step="0.000001" min="-90" max="90" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="31.7040">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Longitude *
                        </label>
                        <input type="number" name="longitude" step="0.000001" min="-180" max="180" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="35.2066">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-list mr-2"></i>Type *
                        </label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Type</option>
                            <option value="historical">Historical</option>
                            <option value="natural">Natural</option>
                            <option value="cultural">Cultural</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-image mr-2"></i>Image URL
                        </label>
                        <input type="url" name="image_url"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-dollar-sign mr-2"></i>Price (USD)
                        </label>
                        <input type="number" name="price" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
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
                                <option value="{{ $guide->id }}">{{ $guide->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Guide Name (if not in list)
                        </label>
                        <input type="text" name="guide_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter guide name manually">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-route mr-2"></i>Distance (km)
                        </label>
                        <input type="number" name="distance" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-clock mr-2"></i>Duration
                        </label>
                        <input type="text" name="duration"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g., 2 hours or 1 day">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tasks mr-2"></i>Available Activities
                        </label>
                        <select name="activities[]" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            style="min-height: 100px;">
                            <option value="photography">Photography</option>
                            <option value="hiking">Hiking</option>
                            <option value="swimming">Swimming</option>
                            <option value="camping">Camping</option>
                            <option value="sightseeing">Sightseeing</option>
                            <option value="shopping">Shopping</option>
                            <option value="dining">Dining</option>
                            <option value="cultural_tours">Cultural Tours</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple activities</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddSiteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Create Site
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Site Modal -->
<div id="viewSiteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Site Details</h3>
                <button onclick="closeViewSiteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewSiteContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Site Modal -->
<div id="editSiteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit Site</h3>
                <button onclick="closeEditSiteModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editSiteForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-2"></i>Site Name
                        </label>
                        <input type="text" name="name" id="edit_site_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left mr-2"></i>Description
                        </label>
                        <textarea name="description" id="edit_site_description" required rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Latitude
                        </label>
                        <input type="number" name="latitude" id="edit_site_latitude" step="0.000001" min="-90" max="90" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-pin mr-2"></i>Longitude
                        </label>
                        <input type="number" name="longitude" id="edit_site_longitude" step="0.000001" min="-180" max="180" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-list mr-2"></i>Type
                        </label>
                        <select name="type" id="edit_site_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="historical">Historical</option>
                            <option value="natural">Natural</option>
                            <option value="cultural">Cultural</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-image mr-2"></i>Image URL
                        </label>
                        <input type="url" name="image_url" id="edit_site_image_url"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-dollar-sign mr-2"></i>Price (USD)
                        </label>
                        <input type="number" name="price" id="edit_site_price" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tie mr-2"></i>Select Guide
                        </label>
                        <select name="guide_id" id="edit_site_guide_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select Guide --</option>
                            @foreach($guides as $guide)
                                <option value="{{ $guide->id }}">{{ $guide->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Guide Name
                        </label>
                        <input type="text" name="guide_name" id="edit_site_guide_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter guide name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-route mr-2"></i>Distance (km)
                        </label>
                        <input type="number" name="distance" id="edit_site_distance" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-clock mr-2"></i>Duration
                        </label>
                        <input type="text" name="duration" id="edit_site_duration"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g., 2 hours or 1 day">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tasks mr-2"></i>Available Activities
                        </label>
                        <select name="activities[]" id="edit_site_activities" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            style="min-height: 100px;">
                            <option value="photography">Photography</option>
                            <option value="hiking">Hiking</option>
                            <option value="swimming">Swimming</option>
                            <option value="camping">Camping</option>
                            <option value="sightseeing">Sightseeing</option>
                            <option value="shopping">Shopping</option>
                            <option value="dining">Dining</option>
                            <option value="cultural_tours">Cultural Tours</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple activities</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditSiteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteSiteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete Site</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteSiteMessage">
                    Are you sure you want to delete this site?
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteSiteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteSiteForm" method="POST" class="inline">
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
function openAddSiteModal() {
    document.getElementById('addSiteModal').classList.remove('hidden');
}

function closeAddSiteModal() {
    document.getElementById('addSiteModal').classList.add('hidden');
}

function viewSite(siteId) {
    const modal = document.getElementById('viewSiteModal');
    const content = document.getElementById('viewSiteContent');
    const row = document.querySelector(`tr[data-site-id="${siteId}"]`);
    
    const name = row.getAttribute('data-name');
    const description = row.getAttribute('data-description');
    const type = row.getAttribute('data-type');
    const latitude = row.getAttribute('data-latitude');
    const longitude = row.getAttribute('data-longitude');
    const price = row.getAttribute('data-price');
    const guideName = row.getAttribute('data-guide-name');
    const distance = row.getAttribute('data-distance');
    const duration = row.getAttribute('data-duration');
    const activitiesJson = row.getAttribute('data-activities') || '[]';
    const activities = JSON.parse(activitiesJson);
    const created = row.querySelectorAll('td')[4].textContent.trim();
    
    let html = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Name</p>
            <p class="text-sm font-medium">${name}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Description</p>
            <p class="text-sm font-medium">${description}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Type</p>
            <p class="text-sm font-medium">${type.charAt(0).toUpperCase() + type.slice(1)}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Location</p>
            <p class="text-sm font-medium">Lat: ${latitude}, Lng: ${longitude}</p>
        </div>
    `;
    
    if (price) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Price</p>
            <p class="text-sm font-medium">$${parseFloat(price).toFixed(2)}</p>
        </div>
        `;
    }
    
    if (guideName) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Guide</p>
            <p class="text-sm font-medium">${guideName}</p>
        </div>
        `;
    }
    
    if (distance) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Distance</p>
            <p class="text-sm font-medium">${distance} km</p>
        </div>
        `;
    }
    
    if (duration) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Duration</p>
            <p class="text-sm font-medium">${duration}</p>
        </div>
        `;
    }
    
    if (activities && activities.length > 0) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Activities</p>
            <p class="text-sm font-medium">${activities.join(', ')}</p>
        </div>
        `;
    }
    
    html += `
        <div>
            <p class="text-xs text-gray-500">Created Date</p>
            <p class="text-sm font-medium">${created}</p>
        </div>
    `;
    
    content.innerHTML = html;
    modal.classList.remove('hidden');
}

function closeViewSiteModal() {
    document.getElementById('viewSiteModal').classList.add('hidden');
}

function editSite(siteId) {
    const modal = document.getElementById('editSiteModal');
    const form = document.getElementById('editSiteForm');
    const row = document.querySelector(`tr[data-site-id="${siteId}"]`);
    
    // Get all data from row attributes
    document.getElementById('edit_site_name').value = row.getAttribute('data-name') || '';
    document.getElementById('edit_site_description').value = row.getAttribute('data-description') || '';
    document.getElementById('edit_site_type').value = row.getAttribute('data-type') || '';
    document.getElementById('edit_site_latitude').value = row.getAttribute('data-latitude') || '';
    document.getElementById('edit_site_longitude').value = row.getAttribute('data-longitude') || '';
    document.getElementById('edit_site_image_url').value = row.getAttribute('data-image-url') || '';
    document.getElementById('edit_site_price').value = row.getAttribute('data-price') || '';
    document.getElementById('edit_site_guide_id').value = row.getAttribute('data-guide-id') || '';
    document.getElementById('edit_site_guide_name').value = row.getAttribute('data-guide-name') || '';
    document.getElementById('edit_site_distance').value = row.getAttribute('data-distance') || '';
    document.getElementById('edit_site_duration').value = row.getAttribute('data-duration') || '';
    
    // Handle activities (JSON array)
    const activitiesSelect = document.getElementById('edit_site_activities');
    const activitiesJson = row.getAttribute('data-activities') || '[]';
    const activities = JSON.parse(activitiesJson);
    
    // Clear previous selections
    Array.from(activitiesSelect.options).forEach(option => {
        option.selected = activities.includes(option.value);
    });
    
    form.action = `/admin/sites/${siteId}`;
    modal.classList.remove('hidden');
}

function closeEditSiteModal() {
    document.getElementById('editSiteModal').classList.add('hidden');
}

function confirmDeleteSite(siteId, siteName) {
    const modal = document.getElementById('deleteSiteModal');
    const message = document.getElementById('deleteSiteMessage');
    const form = document.getElementById('deleteSiteForm');
    
    message.textContent = `Are you sure you want to delete "${siteName}"? This action cannot be undone.`;
    form.action = `/admin/sites/${siteId}`;
    
    modal.classList.remove('hidden');
}

function closeDeleteSiteModal() {
    document.getElementById('deleteSiteModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addSiteModal');
    const viewModal = document.getElementById('viewSiteModal');
    const editModal = document.getElementById('editSiteModal');
    const deleteModal = document.getElementById('deleteSiteModal');
    
    if (event.target === addModal) closeAddSiteModal();
    if (event.target === viewModal) closeViewSiteModal();
    if (event.target === editModal) closeEditSiteModal();
    if (event.target === deleteModal) closeDeleteSiteModal();
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
    
    @if($errors->any())
        openAddSiteModal();
    @endif
});
</script>
@endsection
