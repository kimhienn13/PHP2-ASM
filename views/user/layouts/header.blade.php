<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TechMart - Gia Dụng Thông Minh' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #009981; /* Màu xanh chủ đạo cho gia dụng */
            --primary-hover: #007a67;
            --secondary-color: #fd7e14; /* Màu cam cho nút mua hàng */
            --bg-body: #f3f4f6;
            --text-dark: #1f2937;
        }

        body { 
            font-family: 'Nunito Sans', sans-serif; 
            background-color: var(--bg-body); 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            color: var(--text-dark);
        }

        /* Navbar Design */
        .navbar-user { 
            background-color: #ffffff !important; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        
        .navbar-brand { 
            color: var(--primary-color) !important; 
            font-weight: 800; 
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        .nav-link { 
            color: #4b5563 !important; 
            font-weight: 700; 
            font-size: 0.95rem; 
            transition: 0.2s; 
            padding: 8px 18px !important; 
            text-transform: uppercase;
        }
        
        .nav-link:hover, .nav-link.active { 
            color: var(--primary-color) !important; 
        }

        /* User Profile & Buttons */
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: white;
        }

        .user-profile-btn { 
            background: #f1f5f9;
            color: var(--primary-color); 
            padding: 8px 20px; 
            border-radius: 50px; 
            font-weight: 700;
            border: 1px solid transparent;
            transition: 0.3s;
        }
        .user-profile-btn:hover { 
            background: #e2e8f0; 
            color: var(--primary-hover);
        }
        
        /* Dropdown Menu */
        .dropdown-menu { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            margin-top: 15px !important;
            padding: 10px;
            min-width: 240px;
        }
        .dropdown-item { 
            border-radius: 8px; 
            padding: 10px 15px; 
            font-weight: 600; 
            color: #4b5563;
        }
        .dropdown-item:hover { 
            background-color: #f0fdf4; 
            color: var(--primary-color); 
        }
        
        /* Badge Giỏ hàng */
        .cart-icon-wrapper {
            position: relative;
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .cart-icon-wrapper:hover {
            background: var(--primary-color);
            color: white !important;
        }
        .cart-icon-wrapper:hover i { color: white !important; }
        .cart-badge { 
            font-size: 0.7rem; 
            padding: 0.35em 0.6em; 
            top: -5px !important; 
            right: -5px !important;
            border: 2px solid #fff;
        }

        /* Tiện ích chung */
        .text-primary-custom { color: var(--primary-color) !important; }
        .bg-primary-custom { background-color: var(--primary-color) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-user sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ BASE_URL }}/">
            <div class="d-flex align-items-center justify-content-center bg-primary-custom text-white rounded-3" style="width: 40px; height: 40px;">
                <i class="bi bi-house-heart-fill fs-5"></i>
            </div>
            <span>TECHMART</span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-5">
                <li class="nav-item">
                    <a class="nav-link" href="{{ BASE_URL }}/product/index">SẢN PHẨM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ BASE_URL }}/coupon/index">KHUYẾN MÃI</a>
                </li>
                
                {{-- Hiển thị nút ADMIN nếu người dùng là admin --}}
                @if(isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin')
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ BASE_URL }}/adminproduct/index">
                            <i class="bi bi-shield-lock-fill me-1"></i>QUẢN TRỊ
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="cart-icon-wrapper text-dark" href="{{ BASE_URL }}/cart/index">
                        <i class="bi bi-handbag fs-5 text-secondary"></i>
                        @if(!empty($_SESSION['cart']))
                            <span class="position-absolute translate-middle badge rounded-pill bg-danger cart-badge">
                                {{ count($_SESSION['cart']) }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- KIỂM TRA TRẠNG THÁI ĐĂNG NHẬP --}}
                @if(isset($_SESSION['user']) && !empty($_SESSION['user']))
                    <li class="nav-item dropdown">
                        <button class="user-profile-btn dropdown-toggle d-flex align-items-center gap-2" 
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-lg-inline">{{ $_SESSION['user']['fullname'] }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0">
                            <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Tài khoản</div>
                                <div class="text-dark fw-bold small truncate">{{ $_SESSION['user']['email'] }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ BASE_URL }}/order/history">
                                    <i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ BASE_URL }}/profile/index">
                                    <i class="bi bi-gear me-2"></i>Cài đặt tài khoản
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="{{ BASE_URL }}/auth/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ BASE_URL }}/auth/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-custom rounded-pill px-4 fw-bold shadow-sm" href="{{ BASE_URL }}/auth/register">
                            Đăng ký
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 flex-grow-1">