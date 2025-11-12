@extends('layouts.admin')

@section('title', 'Reviews Management - Velora Admin')
@section('page-title', 'Reviews & Ratings Management')

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
            <h3 class="text-lg font-semibold text-gray-800">All Reviews & Ratings</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.reviews') }}" class="flex space-x-2">
                    <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="site" {{ request('type') == 'site' ? 'selected' : '' }}>Site Reviews</option>
                        <option value="guide" {{ request('type') == 'guide' ? 'selected' : '' }}>Guide Reviews</option>
                    </select>
                    <select name="rating" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50" data-review-id="{{ $review->id }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $review->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900" data-reviewer-name="{{ $review->user->name }}">{{ $review->user->name }}</div>
                                <div class="text-xs text-gray-500" data-reviewer-email="{{ $review->user->email }}">{{ $review->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($review->site)
                            <div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-1">
                                    Site Review
                                </span>
                                <div class="text-sm font-medium text-gray-900" data-target-name="{{ $review->site->name }}" data-target-type="site">{{ $review->site->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($review->site->type) }} Site</div>
                            </div>
                        @elseif($review->guide)
                            <div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mb-1">
                                    Guide Review
                                </span>
                                <div class="text-sm font-medium text-gray-900" data-target-name="{{ $review->guide->user->name }}" data-target-type="guide">{{ $review->guide->user->name }}</div>
                                <div class="text-xs text-gray-500">Tour Guide</div>
                            </div>
                        @else
                            <span class="text-gray-400">Unknown Target</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center" data-rating="{{ $review->rating }}">
                            <div class="flex items-center mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="fas fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $review->rating }}/5</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($review->comment)
                            <div class="text-sm text-gray-900 max-w-xs" data-comment="{{ $review->comment }}">
                                {{ Str::limit($review->comment, 100) }}
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">No comment</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $review->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-400">{{ $review->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewReview({{ $review->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View Full Review">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="confirmDeleteReview({{ $review->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-star text-4xl mb-2 text-gray-300"></i>
                        <p>No reviews found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $reviews->links() }}
    </div>
    @endif
</div>

<!-- Reviews Statistics -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    @for($rating = 5; $rating >= 1; $rating--)
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                @for($i = 1; $i <= $rating; $i++)
                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                @endfor
                @for($i = $rating + 1; $i <= 5; $i++)
                    <i class="fas fa-star text-gray-300 text-sm"></i>
                @endfor
            </div>
            <span class="text-lg font-bold text-gray-700">
                {{ $reviews->where('rating', $rating)->count() }}
            </span>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-200 rounded-full h-2">
                @php
                    $total = $reviews->count();
                    $count = $reviews->where('rating', $rating)->count();
                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                @endphp
                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>
    @endfor
</div>

<!-- Average Rating Card -->
<div class="mt-4 bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-center">
        <div class="text-center">
            <div class="text-4xl font-bold text-gray-800">
                @if($reviews->count() > 0)
                    {{ number_format($reviews->avg('rating'), 1) }}
                @else
                    0.0
                @endif
            </div>
            <div class="flex items-center justify-center mt-2">
                @php $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating')) : 0; @endphp
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $avgRating)
                        <i class="fas fa-star text-yellow-400 text-xl"></i>
                    @else
                        <i class="fas fa-star text-gray-300 text-xl"></i>
                    @endif
                @endfor
            </div>
            <div class="text-gray-600 mt-2">
                Average Rating from {{ $reviews->count() }} reviews
            </div>
        </div>
    </div>
</div>

<!-- View Review Modal -->
<div id="viewReviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Review Details</h3>
                <button onclick="closeViewReviewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewReviewContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteReviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete Review</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">
                    Are you sure you want to delete this review? This action cannot be undone.
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteReviewModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteReviewForm" method="POST" class="inline">
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
function viewReview(reviewId) {
    const modal = document.getElementById('viewReviewModal');
    const content = document.getElementById('viewReviewContent');
    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
    
    const reviewerName = row.querySelector('[data-reviewer-name]').getAttribute('data-reviewer-name');
    const reviewerEmail = row.querySelector('[data-reviewer-email]').getAttribute('data-reviewer-email');
    const targetName = row.querySelector('[data-target-name]').getAttribute('data-target-name');
    const targetType = row.querySelector('[data-target-type]').getAttribute('data-target-type');
    const rating = row.querySelector('[data-rating]').getAttribute('data-rating');
    const comment = row.querySelector('[data-comment]')?.getAttribute('data-comment') || 'No comment provided';
    const created = row.querySelectorAll('td')[5].textContent.trim();
    
    // Generate stars HTML
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= parseInt(rating)) {
            starsHtml += '<i class="fas fa-star text-yellow-400 text-lg"></i>';
        } else {
            starsHtml += '<i class="fas fa-star text-gray-300 text-lg"></i>';
        }
    }
    
    content.innerHTML = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Reviewer</p>
            <p class="text-sm font-medium">${reviewerName}</p>
            <p class="text-xs text-gray-400">${reviewerEmail}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Review For</p>
            <p class="text-sm font-medium">${targetName}</p>
            <p class="text-xs text-gray-400">${targetType === 'site' ? 'Tourist Site' : 'Tour Guide'}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Rating</p>
            <div class="flex items-center mt-1">
                ${starsHtml}
                <span class="ml-2 text-sm font-medium">${rating}/5</span>
            </div>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Comment</p>
            <p class="text-sm font-medium">${comment}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Review Date</p>
            <p class="text-sm font-medium">${created}</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeViewReviewModal() {
    document.getElementById('viewReviewModal').classList.add('hidden');
}

function confirmDeleteReview(reviewId) {
    const modal = document.getElementById('deleteReviewModal');
    const form = document.getElementById('deleteReviewForm');
    
    form.action = `/admin/reviews/${reviewId}`;
    modal.classList.remove('hidden');
}

function closeDeleteReviewModal() {
    document.getElementById('deleteReviewModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const viewModal = document.getElementById('viewReviewModal');
    const deleteModal = document.getElementById('deleteReviewModal');
    
    if (event.target === viewModal) closeViewReviewModal();
    if (event.target === deleteModal) closeDeleteReviewModal();
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