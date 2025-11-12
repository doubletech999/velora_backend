@extends('layouts.guide')

@section('title', 'Reviews - Velora Guide')
@section('page-title', 'Customer Reviews')

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Average Rating -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Average Rating</p>
                <h3 class="text-3xl font-bold text-yellow-500">{{ number_format($averageRating, 1) }}</h3>
                <div class="flex items-center mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <i class="fas fa-star text-yellow-400"></i>
                        @elseif($i - $averageRating < 1)
                            <i class="fas fa-star-half-alt text-yellow-400"></i>
                        @else
                            <i class="far fa-star text-yellow-400"></i>
                        @endif
                    @endfor
                </div>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-2xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- Total Reviews -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">Total Reviews</p>
                <h3 class="text-3xl font-bold text-blue-600">{{ $totalReviews }}</h3>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-comments text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- 5 Star Reviews -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">5 Star Reviews</p>
                <h3 class="text-3xl font-bold text-green-600">{{ $fiveStarReviews }}</h3>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-award text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- This Month -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm mb-1">This Month</p>
                <h3 class="text-3xl font-bold text-purple-600">{{ $monthReviews }}</h3>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Rating Distribution -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-bar mr-2"></i>Rating Distribution
    </h3>
    
    <div class="space-y-3">
        @for($i = 5; $i >= 1; $i--)
        <div class="flex items-center">
            <span class="text-sm font-medium text-gray-600 w-12">{{ $i }} <i class="fas fa-star text-yellow-400 text-xs"></i></span>
            <div class="flex-1 mx-4">
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-yellow-400 h-3 rounded-full" style="width: {{ $ratingDistribution[$i] ?? 0 }}%"></div>
                </div>
            </div>
            <span class="text-sm font-medium text-gray-600 w-16 text-right">{{ $ratingCounts[$i] ?? 0 }} reviews</span>
        </div>
        @endfor
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('guide.reviews') }}">
        <div class="flex flex-wrap items-center gap-4">
            <select name="rating" id="ratingFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Ratings</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
            </select>

            <select name="sort" id="sortFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Highest Rating</option>
                <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Lowest Rating</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Reviews List -->
<div class="space-y-4">
    @forelse($reviews as $review)
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <span class="text-blue-600 font-bold text-lg">{{ substr($review->user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $review->site->name ?? 'Site Review' }}</p>
                </div>
            </div>
            
            <div class="text-right">
                <div class="flex items-center mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                    @endfor
                    <span class="ml-2 text-sm font-semibold text-gray-700">{{ $review->rating }}.0</span>
                </div>
                <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
            </div>
        </div>
        
        <p class="text-gray-700 leading-relaxed mb-3">{{ $review->comment }}</p>
        
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span><i class="far fa-calendar mr-1"></i>{{ $review->created_at->format('M d, Y') }}</span>
                @if($review->site)
                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($review->site->description ?? '', 30) }}</span>
                @endif
            </div>
            
            @if(isset($review->response) && $review->response)
            <button onclick="toggleResponse({{ $review->id }})" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                <i class="fas fa-reply mr-1"></i>View Response
            </button>
            @else
            <button onclick="openResponseModal({{ $review->id }})" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                <i class="fas fa-reply mr-1"></i>Respond
            </button>
            @endif
        </div>
        
        @if(isset($review->response) && $review->response)
        <div id="response-{{ $review->id }}" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
            <div class="flex items-start">
                <i class="fas fa-reply text-blue-600 mr-3 mt-1"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800 mb-1">Your Response:</p>
                    <p class="text-sm text-gray-700">{{ $review->response }}</p>
                    @if(isset($review->response_date))
                    <p class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($review->response_date)->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">No reviews yet</p>
        <p class="text-gray-400 text-sm mt-2">Reviews from your customers will appear here</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($reviews->hasPages())
<div class="mt-6">
    {{ $reviews->links() }}
</div>
@endif

<!-- Response Modal -->
<div id="responseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <i class="fas fa-reply mr-2 text-blue-600"></i>Respond to Review
            </h3>
            <button onclick="closeResponseModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="responseForm" method="POST">
            @csrf
            <textarea name="response" rows="4" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Write your response..."></textarea>
            
            <div class="flex items-center justify-end gap-3 mt-4">
                <button type="button" onclick="closeResponseModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-paper-plane mr-2"></i>Send Response
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleResponse(reviewId) {
    const responseDiv = document.getElementById(`response-${reviewId}`);
    responseDiv.classList.toggle('hidden');
}

function openResponseModal(reviewId) {
    const modal = document.getElementById('responseModal');
    const form = document.getElementById('responseForm');
    form.action = `/guide/reviews/${reviewId}/respond`;
    modal.classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('responseModal');
    if (event.target === modal) {
        closeResponseModal();
    }
}
</script>

@endsection