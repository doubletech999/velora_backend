<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Velora Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-green-800 text-white w-64 min-h-screen p-4 sidebar-transition">
            <div class="flex items-center mb-8">
                <i class="fas fa-map-marked-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-bold">Velora Admin</h1>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Users
                </a>
                <a href="{{ route('admin.sites') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.sites*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-map-marker-alt mr-3"></i>
                    Sites
                </a>
                <a href="{{ route('admin.guides') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.guides*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-user-tie mr-3"></i>
                    Guides
                </a>
                <a href="{{ route('admin.trips') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.trips*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-route mr-3"></i>
                    Trips
                </a>
                <a href="{{ route('admin.reviews') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.reviews*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-star mr-3"></i>
                    Reviews
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center p-3 rounded-lg hover:bg-green-700 transition-colors {{ request()->routeIs('admin.bookings*') ? 'bg-green-700' : '' }}">
                    <i class="fas fa-calendar-check mr-3"></i>
                    Bookings
                </a>
                
                
                <!-- Divider -->
                <div class="border-t border-green-700 my-4"></div>
                
                <!-- Logout Button -->
                <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="w-full flex items-center p-3 rounded-lg hover:bg-red-600 transition-colors text-left">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Welcome, <strong>{{ Auth::user()->name }}</strong></span>
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Logout Confirmation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        Are you sure you want to logout from the admin panel?
                    </p>
                </div>
                <div class="flex items-center justify-center gap-4 px-4 py-3">
                    <button onclick="closeLogoutModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button onclick="document.getElementById('logoutForm').submit()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>
</body>
</html>