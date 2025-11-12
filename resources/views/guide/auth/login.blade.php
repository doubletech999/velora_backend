<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Login - Velora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-400 to-blue-600 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                <i class="fas fa-compass text-4xl text-blue-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Velora Guide Panel</h1>
            <p class="text-gray-600 mt-2">Tour Guide Login</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            @foreach($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <p class="text-sm">{{ session('error') }}</p>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('status') == 'pending')
        <div class="bg-orange-50 border border-orange-200 text-orange-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-clock mr-2"></i>
                <p class="text-sm font-bold">Account Pending Approval</p>
            </div>
            <p class="text-xs">Your guide account is under review.</p>
        </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ url('guide/login') }}">
            @csrf
            
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value
                    placeholder="Email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    autofocus
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2"></i>Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Password"
                    required
                >
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                    >
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>
        </form>

        <!-- Divider -->
        <div class="my-6 border-t border-gray-200"></div>

        <!-- Links -->
        <div class="text-center space-y-2">
            <p class="text-sm text-gray-600">
                Are you admin? 
                <a href="{{ route('admin.login') }}" class="text-green-600 hover:text-green-700 font-medium">
                    Admin Login
                </a>
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                &copy; 2025 Velora Tourism. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Auto-dismiss error messages
        setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>