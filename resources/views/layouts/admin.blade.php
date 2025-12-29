<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - @yield('title')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #4a6572;
            margin-bottom: 20px;
        }
        
        .sidebar-nav .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar-nav .nav-link:hover {
            background-color: #34495e;
            color: white;
        }
        
        .sidebar-nav .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .sidebar-nav .nav-link i {
            width: 25px;
        }
        
        .main-content {
            margin-right: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .navbar-top {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
            margin-bottom: 20px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 2px solid #f0f0f0;
            font-weight: bold;
            padding: 15px 20px;
        }
        
        .table th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn-action {
            margin: 0 3px;
            font-size: 0.9em;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>
    <!-- الشريط الجانبي -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4 class="mb-0">
                <i class="fas fa-cogs"></i> لوحة التحكم
            </h4>
            <small class="text-muted">نظام إدارة المستخدمين</small>
        </div>
        
        <ul class="sidebar-nav nav flex-column">
          
            <li class="nav-item">
                <a href="{{ url('admin/users') }}" class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> جميع المستخدمين
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/users/pending') }}" class="nav-link {{ request()->is('admin/users/pending') ? 'active' : '' }}">
                    <i class="fas fa-user-clock"></i> المستخدمين المعلقين
                    @if($pendingCount ?? 0 > 0)
                        <span class="badge bg-danger float-start">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
          
            <li class="nav-item mt-4">
                <a href="{{ route('logout') }}" class="nav-link text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <!-- شريط التنقل العلوي -->
        <div class="navbar-top">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <button class="btn btn-outline-secondary d-md-none" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h5 class="mb-0">@yield('title')</h5>
                    </div>
                    <div class="col-md-6 text-md-start">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <div class="dropdown">
                                <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> الإعدادات</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- محتوى الصفحة -->
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // تبديل الشريط الجانبي في الهواتف
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // إظهار رسائل التأكيد قبل الحذف
        function confirmDelete(userId, userName) {
            if (confirm(`هل أنت متأكد من حذف المستخدم "${userName}"؟\nهذا الإجراء لا يمكن التراجع عنه.`)) {
                document.getElementById('delete-form-' + userId).submit();
            }
        }
        
        // إظهار تفاصيل المستخدم
        function showUserDetails(userId) {
            window.location.href = `/admin/users/${userId}`;
        }
    </script>
    
    @yield('scripts')
</body>
</html>