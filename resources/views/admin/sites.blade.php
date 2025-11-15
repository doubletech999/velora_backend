@extends('layouts.admin')

@section('title', 'Sites Management - Velora Admin')
@section('page-title', 'Tourist Sites Management')

@section('content')

<script>
// Define function immediately at the top of the page
window.openAddSiteModal = function() {
    try {
        console.log('openAddSiteModal called');
        const modal = document.getElementById('addSiteModal');
        console.log('Modal element:', modal);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            modal.style.zIndex = '9999';
            document.body.style.overflow = 'hidden';
            console.log('Modal opened successfully');
        } else {
            console.error('Modal addSiteModal not found');
            alert('Modal not found. Please refresh the page.');
        }
    } catch (error) {
        console.error('Error opening modal:', error);
        alert('Error opening modal: ' + error.message);
    }
};

window.closeAddSiteModal = function() {
    try {
        const modal = document.getElementById('addSiteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    } catch (error) {
        console.error('Error closing modal:', error);
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
            <h3 class="text-xl font-semibold text-gray-900">Content Management</h3>
            <button type="button" id="addContentBtn" onclick="window.openAddSiteModal(); return false;" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Add New Content
            </button>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex items-center space-x-2 border-b border-gray-200 pb-2">
            <a href="{{ route('admin.sites') }}" 
               class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg {{ !request('type') ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-list mr-1"></i>All ({{ $counts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.sites', ['type' => 'hotel']) }}" 
               class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg {{ request('type') == 'hotel' ? 'border-b-2 border-orange-600 text-orange-600 bg-orange-50 font-semibold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-hotel mr-1"></i>Hotels ({{ $counts['hotel'] ?? 0 }})
                <span class="ml-1 text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded-full">Primary</span>
            </a>
            <a href="{{ route('admin.sites', ['type' => 'restaurant']) }}" 
               class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg {{ request('type') == 'restaurant' ? 'border-b-2 border-green-600 text-green-600 bg-green-50 font-semibold' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-utensils mr-1"></i>Restaurants ({{ $counts['restaurant'] ?? 0 }})
                <span class="ml-1 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Primary</span>
            </a>
            <a href="{{ route('admin.sites', ['type' => 'site']) }}" 
               class="px-4 py-2 text-sm font-medium transition-colors rounded-t-lg {{ request('type') == 'site' ? 'border-b-2 border-gray-600 text-gray-600 bg-gray-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas fa-map-marker-alt mr-1"></i>Tourist Sites ({{ $counts['site'] ?? 0 }})
                <span class="ml-1 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Secondary</span>
            </a>
        </div>
    </div>

    <!-- Sites Table -->
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
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
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($sites as $site)
                <tr class="table-row-hover {{ $site->type === 'hotel' ? 'bg-orange-50/50' : ($site->type === 'restaurant' ? 'bg-green-50/50' : '') }}" 
                    data-site-id="{{ $site->id }}"
                    data-name="{{ $site->name }}"
                    data-description="{{ $site->description }}"
                    data-type="{{ $site->type }}"
                    data-latitude="{{ $site->latitude }}"
                    data-longitude="{{ $site->longitude }}"
                    data-image-url="{{ $site->image_url ?? '' }}"
                    data-location="{{ $site->location ?? '' }}"
                    data-location-ar="{{ $site->location_ar ?? '' }}"
                    data-address="{{ $site->address ?? '' }}"
                    data-city="{{ $site->city ?? '' }}"
                    data-contact-phone="{{ $site->contact_phone ?? '' }}"
                    data-contact-email="{{ $site->contact_email ?? '' }}"
                    data-website="{{ $site->website ?? '' }}"
                    data-working-hours="{{ $site->working_hours ?? '' }}"
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
                        @if($site->type === 'hotel')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-orange-100 text-orange-800">
                                <i class="fas fa-hotel mr-1.5"></i>Hotel
                            </span>
                        @elseif($site->type === 'restaurant')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">
                                <i class="fas fa-utensils mr-1.5"></i>Restaurant
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md bg-gray-100 text-gray-700">
                                <i class="fas fa-map-marker-alt mr-1.5"></i>Site
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($site->latitude, 4) }}, {{ number_format($site->longitude, 4) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $site->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewSite({{ $site->id }})" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editSite({{ $site->id }})" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDeleteSite({{ $site->id }}, '{{ $site->name }}')" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-map-marker-alt text-6xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium text-gray-500 mb-2">No sites found</p>
                            <p class="text-sm text-gray-400 mb-4">Get started by adding your first site, hotel, or restaurant</p>
                            <button type="button" id="addContentBtnEmpty" onclick="window.openAddSiteModal(); return false;" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg transition-colors shadow-md hover:shadow-lg cursor-pointer">
                                <i class="fas fa-plus mr-2"></i>Add New Content
                            </button>
                        </div>
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
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>Add New Site
                </h3>
                <button type="button" onclick="window.closeAddSiteModal()" class="text-gray-500 hover:text-gray-700">
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
                            <i class="fas fa-list mr-2"></i>Content Type *
                        </label>
                        <select name="type" id="add-site-type" required
                            onchange="if(typeof window.toggleContactSection === 'function') { window.toggleContactSection(this, { clearOnHide: true }); } else { const type = this.value; const fields = document.querySelectorAll('.contact-fields'); if(type === 'hotel' || type === 'restaurant') { fields.forEach(f => { f.classList.remove('hidden'); f.style.display = 'block'; }); } else { fields.forEach(f => { f.classList.add('hidden'); f.style.display = 'none'; }); } }"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Type</option>
                            <option value="hotel" class="font-bold text-orange-600">🏨 Hotel (Primary)</option>
                            <option value="restaurant" class="font-bold text-green-600">🍽️ Restaurant (Primary)</option>
                            <option value="site" class="text-gray-600">📍 Tourist Site (Secondary)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <span class="font-semibold text-orange-600">Hotels</span> and <span class="font-semibold text-green-600">Restaurants</span> are primary content.
                            <span class="text-gray-600">Tourist Sites</span> are secondary.
                        </p>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-image mr-2"></i>Image URL
                        </label>
                        <input type="url" name="image_url"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <!-- Contact Fields (Required for Hotels & Restaurants only) -->
                    <div class="col-span-2 grid grid-cols-2 gap-4 contact-fields hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone mr-2"></i>Contact Phone <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="text" name="contact_phone" id="add_contact_phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="+970 599 000000">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-2"></i>Contact Email <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="email" name="contact_email" id="add_contact_email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="info@example.com">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clock mr-2"></i>Working Hours <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="text" name="working_hours" id="add_working_hours"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Sat-Thu 9:00 - 18:00">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="window.closeAddSiteModal()" 
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
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Site Details</h3>
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
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Site</h3>
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
                            <i class="fas fa-list mr-2"></i>Content Type *
                        </label>
                        <select name="type" id="edit_site_type" required
                            onchange="if(typeof window.toggleContactSection === 'function') { window.toggleContactSection(this, { clearOnHide: true }); } else { const type = this.value; const fields = document.querySelectorAll('.contact-fields'); if(type === 'hotel' || type === 'restaurant') { fields.forEach(f => { f.classList.remove('hidden'); f.style.display = 'block'; }); } else { fields.forEach(f => { f.classList.add('hidden'); f.style.display = 'none'; }); } }"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="hotel">🏨 Hotel (Primary)</option>
                            <option value="restaurant">🍽️ Restaurant (Primary)</option>
                            <option value="site">📍 Tourist Site (Secondary)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <span class="font-semibold text-orange-600">Hotels</span> and <span class="font-semibold text-green-600">Restaurants</span> are primary content.
                            <span class="text-gray-600">Tourist Sites</span> are secondary.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location (Short Description)
                        </label>
                        <input type="text" name="location" id="edit_site_location"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location (Arabic)
                        </label>
                        <input type="text" name="location_ar" id="edit_site_location_ar"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-list mr-2"></i>Type
                        </label>
                        <select name="type" id="edit_site_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="site">Tourist Site</option>
                            <option value="hotel">Hotel</option>
                            <option value="restaurant">Restaurant</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-image mr-2"></i>Image URL
                        </label>
                        <input type="url" name="image_url" id="edit_site_image_url"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <!-- Contact Fields (Required for Hotels & Restaurants only) -->
                    <div class="col-span-2 grid grid-cols-2 gap-4 contact-fields hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone mr-2"></i>Contact Phone <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="text" name="contact_phone" id="edit_contact_phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-2"></i>Contact Email <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="email" name="contact_email" id="edit_contact_email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clock mr-2"></i>Working Hours <span class="text-red-500 contact-required">*</span>
                            </label>
                            <input type="text" name="working_hours" id="edit_working_hours"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Sat-Thu 9:00 - 18:00">
                        </div>
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
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mt-4 text-center">Delete Site</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteSiteMessage">
                    Are you sure you want to delete this site?
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 px-4 py-3">
                <button onclick="closeDeleteSiteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form id="deleteSiteForm" method="POST" class="inline">
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
// Make function globally available immediately
(function() {
    'use strict';
    
    // Define function immediately
    window.openAddSiteModal = function() {
        try {
            console.log('openAddSiteModal called');
            const modal = document.getElementById('addSiteModal');
            console.log('Modal element:', modal);
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'block';
                modal.style.zIndex = '9999';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
                console.log('Modal opened successfully');
                
                // Initialize contact fields visibility based on selected type
                setTimeout(() => {
                    const addTypeSelect = document.getElementById('add-site-type');
                    if (addTypeSelect) {
                        if (typeof window.toggleContactSection === 'function') {
                            window.toggleContactSection(addTypeSelect, { clearOnHide: false });
                        } else {
                            // Fallback: manually show/hide based on selected value
                            const selectedType = addTypeSelect.value;
                            const contactFields = document.querySelectorAll('#addSiteModal .contact-fields');
                            if (selectedType === 'hotel' || selectedType === 'restaurant') {
                                contactFields.forEach(section => {
                                    section.classList.remove('hidden');
                                    section.style.display = 'block';
                                });
                            } else {
                                contactFields.forEach(section => {
                                    section.classList.add('hidden');
                                    section.style.display = 'none';
                                });
                            }
                        }
                    }
                }, 200);
            } else {
                console.error('Modal addSiteModal not found');
                alert('Modal not found. Please refresh the page.');
            }
        } catch (error) {
            console.error('Error opening modal:', error);
            alert('Error opening modal: ' + error.message);
        }
    };
    
    console.log('openAddSiteModal function defined');
})();

// Add event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, attaching event listeners...');
    
    // Button in header
    const addBtn = document.getElementById('addContentBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add button clicked (header)');
            window.openAddSiteModal();
        });
        console.log('Header add button listener attached');
    } else {
        console.error('Header add button not found!');
    }
    
    // Button in empty state
    const addBtnEmpty = document.getElementById('addContentBtnEmpty');
    if (addBtnEmpty) {
        addBtnEmpty.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Add button clicked (empty state)');
            window.openAddSiteModal();
        });
        console.log('Empty state add button listener attached');
    } else {
        console.log('Empty state button not found (this is OK if table has data)');
    }
    
    // Also keep onclick as fallback
    window.addEventListener('load', function() {
        const allAddButtons = document.querySelectorAll('[id^="addContentBtn"]');
        allAddButtons.forEach(btn => {
            if (!btn.onclick) {
                btn.setAttribute('onclick', 'window.openAddSiteModal(); return false;');
            }
        });
    });
    
    console.log('Event listeners attached. Add buttons ready.');
});

window.closeAddSiteModal = function() {
    try {
        const modal = document.getElementById('addSiteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Restore scrolling
        }
    } catch (error) {
        console.error('Error closing modal:', error);
    }
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
    const location = row.getAttribute('data-location') || '';
    const locationAr = row.getAttribute('data-location-ar') || '';
    const address = row.getAttribute('data-address') || '';
    const city = row.getAttribute('data-city') || '';
    const contactPhone = row.getAttribute('data-contact-phone') || '';
    const contactEmail = row.getAttribute('data-contact-email') || '';
    const website = row.getAttribute('data-website') || '';
    const workingHours = row.getAttribute('data-working-hours') || '';
    const imageUrl = row.getAttribute('data-image-url');
    const price = row.getAttribute('data-price');
    const guideName = row.getAttribute('data-guide-name');
    const distance = row.getAttribute('data-distance');
    const duration = row.getAttribute('data-duration');
    const activitiesJson = row.getAttribute('data-activities') || '[]';
    const activities = JSON.parse(activitiesJson);
    const created = row.querySelectorAll('td')[4].textContent.trim();
    
    let html = '';
    
    // Image display
    if (imageUrl) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500 mb-2">Image</p>
            <img src="${imageUrl}" alt="${name}" class="w-full h-48 object-cover rounded-lg border border-gray-300">
        </div>
        `;
    }
    
    html += `
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

    if (location) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Location Description</p>
            <p class="text-sm font-medium">${location}</p>
        </div>
        `;
    }

    if (locationAr) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">الموقع (بالعربية)</p>
            <p class="text-sm font-medium">${locationAr}</p>
        </div>
        `;
    }

    if (address) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Address</p>
            <p class="text-sm font-medium">${address}</p>
        </div>
        `;
    }

    if (city) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">City</p>
            <p class="text-sm font-medium">${city}</p>
        </div>
        `;
    }

    if (contactPhone) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Phone</p>
            <p class="text-sm font-medium">${contactPhone}</p>
        </div>
        `;
    }

    if (contactEmail) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Email</p>
            <p class="text-sm font-medium">${contactEmail}</p>
        </div>
        `;
    }

    if (website) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Website</p>
            <a href="${website}" target="_blank" class="text-sm font-medium text-green-600 hover:underline">${website}</a>
        </div>
        `;
    }

    if (workingHours) {
        html += `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Working Hours</p>
            <p class="text-sm font-medium">${workingHours}</p>
        </div>
        `;
    }
    
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
    document.getElementById('edit_site_location').value = row.getAttribute('data-location') || '';
    document.getElementById('edit_site_location_ar').value = row.getAttribute('data-location-ar') || '';
    document.getElementById('edit_site_address').value = row.getAttribute('data-address') || '';
    document.getElementById('edit_site_city').value = row.getAttribute('data-city') || '';
    document.getElementById('edit_site_contact_phone').value = row.getAttribute('data-contact-phone') || '';
    document.getElementById('edit_site_contact_email').value = row.getAttribute('data-contact-email') || '';
    document.getElementById('edit_site_website').value = row.getAttribute('data-website') || '';
    document.getElementById('edit_site_working_hours').value = row.getAttribute('data-working-hours') || '';
    document.getElementById('edit_site_price').value = row.getAttribute('data-price') || '';
    document.getElementById('edit_site_guide_id').value = row.getAttribute('data-guide-id') || '';
    document.getElementById('edit_site_guide_name').value = row.getAttribute('data-guide-name') || '';
    document.getElementById('edit_site_distance').value = row.getAttribute('data-distance') || '';
    document.getElementById('edit_site_duration').value = row.getAttribute('data-duration') || '';
    
    // Handle activities (JSON array) - checkboxes
    const activitiesJson = row.getAttribute('data-activities') || '[]';
    let activities = [];
    try {
        activities = JSON.parse(activitiesJson);
    } catch (e) {
        activities = [];
    }
    
    // Clear and set checkboxes
    const activityCheckboxes = form.querySelectorAll('input[name="activities[]"]');
    activityCheckboxes.forEach(checkbox => {
        checkbox.checked = activities.includes(checkbox.value);
    });
    
    form.action = `/admin/sites/${siteId}`;
    if (typeof window.toggleContactSection === 'function') {
        window.toggleContactSection(document.getElementById('edit_site_type'));
    }

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
document.addEventListener('click', function(event) {
    const addModal = document.getElementById('addSiteModal');
    const viewModal = document.getElementById('viewSiteModal');
    const editModal = document.getElementById('editSiteModal');
    const deleteModal = document.getElementById('deleteSiteModal');
    
    if (event.target === addModal) window.closeAddSiteModal();
    if (event.target === viewModal) closeViewSiteModal();
    if (event.target === editModal) closeEditSiteModal();
    if (event.target === deleteModal) closeDeleteSiteModal();
});

window.toggleContactSection = function(selectElement, { clearOnHide = false } = {}) {
    if (!selectElement) {
        console.log('toggleContactSection: selectElement is null');
        return;
    }
    const form = selectElement.closest('form');
    if (!form) {
        console.log('toggleContactSection: form not found');
        return;
    }
    
    const selectedType = selectElement.value;
    console.log('toggleContactSection: selectedType =', selectedType);
    const isPrimary = selectedType === 'hotel' || selectedType === 'restaurant';
    const isTouristSite = selectedType === 'site';
    
    // Toggle contact fields (for hotels & restaurants only)
    const contactSections = form.querySelectorAll('.contact-fields');
    console.log('toggleContactSection: found', contactSections.length, 'contact sections');
    
    contactSections.forEach(section => {
        if (isPrimary) {
            // Show contact fields for hotels and restaurants
            console.log('toggleContactSection: Showing contact fields for', selectedType);
            section.classList.remove('hidden');
            section.style.display = 'block';
            // Make required fields required
            section.querySelectorAll('.contact-required').forEach(span => {
                span.style.display = 'inline';
            });
            const phoneInput = section.querySelector('#add_contact_phone, #edit_contact_phone');
            const emailInput = section.querySelector('#add_contact_email, #edit_contact_email');
            const hoursInput = section.querySelector('#add_working_hours, #edit_working_hours');
            if (phoneInput) {
                phoneInput.required = true;
                phoneInput.disabled = false;
                console.log('toggleContactSection: Phone input enabled');
            }
            if (emailInput) {
                emailInput.required = true;
                emailInput.disabled = false;
                console.log('toggleContactSection: Email input enabled');
            }
            if (hoursInput) {
                hoursInput.required = true;
                hoursInput.disabled = false;
                console.log('toggleContactSection: Working hours input enabled');
            }
        } else {
            // Hide contact fields for tourist sites and other types
            console.log('toggleContactSection: Hiding contact fields for', selectedType);
            section.classList.add('hidden');
            section.style.display = 'none';
            // Remove required attribute and clear values
            section.querySelectorAll('input').forEach(input => {
                input.required = false;
                input.disabled = true;
                if (clearOnHide) input.value = '';
            });
        }
    });
    
    // Tourist sites don't need any additional fields - they only need name, description, image, and location
}

// Auto-hide success/error messages and initialize dynamic fields
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
        setTimeout(function() {
            openAddSiteModal();
        }, 100);
    @endif

    const addTypeSelect = document.getElementById('add-site-type');
    if (addTypeSelect) {
        if (typeof window.toggleContactSection === 'function') {
            window.toggleContactSection(addTypeSelect, { clearOnHide: true });
        }
        addTypeSelect.addEventListener('change', () => {
            if (typeof window.toggleContactSection === 'function') {
                window.toggleContactSection(addTypeSelect, { clearOnHide: true });
            }
        });
    }

    const editTypeSelect = document.getElementById('edit_site_type');
    if (editTypeSelect) {
        if (typeof window.toggleContactSection === 'function') {
            window.toggleContactSection(editTypeSelect);
        }
        editTypeSelect.addEventListener('change', () => {
            if (typeof window.toggleContactSection === 'function') {
                window.toggleContactSection(editTypeSelect, { clearOnHide: true });
            }
        });
    }
});
</script>
@endsection
