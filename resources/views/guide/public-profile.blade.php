<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $guide->user->name ?? 'Guide' }} - Tour Guide Profile | Velora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-map-marked-alt text-3xl text-green-600 mr-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Velora Tourism</h1>
                </div>
                <a href="/guide/login" class="text-green-600 hover:text-green-700 font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Guide Login
                </a>
            </div>
        </div>
    </header>

    <div class="min-h-screen py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Back Button -->
            <div class="mb-6">
                <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-900 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-5xl font-bold">{{ substr($guide->user->name ?? 'G', 0, 1) }}</span>
                        </div>
                    </div>
                    
                    <!-- Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $guide->user->name ?? 'Tour Guide' }}</h1>
                        <p class="text-gray-600 mb-3">Professional Tour Guide</p>
                        
                        <!-- Rating -->
                        <div class="flex items-center justify-center md:justify-start mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-yellow-400 text-lg"></i>
                            @endfor
                            <span class="ml-2 text-lg font-semibold text-gray-700">{{ number_format($guide->rating ?? 5.0, 1) }}</span>
                            <span class="ml-2 text-gray-500">({{ $guide->reviews_count ?? 0 }} reviews)</span>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            @if(isset($guide->experience_years) && $guide->experience_years > 0)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                <span>{{ $guide->experience_years }} years experience</span>
                            </div>
                            @endif
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-language mr-2 text-green-600"></i>
                                <span>{{ $guide->languages ?? 'English' }}</span>
                            </div>
                            @if($guide->phone)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-phone mr-2 text-purple-600"></i>
                                <span>{{ $guide->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Contact Button -->
                    <div class="flex-shrink-0">
                        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>Contact Guide
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- About Section -->
                    @if($guide->bio)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i>About Me
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $guide->bio }}</p>
                    </div>
                    @endif
                    
                    <!-- Specializations -->
                    @if(isset($guide->specializations) && $guide->specializations)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-star mr-2 text-yellow-500"></i>Specializations
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $guide->specializations) as $spec)
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ trim($spec) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Certifications -->
                    @if(isset($guide->certifications) && $guide->certifications)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-certificate mr-2 text-green-600"></i>Certifications & Qualifications
                        </h2>
                        <div class="space-y-3">
                            @foreach(explode("\n", $guide->certifications) as $cert)
                                @if(trim($cert))
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                    <p class="text-gray-700">{{ trim($cert) }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Reviews Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-comments mr-2 text-purple-600"></i>Recent Reviews
                        </h2>
                        
                        @forelse($reviews as $review)
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-bold">{{ substr($review->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm">{{ $review->comment }}</p>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No reviews yet</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Stats Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Guide Statistics</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-check text-blue-600 w-8"></i>
                                    <span class="text-sm text-gray-600">Total Tours</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $guide->tours_count ?? 0 }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-500 w-8"></i>
                                    <span class="text-sm text-gray-600">Reviews</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $guide->reviews_count ?? 0 }}</span>
                            </div>
                            
                            @if(isset($guide->experience_years) && $guide->experience_years > 0)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-briefcase text-green-600 w-8"></i>
                                    <span class="text-sm text-gray-600">Experience</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $guide->experience_years }} years</span>
                            </div>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-purple-600 w-8"></i>
                                    <span class="text-sm text-gray-600">Member Since</span>
                                </div>
                                <span class="font-bold text-gray-900">{{ $guide->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Languages Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-language mr-2 text-green-600"></i>Languages
                        </h3>
                        <div class="space-y-2">
                            @foreach(explode(',', $guide->languages ?? 'English') as $lang)
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-700">{{ trim($lang) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Availability Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Availability</h3>
                        <button class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>Book Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2025 Velora Tourism. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>