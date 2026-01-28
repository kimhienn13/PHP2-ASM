@include('user.layouts.header')

<style>
    /* Global Styles cho giao diện Shop Gia Dụng */
    body {
        background-color: #fff7ed !important; /* Màu nền kem nhạt ấm cúng */
        background-image: radial-gradient(#ffedd5 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    .shop-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px -10px rgba(234, 88, 12, 0.15); /* Shadow màu cam nhạt */
    }

    .shop-header {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); /* Gradient Cam Đất */
        position: relative;
        overflow: hidden;
    }
    
    .shop-header::after {
        content: "";
        position: absolute;
        bottom: -20px;
        left: 0;
        width: 100%;
        height: 40px;
        background: white;
        border-radius: 50% 50% 0 0;
    }

    .form-control:focus {
        border-color: #fb923c;
        box-shadow: 0 0 0 0.25rem rgba(251, 146, 60, 0.25);
    }

    .btn-shop {
        background: linear-gradient(to right, #ea580c, #f97316);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-shop:hover {
        background: linear-gradient(to right, #c2410c, #ea580c);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(234, 88, 12, 0.4);
        color: white;
    }

    .text-shop {
        color: #ea580c !important;
    }
    
    /* Animation nhẹ nhàng */
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5 col-lg-4">
            <div class="card shop-card overflow-hidden bg-white">
                <!-- Header Đăng nhập: Tông màu Cam Ấm -->
                <div class="shop-header p-5 text-center text-white">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle text-shop" style="width: 80px; height: 80px;">
                            <i class="bi bi-shop fs-1"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">CHÀO MỪNG BẠN</h3>
                    <p class="small opacity-75 mb-0">Mua sắm tiện nghi cho tổ ấm</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- VÙNG HIỂN THỊ THÔNG BÁO (AJAX) -->
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 animate-slide-in rounded-3">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-2 fs-5"></i>
                            <span id="alert-message" class="fw-medium small"></span>
                        </div>
                    </div>

                    <!-- Form Đăng nhập -->
                    <form id="loginForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Email / Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-person"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="nhap-email@example.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-0">Mật khẩu</label>
                                <a href="{{ rtrim(BASE_URL, '/') }}/auth/forgot" class="text-shop text-decoration-none small fw-bold">Quên mật khẩu?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-shop w-100 rounded-pill py-3 fw-bold mb-4">
                            <span id="btnText"><i class="bi bi-box-arrow-in-right me-2"></i>ĐĂNG NHẬP NGAY</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center border-top pt-4">
                            <p class="text-muted small mb-0">
                                Bạn chưa là thành viên? 
                                <a href="{{ rtrim(BASE_URL, '/') }}/auth/register" class="text-shop fw-bold text-decoration-none">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// GIỮ NGUYÊN LOGIC JAVASCRIPT CŨ
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    btn.disabled = true;
    btnText.innerHTML = 'ĐANG XỬ LÝ...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ rtrim(BASE_URL, "/") }}/auth/postLogin', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success bg-success bg-opacity-10 text-success border-0 shadow-sm mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill';
            alertMsg.innerText = result.message || 'Đăng nhập thành công!';
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 800);
        } else {
            alertBox.className = 'alert alert-danger bg-danger bg-opacity-10 text-danger border-0 shadow-sm mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>XÁC NHẬN ĐĂNG NHẬP';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi:', error);
        alertBox.className = 'alert alert-danger bg-danger bg-opacity-10 text-danger border-0 shadow-sm mb-4 py-3 d-block';
        alertMsg.innerText = 'Lỗi kết nối máy chủ!';
        btn.disabled = false;
        btnText.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>XÁC NHẬN ĐĂNG NHẬP';
        btnLoader.classList.add('d-none');
    }
});
</script>

@include('user.layouts.footer')