<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Velora Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f8fafc;
            min-height: 100vh;
        }
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border-radius: 12px;
        }
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%) scaleY(0);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, #ffffff 0%, #e0f2fe 100%);
            border-radius: 0 4px 4px 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-item:hover {
            transform: translateX(6px);
            background: rgba(255, 255, 255, 0.12);
        }
        .nav-item:hover::before {
            transform: translateY(-50%) scaleY(1);
            height: 60%;
        }
        .nav-item.active {
            background: rgba(255, 255, 255, 0.18);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .nav-item.active::before {
            transform: translateY(-50%) scaleY(1);
            height: 70%;
        }
        .card-hover {
            transition: all 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .table-row-hover {
            transition: all 0.15s ease;
        }
        .table-row-hover:hover {
            background-color: #f9fafb;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        .slide-in {
            animation: slideInRight 0.4s ease-out;
        }
        .gradient-bg {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }
        .gradient-bg-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            transform: translateY(-2px);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge {
            position: relative;
            padding-left: 1.5rem;
        }
        .status-badge::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .status-upcoming::before {
            background: #3b82f6;
        }
        .status-ongoing::before {
            background: #10b981;
        }
        .status-completed::before {
            background: #6b7280;
        }
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .icon-wrapper:hover {
            transform: rotate(5deg) scale(1.1);
        }
        .modal-backdrop {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .table-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="gradient-bg text-white w-64 min-h-screen p-5 sidebar-transition shadow-lg">
            <!-- Logo -->
            <div class="flex items-center mb-8 pb-6 border-b border-white/20">
                <div class="flex items-center space-x-3">
                    <!-- Logo Image -->
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Velora Logo" class="w-12 h-12 object-contain">
                    @elseif(file_exists(public_path('images/logo.jpg')))
                        <img src="{{ asset('images/logo.jpg') }}" alt="Velora Logo" class="w-12 h-12 object-contain">
                    @elseif(file_exists(public_path('images/logo.svg')))
                        <img src="{{ asset('images/logo.svg') }}" alt="Velora Logo" class="w-12 h-12 object-contain">
                    @else
                        <!-- Fallback SVG Logo -->
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-md">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="#10b981" stroke="#10b981" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M2 17L12 22L22 17" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M2 12L12 17L22 12" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-tight">Velora</h1>
                        <p class="text-xs text-green-100">Admin Panel</p>
                    </div>
                </div>
            </div>
            
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>
                    <span class="font-medium">Users</span>
                </a>
                <a href="{{ route('admin.sites') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.sites*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt mr-3 w-5 text-center"></i>
                    <span class="font-medium">Sites</span>
                </a>
                <a href="{{ route('admin.guides') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.guides*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie mr-3 w-5 text-center"></i>
                    <span class="font-medium">Guides</span>
                </a>
                <a href="{{ route('admin.trips') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.trips*') ? 'active' : '' }}">
                    <i class="fas fa-route mr-3 w-5 text-center"></i>
                    <span class="font-medium">Trips</span>
                </a>
                <a href="{{ route('admin.reviews') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <i class="fas fa-star mr-3 w-5 text-center"></i>
                    <span class="font-medium">Reviews</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="nav-item flex items-center p-3 rounded-lg {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check mr-3 w-5 text-center"></i>
                    <span class="font-medium">Bookings</span>
                </a>
                
                <!-- Divider -->
                <div class="border-t border-white/20 my-4"></div>
                
                <!-- Logout Button -->
                <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="nav-item w-full flex items-center p-3 rounded-lg hover:bg-red-500/20 text-left">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="gradient-bg-header sticky top-0 z-10">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right hidden md:block">
                                <p class="text-sm text-gray-600">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mt-4 text-center">Logout Confirmation</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        Are you sure you want to logout from the admin panel?
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 px-4 py-3">
                    <button onclick="closeLogoutModal()" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button onclick="document.getElementById('logoutForm').submit()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
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