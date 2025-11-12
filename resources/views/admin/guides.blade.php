@extends('layouts.admin')

@section('title', 'Guides Management - Velora Admin')
@section('page-title', 'Tour Guides Management')

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
            <h3 class="text-lg font-semibold text-gray-800">All Tour Guides</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.guides') }}" class="flex space-x-2">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Guides Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guide Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Languages</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($guides as $guide)
                <tr class="hover:bg-gray-50" data-guide-id="{{ $guide->id }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $guide->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-medium">{{ substr($guide->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900" data-name="{{ $guide->user->name }}">{{ $guide->user->name }}</div>
                                <div class="text-sm text-gray-500" data-email="{{ $guide->user->email }}">{{ $guide->user->email }}</div>
                                <div class="text-sm text-gray-500" data-phone="{{ $guide->phone }}">{{ $guide->phone }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1" data-languages="{{ $guide->languages }}">
                            @foreach(explode(',', $guide->languages) as $language)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ trim($language) }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" data-rate="{{ $guide->hourly_rate }}">
                        @if($guide->hourly_rate)
                            ${{ number_format($guide->hourly_rate, 2) }}/hour
                        @else
                            <span class="text-gray-400">Not set</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($guide->is_approved) bg-green-100 text-green-800
                            @else bg-yellow-100 text-yellow-800 @endif" data-approved="{{ $guide->is_approved }}">
                            {{ $guide->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $guide->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewGuide({{ $guide->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if(!$guide->is_approved)
                            <form method="POST" action="{{ route('admin.guides.approve', $guide->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif
                            <button onclick="confirmDeleteGuide({{ $guide->id }}, '{{ $guide->user->name }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-user-tie text-4xl mb-2 text-gray-300"></i>
                        <p>No guides found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($guides->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $guides->links() }}
    </div>
    @endif
</div>

<!-- View Guide Modal -->
<div id="viewGuideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Guide Details</h3>
                <button onclick="closeViewGuideModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewGuideContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteGuideModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete Guide</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteGuideMessage">
                    Are you sure you want to delete this guide?
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteGuideModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteGuideForm" method="POST" class="inline">
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
function viewGuide(guideId) {
    const modal = document.getElementById('viewGuideModal');
    const content = document.getElementById('viewGuideContent');
    const row = document.querySelector(`tr[data-guide-id="${guideId}"]`);
    
    const name = row.querySelector('[data-name]').getAttribute('data-name');
    const email = row.querySelector('[data-email]').getAttribute('data-email');
    const phone = row.querySelector('[data-phone]').getAttribute('data-phone');
    const languages = row.querySelector('[data-languages]').getAttribute('data-languages');
    const rate = row.querySelector('[data-rate]').getAttribute('data-rate');
    const approved = row.querySelector('[data-approved]').getAttribute('data-approved');
    const created = row.querySelectorAll('td')[5].textContent.trim();
    
    content.innerHTML = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Name</p>
            <p class="text-sm font-medium">${name}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Email</p>
            <p class="text-sm font-medium">${email}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Phone</p>
            <p class="text-sm font-medium">${phone}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Languages</p>
            <p class="text-sm font-medium">${languages}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Hourly Rate</p>
            <p class="text-sm font-medium">${parseFloat(rate).toFixed(2)}/hour</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-sm font-medium">${approved === '1' ? 'Approved' : 'Pending'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Joined Date</p>
            <p class="text-sm font-medium">${created}</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeViewGuideModal() {
    document.getElementById('viewGuideModal').classList.add('hidden');
}

function confirmDeleteGuide(guideId, guideName) {
    const modal = document.getElementById('deleteGuideModal');
    const message = document.getElementById('deleteGuideMessage');
    const form = document.getElementById('deleteGuideForm');
    
    message.textContent = `Are you sure you want to delete guide "${guideName}"? This action cannot be undone.`;
    form.action = `/admin/guides/${guideId}`;
    
    modal.classList.remove('hidden');
}

function closeDeleteGuideModal() {
    document.getElementById('deleteGuideModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewGuideModal');
    const deleteModal = document.getElementById('deleteGuideModal');
    
    if (event.target === viewModal) closeViewGuideModal();
    if (event.target === deleteModal) closeDeleteGuideModal();
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