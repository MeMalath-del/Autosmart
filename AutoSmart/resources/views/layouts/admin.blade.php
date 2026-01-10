<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - لوحة الإدارة</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f1f5f9; }
        .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(135deg, #1e3a5f, #0f172a); position: fixed; right: 0; top: 0; z-index: 1000; }
        .sidebar-brand { padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 20px 0; }
        .sidebar-nav .nav-section { padding: 10px 20px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 1px; }
        .sidebar-nav a { display: flex; align-items: center; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: all 0.2s; border-right: 3px solid transparent; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.05); color: white; }
        .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: white; border-right-color: #3b82f6; }
        .sidebar-nav a i { margin-left: 10px; font-size: 1.1rem; width: 24px; }
        .main-content { margin-right: 260px; padding: 30px; }
        .top-bar { background: white; padding: 20px 30px; margin: -30px -30px 30px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .stat-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .stat-card.primary::before { background: #3b82f6; }
        .stat-card.success::before { background: #22c55e; }
        .stat-card.warning::before { background: #f59e0b; }
        .stat-card.danger::before { background: #ef4444; }
        .stat-card .icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .card { border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.05); border-radius: 15px; }
        .card-header { background: white; border-bottom: 1px solid #e2e8f0; border-radius: 15px 15px 0 0 !important; }
        @media (max-width: 992px) { .sidebar { transform: translateX(100%); } .main-content { margin-right: 0; } }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-4 fw-bold">
                <i class="bi bi-speedometer2 me-2"></i>AutoSmart
            </a>
            <div class="text-white-50 small mt-1">لوحة الإدارة</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">الرئيسية</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>لوحة التحكم
            </a>
            
            <div class="nav-section mt-3">المحتوى</div>
            <a href="{{ route('admin.stores.index') }}" class="{{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                <i class="bi bi-shop"></i>المتاجر
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-folder"></i>التصنيفات
            </a>
            <a href="{{ route('admin.car-brands.index') }}" class="{{ request()->routeIs('admin.car-brands.*') ? 'active' : '' }}">
                <i class="bi bi-car-front"></i>ماركات السيارات
            </a>
            
            <div class="nav-section mt-3">العمليات</div>
            <a href="{{ route('admin.workshops.index') }}" class="{{ request()->routeIs('admin.workshops.*') ? 'active' : '' }}">
                <i class="bi bi-wrench"></i>ورش الصيانة
            </a>
            <a href="{{ route('admin.refunds.index') }}" class="{{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-counterclockwise"></i>الاستردادات
            </a>
            <a href="{{ route('admin.withdrawals.index') }}" class="{{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>السحوبات
            </a>

            <div class="nav-section mt-3">المرحلة الرابعة</div>
            <a href="{{ route('admin.support.index') }}" class="{{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                <i class="bi bi-headset"></i>تذاكر الدعم
            </a>
            <a href="{{ route('admin.b2b.index') }}" class="{{ request()->routeIs('admin.b2b.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>حسابات الشركات
            </a>
            <a href="{{ route('admin.auctions.index') }}" class="{{ request()->routeIs('admin.auctions.*') ? 'active' : '' }}">
                <i class="bi bi-hammer"></i>المزادات
            </a>
            <a href="{{ route('admin.influencers.index') }}" class="{{ request()->routeIs('admin.influencers.*') ? 'active' : '' }}">
                <i class="bi bi-stars"></i>المؤثرين
            </a>
            <a href="{{ route('admin.gift-cards.index') }}" class="{{ request()->routeIs('admin.gift-cards.*') ? 'active' : '' }}">
                <i class="bi bi-gift"></i>بطاقات الهدايا
            </a>

            <div class="nav-section mt-3">التسويق</div>
            <a href="{{ route('admin.campaigns.index') }}" class="{{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i>حملات البريد
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                <i class="bi bi-ticket"></i>الكوبونات
            </a>
            <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                <i class="bi bi-image"></i>البانرات
            </a>
            <a href="{{ route('admin.flash-sales.index') }}" class="{{ request()->routeIs('admin.flash-sales.*') ? 'active' : '' }}">
                <i class="bi bi-lightning"></i>العروض السريعة
            </a>

            <div class="nav-section mt-3">التقارير</div>
            <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>التحليلات
            </a>
            
            <div class="nav-section mt-3">النظام</div>
            <a href="{{ route('home') }}">
                <i class="bi bi-globe"></i>الموقع الرئيسي
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>تسجيل الخروج
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h4 class="mb-0">@yield('title')</h4>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">مرحباً، {{ auth()->user()->name }}</span>
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width: 40px; height: 40px;">
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
