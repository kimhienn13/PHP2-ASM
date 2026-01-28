<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - TechMart Administrator</title>
    
    <!-- CSS: Bootstrap 5, Icons & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font chữ hiện đại -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d9488; /* Đồng bộ màu Teal với User */
            --primary-dark: #0f766e;
            --sidebar-bg: #0f172a; /* Màu tối sang trọng cho Sidebar */
            --sidebar-text: #94a3b8;
            --sidebar-active: #ffffff;
            --sidebar-active-bg: #0d9488;
            --body-bg: #f1f5f9;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--body-bg); 
            overflow-x: hidden;
        }

        /* --- SIDEBAR DESIGN --- */
        #wrapper { display: flex; width: 100%; align-items: stretch; }
        
        #sidebar-wrapper {
            min-width: 260px;
            max-width: 260px;
            background-color: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .sidebar-heading {
            padding: 1.5rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .list-group-item {
            background-color: transparent;
            color: var(--sidebar-text);
            border: none;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .list-group-item:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.05);
            transform: translateX(5px);
        }

        .list-group-item.active {
            color: var(--sidebar-active);
            background-color: var(--sidebar-active-bg);
            font-weight: 700;
            border-radius: 0 50px 50px 0; /* Bo tròn góc phải */
            margin-right: 15px; /* Thụt vào để hiện bo tròn */
        }
        
        .list-group-item.active:hover { transform: none; }

        /* --- MAIN CONTENT --- */
        #page-content-wrapper { width: 100%; display: flex; flex-direction: column; }
        
        .top-navbar {
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -260px; position: fixed; z-index: 1000; height: 100%; }
            #wrapper.toggled #sidebar-wrapper { margin-left: 0; }
            #sidebar-overlay { display: none; position: fixed; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
            #wrapper.toggled #sidebar-overlay { display: block; }
        }
        
        /* Utils */
        .btn-primary-custom { background: var(--primary-color); color: #fff; border: none; }
        .btn-primary-custom:hover { background: var(--primary-dark); color: #fff; }
        .text-primary-custom { color: var(--primary-color); }
        .card-dashboard { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); background: #fff; }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- 1. SIDEBAR -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">
            <i class="bi bi-shield-lock-fill"></i> TECHMART
        </div>
        <div class="list-group list-group-flush my-3">
            <div class="small text-uppercase fw-bold text-muted px-4 mb-2" style="font-size: 0.7rem;">Quản lý chung</div>
            
            <a href="{{ BASE_URL }}/adminproduct/index" class="list-group-item list-group-item-action {{ (strpos($_SERVER['REQUEST_URI'], 'adminproduct') !== false) ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Sản phẩm
            </a>
            <a href="{{ BASE_URL }}/admincategory/index" class="list-group-item list-group-item-action {{ (strpos($_SERVER['REQUEST_URI'], 'admincategory') !== false) ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Danh mục
            </a>
            <a href="{{ BASE_URL }}/adminbrand/index" class="list-group-item list-group-item-action {{ (strpos($_SERVER['REQUEST_URI'], 'adminbrand') !== false) ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Thương hiệu
            </a>
            
            <div class="small text-uppercase fw-bold text-muted px-4 mb-2 mt-4" style="font-size: 0.7rem;">Hệ thống</div>
            
            <a href="{{ BASE_URL }}/admincoupon/index" class="list-group-item list-group-item-action {{ (strpos($_SERVER['REQUEST_URI'], 'admincoupon') !== false) ? 'active' : '' }}">
                <i class="bi bi-ticket-perforated"></i> Mã giảm giá
            </a>
            <a href="{{ BASE_URL }}/adminuser/index" class="list-group-item list-group-item-action {{ (strpos($_SERVER['REQUEST_URI'], 'adminuser') !== false) ? 'active' : '' }}">
                <i class="bi bi-people"></i> Thành viên
            </a>
        </div>
        
        <!-- Sidebar Footer -->
        <div class="mt-auto p-4 border-top border-secondary border-opacity-25">
             <a href="{{ BASE_URL }}/product/index" target="_blank" class="btn btn-outline-light btn-sm w-100 rounded-pill">
                <i class="bi bi-box-arrow-up-right me-2"></i>Xem Website
             </a>
        </div>
    </div>
    
    <!-- Overlay for mobile -->
    <div id="sidebar-overlay"></div>

    <!-- 2. MAIN CONTENT WRAPPER -->
    <div id="page-content-wrapper">
        <!-- Top Navigation -->
        <nav class="top-navbar sticky-top">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light rounded-circle shadow-sm border" id="menu-toggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="m-0 fw-bold text-dark d-none d-md-block">{{ $title ?? 'Tổng quan' }}</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications (Mockup) -->
                <button class="btn btn-light rounded-circle position-relative border-0">
                    <i class="bi bi-bell fs-5 text-secondary"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </button>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                        <div class="bg-primary-custom text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                            {{ substr($_SESSION['user']['fullname'] ?? 'A', 0, 1) }}
                        </div>
                        <div class="d-none d-lg-block text-end lh-1">
                            <div class="fw-bold text-dark small">{{ $_SESSION['user']['fullname'] ?? 'Admin' }}</div>
                            <div class="text-muted" style="font-size: 10px;">Administrator</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 p-2 rounded-3">
                        <li><a class="dropdown-item rounded-2" href="#"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item rounded-2" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item rounded-2 text-danger fw-bold" href="{{ BASE_URL }}/adminauth/logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content Container (Closed in footer) -->
        <div class="container-fluid px-4 py-4">