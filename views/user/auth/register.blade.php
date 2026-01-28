@include('user.layouts.header')

<style>
    /* Kế thừa style từ trang login cho đồng bộ */
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

    .shop-header-reg {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); /* Màu xanh lá tươi mới cho đăng ký */
        position: relative;
        overflow: hidden;
    }
    
    .shop-header-reg::after {
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
        border-color: #10b981;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }

    .btn-shop-reg {
        background: linear-gradient(to right, #10b981, #34d399);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-shop-reg:hover {
        background: linear-gradient(to right, #059669, #10b981);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .text-shop-reg { color: #059669 !important; }
    
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shop-card overflow-hidden bg-white">
                <!-- Header Đăng ký: Màu xanh lá tạo cảm giác tin cậy -->
                <div class="shop-header-reg p-5 text-center text-white">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle text-shop-reg" style="width: 70px; height: 70px;">
                            <i class="bi bi-person-plus fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1">TẠO TÀI KHOẢN MỚI</h3>
                    <p class="small opacity-75 mb-0">Trở thành thành viên thân thiết ngay</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <!-- VÙNG HIỂN THỊ THÔNG BÁO (AJAX) -->
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 rounded-3">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-2 fs-5"></i>
                            <span id="alert-message" class="fw-medium small"></span>
                        </div>
                    </div>

                    <!-- Form Đăng ký -->
                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Họ và Tên</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-card-heading"></i></span>
                                <input type="text" name="fullname" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="Ví dụ: Nguyễn Thị Lan" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Email liên hệ</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                       placeholder="email@example.com" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                           placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Nhập lại</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="bi bi-check2-circle"></i></span>
                                    <input type="password" name="confirm_password" class="form-control bg-light border-start-0 py-3 ps-2 shadow-none" 
                                           placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-shop-reg w-100 rounded-pill py-3 fw-bold mb-4">
                            <span id="btnText"><i class="bi bi-person-check me-2"></i>HOÀN TẤT ĐĂNG KÝ</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center border-top pt-4">
                            <p class="text-muted small mb-0">
                                Bạn đã có tài khoản rồi? 
                                <a href="{{ BASE_URL }}/auth/login" class="text-shop-reg fw-bold text-decoration-none">Đăng nhập ngay</a>
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
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    btn.disabled = true;
    btnText.innerHTML = 'ĐANG TẠO TÀI KHOẢN...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ rtrim(BASE_URL, "/") }}/auth/postRegister', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success bg-success bg-opacity-10 text-success border-0 shadow-sm mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill';
            alertMsg.innerText = result.message;
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1200);
        } else {
            alertBox.className = 'alert alert-danger bg-danger bg-opacity-10 text-danger border-0 shadow-sm mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-person-check me-2"></i>HOÀN TẤT ĐĂNG KÝ';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi:', error);
        alert('Có lỗi kết nối hệ thống, vui lòng thử lại sau!');
        btn.disabled = false;
        btnText.innerHTML = '<i class="bi bi-person-check me-2"></i>HOÀN TẤT ĐĂNG KÝ';
        btnLoader.classList.add('d-none');
    }
});
</script>

@include('user.layouts.footer')