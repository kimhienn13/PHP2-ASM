<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị - Cửa hàng Gia Dụng</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            /* Nền sáng, sạch sẽ phù hợp đồ gia dụng */
            background: linear-gradient(135deg, #fff1eb 0%, #ace0f9 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif; /* Font chữ thân thiện hơn */
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #ffffff;
        }
        .login-header {
            /* Màu cam/đỏ tạo cảm giác ấm cúng, khuyến mãi */
            background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
            padding: 40px 20px;
            text-align: center;
            color: #fff;
            position: relative;
        }
        /* Họa tiết trang trí nhẹ */
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background: #ffffff;
            border-radius: 20px 20px 0 0;
        }
        .login-header i { font-size: 3.5rem; margin-bottom: 10px; display: block; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); }
        
        .form-control {
            background-color: #f8f9fa;
            border: 2px solid #eee;
            color: #495057;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
            font-weight: 600;
        }
        .form-control:focus {
            background-color: #fff;
            color: #495057;
            box-shadow: none;
            border-color: #ff9966; /* Focus màu cam */
        }
        .form-control::placeholder { color: #adb5bd; font-weight: 400; }
        
        .input-group-text {
            background-color: #fff;
            border: 2px solid #eee;
            border-right: none;
            color: #ff9966;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .input-group .form-control:focus {
            border-left: none;
            border-color: #ff9966;
        }
        
        .btn-admin {
            background: linear-gradient(to right, #ff9966, #ff5e62);
            border: none;
            border-radius: 50px; /* Nút bo tròn mềm mại */
            padding: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
            box-shadow: 0 4px 15px rgba(255, 94, 98, 0.4);
        }
        .btn-admin:hover {
            background: linear-gradient(to right, #ff5e62, #ff9966);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 94, 98, 0.6);
            color: #fff;
        }
        .btn-admin:disabled { opacity: 0.7; transform: none; }
        
        .text-muted-custom { color: #6c757d !important; font-size: 0.85rem; }
        .forgot-link { color: #ff5e62; font-weight: 700; transition: 0.3s; }
        .forgot-link:hover { color: #d63031; text-decoration: underline !important; }

        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <!-- Icon cửa hàng/ngôi nhà thay vì khiên bảo mật -->
        <i class="bi bi-shop-window"></i>
        <h3 class="fw-bold mb-0">QUẢN TRỊ CỬA HÀNG</h3>
        <p class="small opacity-90 mt-1">Hệ thống quản lý đồ gia dụng</p>
    </div>

    <div class="card-body p-4 p-md-5 pt-4">
        <!-- Vùng thông báo lỗi/thành công qua AJAX -->
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3 animate-slide-in shadow-sm"></div>

        <form id="adminLoginForm">
            <div class="mb-4">
                <label class="form-label fw-bold text-muted-custom text-uppercase ms-1">Email Quản Lý</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="email" name="email" class="form-control shadow-none" placeholder="manager@giadung.vn" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between ms-1">
                    <label class="form-label fw-bold text-muted-custom text-uppercase">Mật khẩu</label>
                    <a href="{{ BASE_URL }}/adminauth/forgot" class="small text-decoration-none forgot-link">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-admin w-100 mb-4">
                Đăng Nhập Hệ Thống
            </button>
        </form>
    </div>

    <div class="card-footer border-0 pb-4 text-center bg-white">
        <a href="{{ BASE_URL }}/" class="text-secondary small text-decoration-none hover-highlight">
            <i class="bi bi-arrow-left-circle me-1"></i> Quay lại trang bán hàng
        </a>
    </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('auth-alert');
    const formData = new FormData(this);
    
    // Trạng thái chờ xử lý
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>ĐANG XÁC THỰC...';
    alertBox.classList.add('d-none');

    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postLogin', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Thành công: Hiển thị thông báo xanh
            alertBox.className = 'alert alert-success d-block border-0 bg-success text-white fw-bold';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 800);
        } else {
            // Thất bại: Hiển thị thông báo đỏ
            alertBox.className = 'alert alert-danger d-block border-0 bg-danger text-white animate-shake fw-bold';
            alertBox.innerText = result.message;
            
            btn.disabled = false;
            btn.innerText = 'ĐĂNG NHẬP HỆ THỐNG';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block border-0 bg-danger text-white fw-bold';
        alertBox.innerText = 'Lỗi kết nối hệ thống!';
        btn.disabled = false;
        btn.innerText = 'ĐĂNG NHẬP HỆ THỐNG';
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>