@extends('layouts.admin')

@section('title', 'Guide Velora - Velora Admin')
@section('page-title', 'Guide Velora')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-book text-green-600 mr-2"></i>
            مرحباً بك في دليل Velora
        </h2>
        <p class="text-gray-600">هذا الدليل الشامل لاستخدام نظام Velora للسياحة</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">إجمالي المرشدين</p>
                    <p class="text-3xl font-bold">{{ $stats['total_guides'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">المرشدين المعتمدين</p>
                    <p class="text-3xl font-bold">{{ $stats['approved_guides'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm mb-1">بانتظار الاعتماد</p>
                    <p class="text-3xl font-bold">{{ $stats['pending_guides'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">الحجوزات الكلية</p>
                    <p class="text-3xl font-bold">{{ $stats['total_bookings'] }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">الحجوزات المكتملة</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_bookings'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-check-double text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">إجمالي التقييمات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_reviews'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">متوسط التقييم</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['average_rating'], 1) }}/5</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Sections -->
    <div class="space-y-6">
        <!-- Section 1: Getting Started -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                البدء مع Velora
            </h3>
            <p class="text-gray-600 mb-4">تعرف على الأساسيات وكيفية استخدام النظام بفعالية</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">كيفية إنشاء حساب مرشد سياحي جديد</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">إعداد الملف الشخصي والمعلومات الأساسية</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">فهم واجهة المستخدم والقوائم الرئيسية</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">إعداد المواقع السياحية وإضافة التفاصيل</span>
                </li>
            </ul>
        </div>

        <!-- Section 2: Managing Guides -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                إدارة المرشدين السياحيين
            </h3>
            <p class="text-gray-600 mb-4">كل ما تحتاج معرفته عن إدارة المرشدين</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">كيفية مراجعة طلبات المرشدين الجدد واعتمادهم</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تعديل معلومات المرشدين وأسعارهم الساعية</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">متابعة أداء المرشدين وتقييماتهم</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">إدارة اللغات والمهارات الخاصة بكل مرشد</span>
                </li>
            </ul>
        </div>

        <!-- Section 3: Bookings Management -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-purple-100 text-purple-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                إدارة الحجوزات
            </h3>
            <p class="text-gray-600 mb-4">تتبع وإدارة حجوزات المرشدين السياحيين</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">عرض جميع الحجوزات والفلترة حسب الحالة والتاريخ</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تأكيد أو إلغاء الحجوزات حسب الحاجة</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">التواصل مع العملاء والمرشدين لحل المشاكل</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-purple-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">مراقبة الحجوزات القادمة والمكتملة</span>
                </li>
            </ul>
        </div>

        <!-- Section 4: Sites and Trips -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-orange-100 text-orange-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                المواقع السياحية والرحلات
            </h3>
            <p class="text-gray-600 mb-4">إدارة المواقع السياحية وخطط الرحلات</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-orange-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">إضافة مواقع سياحية جديدة مع الإحداثيات والتفاصيل</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-orange-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تصنيف المواقع (تاريخية، طبيعية، ثقافية)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-orange-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">مراقبة خطط الرحلات التي ينشئها المستخدمون</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-orange-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تعديل أو حذف المواقع والرحلات عند الحاجة</span>
                </li>
            </ul>
        </div>

        <!-- Section 5: Reviews Management -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-yellow-100 text-yellow-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                إدارة التقييمات والمراجعات
            </h3>
            <p class="text-gray-600 mb-4">مراقبة جودة الخدمة من خلال التقييمات</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">عرض جميع التقييمات للمرشدين والمواقع</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">فلترة التقييمات حسب النوع والتقييم</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">حذف التقييمات غير المناسبة أو المخالفة</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-yellow-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تحليل متوسط التقييمات لتحسين الجودة</span>
                </li>
            </ul>
        </div>

        <!-- Section 6: Reports and Analytics -->
        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-red-100 text-red-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                التقارير والإحصائيات
            </h3>
            <p class="text-gray-600 mb-4">فهم أداء النظام من خلال التقارير التفصيلية</p>
            <ul class="space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">عرض إحصائيات شاملة للمرشدين والحجوزات</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تحليل الإيرادات والأداء المالي</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">متابعة نمو المستخدمين والمرشدين</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                    <span class="text-gray-700">تصدير البيانات والتقارير للتحليل الخارجي</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-link text-green-600 mr-2"></i>
            روابط سريعة للإدارة
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.guides') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-green-500 hover:shadow-md transition-all group">
                <i class="fas fa-user-tie text-green-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">إدارة المرشدين</p>
                    <p class="text-sm text-gray-500">عرض وإدارة جميع المرشدين</p>
                </div>
            </a>
            
            <a href="{{ route('admin.bookings') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-500 hover:shadow-md transition-all group">
                <i class="fas fa-calendar-check text-blue-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">الحجوزات</p>
                    <p class="text-sm text-gray-500">متابعة حجوزات المرشدين</p>
                </div>
            </a>
            
            <a href="{{ route('admin.reviews') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-yellow-500 hover:shadow-md transition-all group">
                <i class="fas fa-star text-yellow-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">التقييمات</p>
                    <p class="text-sm text-gray-500">مراجعة تقييمات المرشدين</p>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <a href="{{ route('admin.sites') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-purple-500 hover:shadow-md transition-all group">
                <i class="fas fa-map-marker-alt text-purple-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">المواقع السياحية</p>
                    <p class="text-sm text-gray-500">إدارة المواقع والأماكن</p>
                </div>
            </a>
            
            <a href="{{ route('admin.trips') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-orange-500 hover:shadow-md transition-all group">
                <i class="fas fa-route text-orange-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">الرحلات</p>
                    <p class="text-sm text-gray-500">خطط رحلات المستخدمين</p>
                </div>
            </a>
            
            <a href="{{ route('admin.users') }}" class="flex items-center p-4 bg-white rounded-lg border border-gray-200 hover:border-indigo-500 hover:shadow-md transition-all group">
                <i class="fas fa-users text-indigo-600 text-2xl mr-3 group-hover:scale-110 transition-transform"></i>
                <div>
                    <p class="font-semibold text-gray-800">المستخدمين</p>
                    <p class="text-sm text-gray-500">إدارة حسابات المستخدمين</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-blue-800 mb-3">
            <i class="fas fa-question-circle mr-2"></i>
            هل تحتاج المساعدة؟
        </h3>
        <p class="text-blue-700 mb-4">
            إذا كنت بحاجة إلى مساعدة إضافية أو لديك أي استفسارات حول استخدام نظام Velora، يمكنك التواصل مع فريق الدعم الفني.
        </p>
        <div class="flex flex-wrap gap-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-envelope mr-2"></i>
                تواصل مع الدعم
            </button>
            <button class="bg-white hover:bg-gray-50 text-blue-600 border border-blue-600 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-file-alt mr-2"></i>
                تحميل دليل المستخدم PDF
            </button>
            <button class="bg-white hover:bg-gray-50 text-blue-600 border border-blue-600 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-video mr-2"></i>
                شاهد فيديوهات تعليمية
            </button>
        </div>
    </div>
</div>
@endsection