<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - لوحة تحكم البائع</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f8fafc; }
        .sidebar { width: 260px; min-height: 100vh; background: #1e293b; position: fixed; right: 0; top: 0; z-index: 1000; }
        .sidebar-brand { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-nav { padding: 20px 0; }
        .sidebar-nav a { display: flex; align-items: center; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: all 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-nav a i { margin-left: 10px; font-size: 1.2rem; }
        .main-content { margin-right: 260px; padding: 30px; }
        .top-bar { background: white; padding: 15px 30px; margin: -30px -30px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-card .icon { width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        @media (max-width: 992px) { .sidebar { transform: translateX(100%); } .main-content { margin-right: 0; } }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('seller.dashboard') }}" class="text-white text-decoration-none fs-5 fw-bold">
                <i class="bi bi-shop me-2"></i>{{ auth()->user()->store->name ?? 'لوحة البائع' }}
            </a>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>لوحة التحكم
            </a>
            <a href="{{ route('seller.products.index') }}" class="{{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>المنتجات
            </a>
            <a href="{{ route('seller.orders.index') }}" class="{{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                <i class="bi bi-bag"></i>الطلبات
            </a>
            <a href="{{ route('seller.store.edit') }}" class="{{ request()->routeIs('seller.store.edit') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>إعدادات المتجر
            </a>
            <hr class="border-secondary mx-3">
            <a href="{{ route('home') }}">
                <i class="bi bi-house"></i>الموقع الرئيسي
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>تسجيل الخروج
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title')</h4>
            <div class="d-flex align-items-center">
                <span class="me-3">{{ auth()->user()->name }}</span>
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width: 40px; height: 40px;">
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
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
