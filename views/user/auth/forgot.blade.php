@include('user.layouts.header')

<style>
    /* Style đồng bộ */
    body {
        background-color: #fff7ed !important;
        background-image: radial-gradient(#ffedd5 1px, transparent 1px);
        background-size: 20px 20px;
    }
    
    .shop-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px -10px rgba(234, 88, 12, 0.15);
    }

    .shop-header-forgot {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); /* Màu Vàng Cam Cảnh Báo */
        position: relative;
        overflow: hidden;
    }
    
    .shop-header-forgot::after {
        content: "";
        position: absolute;
        bottom: -20px;
        left: 0;
        width: 100%;
        height: 40px;
        background: white;
        border-radius: 50% 50% 0 0;
    }

    .btn-shop-forgot {
        background: linear-gradient(to right, #f59e0b, #fbbf24);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-shop-forgot:hover {
        background: linear-gradient(to right, #d97706, #f59e0b);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        color: white;
    }
    
    .text-shop-forgot { color: #d97706 !important; }

    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5 col-lg-4">
            <!-- Auth Card -->
            <div class="card shop-card overflow-hidden bg-white">
                
                <!-- Auth Header -->
                <div class="shop-header-forgot p-5 text-center text-white">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle text-shop-forgot" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">CẬP NHẬT MẬT KHẨU</h3>
                    <p class="small opacity-75 mb-0">Thiết lập lại bảo mật cho tài khoản</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- VÙNG HIỂN THỊ THÔNG BÁO (AJAX) -->
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 rounded-3">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-2 fs-5"></i>
                            <span id="alert-message" class="fw-medium small"></span>
                        </div>
                    </div>

                    <!-- Form gửi đến AuthController@postForgot -->
                    <form id="forgotForm">
                        <!-- Nhập Email xác nhận -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Email đã đăng ký</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-envelope-at"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="email@example.com" required>
                            </div>
                        </div>

                        <!-- Nhập Mật khẩu mới -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu mới</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="Tối thiểu 6 ký tự..." required>
                            </div>
                        </div>

                        <!-- Xác nhận Mật khẩu mới -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Xác nhận lại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-check-all"></i></span>
                                <input type="password" name="confirm_password" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="Nhập lại mật khẩu..." required>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-shop-forgot w-100 rounded-pill py-3 fw-bold mb-4">
                            <span id="btnText"><i class="bi bi-arrow-repeat me-2"></i>XÁC NHẬN ĐỔI MẬT KHẨU</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center border-top pt-4">
                            <a href="{{ BASE_URL }}/auth/login" class="text-shop-forgot text-decoration-none small fw-bold">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// GIỮ NGUYÊN LOGIC JAVASCRIPT CŨ
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    // Trạng thái Loading
    btn.disabled = true;
    btnText.innerHTML = 'ĐANG CẬP NHẬT...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        // Gửi đến phương thức postForgot trong AuthController
        const response = await fetch('{{ BASE_URL }}/auth/postForgot', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success bg-success bg-opacity-10 text-success border-0 shadow-sm mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill';
            alertMsg.innerText = result.message;
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            alertBox.className = 'alert alert-danger bg-danger bg-opacity-10 text-danger border-0 shadow-sm mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>XÁC NHẬN ĐỔI MẬT KHẨU';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi chi tiết:', error);
        alertBox.className = 'alert alert-danger bg-danger bg-opacity-10 text-danger border-0 shadow-sm mb-4 py-3 d-block';
        alertMsg.innerText = 'Lỗi xử lý dữ liệu từ máy chủ!';
        btn.disabled = false;
        btnText.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>XÁC NHẬN ĐỔI MẬT KHẨU';
        btnLoader.classList.add('d-none');
    }
});
</script>

@include('user.layouts.footer')