@extends('layouts.admin')

@section('title', 'Users Management - Velora Admin')
@section('page-title', 'Users Management')

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
            <h3 class="text-lg font-semibold text-gray-800">All Users</h3>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.users') }}" class="flex space-x-2">
                    <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Users</option>
                        <option value="guide" {{ request('role') == 'guide' ? 'selected' : '' }}>Guides</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                    </select>
                </form>
                <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-medium">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900" data-name="{{ $user->name }}">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500" data-email="{{ $user->email }}">{{ $user->email }}</div>
                                <div class="text-sm text-gray-500" data-phone="{{ $user->phone ?? '' }}">
                                    @if(!empty($user->phone))
                                        Phone: {{ $user->phone }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($user->role === 'admin') bg-red-100 text-red-800
                            @elseif($user->role === 'guide') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif" data-role="{{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="inline-flex items-center" data-language="{{ $user->language }}">
                            @if($user->language === 'ar')
                                <span class="mr-1">🇵🇸</span> Arabic
                            @else
                                <span class="mr-1">🇺🇸</span> English
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <button onclick="viewUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editUser({{ $user->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <!-- زر تحويل إلى Guide -->
                            @if($user->role === 'user')
                            <button onclick="convertToGuide({{ $user->id }}, '{{ $user->name }}')" class="text-purple-600 hover:text-purple-900 transition-colors" title="Convert to Guide">
                                <i class="fas fa-user-tie"></i>
                            </button>
                            @endif
                            
                            @if($user->role !== 'admin')
                            <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- View User Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">User Details</h3>
                <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="viewModalContent" class="space-y-3">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-user-plus mr-2 text-green-600"></i>Add New User
                </h3>
                <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
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
            
            <form id="addForm" method="POST" action="{{ route('admin.users.create') }}">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Full Name *
                        </label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Enter full name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope mr-2"></i>Email Address *
                        </label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="user@example.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone mr-2"></i>Phone Number
                        </label>
                        <input type="text" name="phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="e.g. +970599000000">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock mr-2"></i>Password *
                        </label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Minimum 8 characters">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock mr-2"></i>Confirm Password *
                        </label>
                        <input type="password" name="password_confirmation" required minlength="8"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Re-enter password">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tag mr-2"></i>User Role *
                        </label>
                        <select name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="guide">Guide</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-language mr-2"></i>Preferred Language *
                        </label>
                        <select name="language" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Language</option>
                            <option value="en">🇺🇸 English</option>
                            <option value="ar">🇵🇸 Arabic</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-user-plus mr-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit User</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-2"></i>Name
                        </label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone mr-2"></i>Phone
                        </label>
                        <input type="text" name="phone" id="edit_phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tag mr-2"></i>Role
                        </label>
                        <select name="role" id="edit_role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            onchange="showGuideNotice(this.value)">
                            <option value="user">User</option>
                            <option value="guide">Guide</option>
                            <option value="admin">Admin</option>
                        </select>
                        
                        <!-- ملاحظة عند اختيار Guide -->
                        <div id="guideNotice" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                When you change role to "Guide", a guide profile will be created automatically and you will be redirected to Guides page.
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-language mr-2"></i>Language
                        </label>
                        <select name="language" id="edit_language" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="en">🇺🇸 English</option>
                            <option value="ar">🇵🇸 Arabic</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" 
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
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mt-4 text-center">Delete User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center" id="deleteMessage">
                    Are you sure you want to delete this user?
                </p>
            </div>
            <div class="flex items-center justify-center gap-4 px-4 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
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
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function viewUser(userId) {
    const modal = document.getElementById('viewModal');
    const content = document.getElementById('viewModalContent');
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    
    const name = row.querySelector('[data-name]').getAttribute('data-name');
    const email = row.querySelector('[data-email]').getAttribute('data-email');
    const phone = row.querySelector('[data-phone]')?.getAttribute('data-phone') || '';
    const role = row.querySelector('[data-role]').getAttribute('data-role');
    const language = row.querySelector('[data-language]').getAttribute('data-language');
    const joined = row.querySelectorAll('td')[4].textContent.trim();
    
    content.innerHTML = `
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Name</p>
            <p class="text-sm font-medium">${name}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Email</p>
            <p class="text-sm font-medium">${email}</p>
        </div>
        ${phone ? `<div class=\"border-b pb-3\"><p class=\"text-xs text-gray-500\">Phone</p><p class=\"text-sm font-medium\">${phone}</p></div>` : ''}
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Role</p>
            <p class="text-sm font-medium">${role.charAt(0).toUpperCase() + role.slice(1)}</p>
        </div>
        <div class="border-b pb-3">
            <p class="text-xs text-gray-500">Language</p>
            <p class="text-sm font-medium">${language === 'ar' ? '🇵🇸 Arabic' : '🇺🇸 English'}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Joined Date</p>
            <p class="text-sm font-medium">${joined}</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

function editUser(userId) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    
    const name = row.querySelector('[data-name]').getAttribute('data-name');
    const email = row.querySelector('[data-email]').getAttribute('data-email');
    const phone = row.querySelector('[data-phone]')?.getAttribute('data-phone') || '';
    const role = row.querySelector('[data-role]').getAttribute('data-role');
    const language = row.querySelector('[data-language]').getAttribute('data-language');
    
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_language').value = language;
    
    // إظهار الملاحظة إذا كان الـ role حالياً guide
    showGuideNotice(role);
    
    form.action = `/admin/users/${userId}`;
    modal.classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function showGuideNotice(role) {
    const notice = document.getElementById('guideNotice');
    if (role === 'guide') {
        notice.classList.remove('hidden');
    } else {
        notice.classList.add('hidden');
    }
}

function convertToGuide(userId, userName) {
    if (confirm(`Convert "${userName}" to a Tour Guide?`)) {
        // إنشاء form مخفي للتحويل
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        form.appendChild(methodField);
        
        const roleField = document.createElement('input');
        roleField.type = 'hidden';
        roleField.name = 'role';
        roleField.value = 'guide';
        form.appendChild(roleField);
        
        // نسخ باقي البيانات من الصف
        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
        const name = row.querySelector('[data-name]').getAttribute('data-name');
        const email = row.querySelector('[data-email]').getAttribute('data-email');
        const language = row.querySelector('[data-language]').getAttribute('data-language');
        
        const nameField = document.createElement('input');
        nameField.type = 'hidden';
        nameField.name = 'name';
        nameField.value = name;
        form.appendChild(nameField);
        
        const emailField = document.createElement('input');
        emailField.type = 'hidden';
        emailField.name = 'email';
        emailField.value = email;
        form.appendChild(emailField);
        
        const langField = document.createElement('input');
        langField.type = 'hidden';
        langField.name = 'language';
        langField.value = language;
        form.appendChild(langField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmDelete(userId, userName) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    const form = document.getElementById('deleteForm');
    
    message.textContent = `Are you sure you want to delete "${userName}"? This action cannot be undone.`;
    form.action = `/admin/users/${userId}`;
    
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');
    const viewModal = document.getElementById('viewModal');
    const editModal = document.getElementById('editModal');
    const addModal = document.getElementById('addModal');
    
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === viewModal) {
        closeViewModal();
    }
    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === addModal) {
        closeAddModal();
    }
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
    
    // Show add modal if there are validation errors
    @if($errors->any())
        openAddModal();
    @endif
});
</script>
@endsection
