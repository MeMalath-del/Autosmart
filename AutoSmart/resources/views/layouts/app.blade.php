<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AutoSmart') - سوق قطع غيار السيارات</title>
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--light-bg);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
        }
        
        .navbar-brand .text-warning {
            color: var(--accent-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .product-old-price {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 0.9rem;
        }
        
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #dc2626;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .category-card {
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            background: white;
            transition: all 0.3s;
        }
        
        .category-card:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .category-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            margin-bottom: 40px;
        }
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .footer {
            background: var(--dark-color);
            color: #94a3b8;
            padding: 60px 0 30px;
        }
        
        .footer a {
            color: #94a3b8;
            text-decoration: none;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .rating {
            color: var(--accent-color);
        }
        
        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .wishlist-btn:hover {
            background: #fee2e2;
        }
        
        .wishlist-btn.active {
            background: #dc2626;
            color: white;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc2626;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .store-card {
            text-align: center;
        }
        
        .store-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--primary-color);
        }
        
        .verified-badge {
            color: var(--primary-color);
            font-size: 1rem;
        }
        
        .condition-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 4px;
        }
        
        .condition-new { background: #dcfce7; color: #166534; }
        .condition-used { background: #fef3c7; color: #92400e; }
        .condition-refurbished { background: #dbeafe; color: #1e40af; }
        
        .toast-container {
            z-index: 9999;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }
            .hero-section h1 {
                font-size: 1.75rem;
            }
        }
    </style>
    
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-car-front-fill text-primary"></i>
                Auto<span class="text-warning">Smart</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">المنتجات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('stores.index') }}">المتاجر</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">من نحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('workshops.index') }}">ورش الصيانة</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">المزيد</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('auctions.index') }}"><i class="bi bi-hammer me-2"></i>المزادات</a></li>
                            <li><a class="dropdown-item" href="{{ route('community.index') }}"><i class="bi bi-people me-2"></i>المجتمع</a></li>
                            <li><a class="dropdown-item" href="{{ route('vin.search') }}"><i class="bi bi-upc-scan me-2"></i>البحث بالشاسيه</a></li>
                            <li><a class="dropdown-item" href="{{ route('gift-cards.index') }}"><i class="bi bi-gift me-2"></i>بطاقات الهدايا</a></li>
                            <li><a class="dropdown-item" href="{{ route('subscriptions.index') }}"><i class="bi bi-arrow-repeat me-2"></i>الاشتراكات</a></li>
                            <li><a class="dropdown-item" href="{{ route('bundles.index') }}"><i class="bi bi-box2-heart me-2"></i>الباقات</a></li>
                            <li><a class="dropdown-item" href="{{ route('compare') }}"><i class="bi bi-columns-gap me-2"></i>مقارنة المنتجات</a></li>
                            <li><a class="dropdown-item" href="{{ route('affiliate.index') }}"><i class="bi bi-share me-2"></i>برنامج الشركاء</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('scanner') }}"><i class="bi bi-upc-scan me-2"></i>مسح الباركود</a></li>
                            <li><a class="dropdown-item" href="{{ route('search.image') }}"><i class="bi bi-image me-2"></i>البحث بالصور</a></li>
                            <li><a class="dropdown-item" href="{{ route('forum.index') }}"><i class="bi bi-chat-dots me-2"></i>المنتدى</a></li>
                            <li><a class="dropdown-item" href="{{ route('blog.index') }}"><i class="bi bi-newspaper me-2"></i>المدونة</a></li>
                            <li><a class="dropdown-item" href="{{ route('installments.calculator') }}"><i class="bi bi-calculator me-2"></i>حاسبة التقسيط</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('support.faq') }}"><i class="bi bi-question-circle me-2"></i>الأسئلة الشائعة</a></li>
                            <li><a class="dropdown-item" href="{{ route('b2b.index') }}"><i class="bi bi-building me-2"></i>حسابات الشركات</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">اتصل بنا</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('compare') }}" title="المقارنة">
                            <i class="bi bi-columns-gap fs-5"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart') }}">
                            <i class="bi bi-cart3 fs-5"></i>
                            @livewire('shop.cart-icon')
                        </a>
                    </li>
                    
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">إنشاء حساب</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>لوحة الإدارة
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                
                                @if(auth()->user()->isSeller())
                                    <li><a class="dropdown-item" href="{{ route('seller.dashboard') }}">
                                        <i class="bi bi-shop me-2"></i>لوحة البائع
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag me-2"></i>طلباتي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlist') }}">
                                    <i class="bi bi-heart me-2"></i>المفضلة
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('part-requests.index') }}">
                                    <i class="bi bi-search me-2"></i>طلبات قطع الغيار
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('conversations.index') }}">
                                    <i class="bi bi-chat-dots me-2"></i>المحادثات
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.index') }}">
                                    <i class="bi bi-wallet2 me-2"></i>المحفظة
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('warranty.index') }}">
                                    <i class="bi bi-shield-check me-2"></i>الضمان
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('addresses.index') }}">
                                    <i class="bi bi-geo-alt me-2"></i>العناوين
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('garage.index') }}">
                                    <i class="bi bi-car-front me-2"></i>سياراتي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('lists.index') }}">
                                    <i class="bi bi-list-ul me-2"></i>قوائمي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('loyalty.index') }}">
                                    <i class="bi bi-stars me-2"></i>نقاط الولاء
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('referral.index') }}">
                                    <i class="bi bi-people me-2"></i>الإحالات
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('preorders.index') }}">
                                    <i class="bi bi-clock-history me-2"></i>الطلبات المسبقة
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('returns.index') }}">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>المرتجعات
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('parts.index') }}">
                                    <i class="bi bi-wrench me-2"></i>تتبع القطع
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('digital.downloads') }}">
                                    <i class="bi bi-cloud-download me-2"></i>التحميلات الرقمية
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('installation.bookings') }}">
                                    <i class="bi bi-tools me-2"></i>حجوزات التركيب
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('consultations.index') }}">
                                    <i class="bi bi-headset me-2"></i>الاستشارات الفنية
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('installments.index') }}">
                                    <i class="bi bi-credit-card-2-back me-2"></i>التقسيط
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person me-2"></i>حسابي
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Comparison Bar -->
    @livewire('shop.comparison-bar')
    
    <!-- Chatbot -->
    @livewire('shop.chatbot')
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white mb-3">
                        <i class="bi bi-car-front-fill text-primary"></i>
                        AutoSmart
                    </h5>
                    <p>سوق إلكتروني ذكي متخصص في بيع ووساطة وتوصيل قطع غيار السيارات.</p>
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#"><i class="bi bi-whatsapp fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white mb-3">روابط سريعة</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">الرئيسية</a></li>
                        <li class="mb-2"><a href="{{ route('products.index') }}">المنتجات</a></li>
                        <li class="mb-2"><a href="{{ route('stores.index') }}">المتاجر</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}">من نحن</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white mb-3">خدمة العملاء</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('contact') }}">اتصل بنا</a></li>
                        <li class="mb-2"><a href="#">الأسئلة الشائعة</a></li>
                        <li class="mb-2"><a href="#">سياسة الإرجاع</a></li>
                        <li class="mb-2"><a href="#">الشحن والتوصيل</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="text-white mb-3">تواصل معنا</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>الرياض، المملكة العربية السعودية</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>+966 50 000 0000</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>info@autosmart.sa</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} AutoSmart. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('terms') }}" class="me-3">الشروط والأحكام</a>
                    <a href="{{ route('privacy') }}">سياسة الخصوصية</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle">إشعار</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @livewireScripts
    
    <script>
        // Toast notification handler
        window.addEventListener('notify', event => {
            const toast = document.getElementById('liveToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastTitle = document.getElementById('toastTitle');
            
            toastMessage.textContent = event.detail.message || event.detail[0].message;
            
            const type = event.detail.type || event.detail[0].type;
            toast.className = 'toast';
            if (type === 'success') {
                toast.classList.add('bg-success', 'text-white');
                toastTitle.textContent = 'نجاح';
            } else if (type === 'error') {
                toast.classList.add('bg-danger', 'text-white');
                toastTitle.textContent = 'خطأ';
            } else if (type === 'warning') {
                toast.classList.add('bg-warning');
                toastTitle.textContent = 'تنبيه';
            }
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        });
    </script>
    
    @stack('scripts')
</body>
</html>
