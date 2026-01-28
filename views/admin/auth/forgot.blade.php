<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu - Cửa hàng Gia Dụng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fff1eb 0%, #ace0f9 100%);
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Nunito', sans-serif; 
            margin: 0; 
        }
        .auth-card { 
            width: 100%; 
            max-width: 420px; 
            border-radius: 20px; 
            border: none;
            background: #ffffff; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .auth-header { 
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); /* Màu xanh lá tươi mới cho khôi phục */
            padding: 40px 20px; 
            text-align: center; 
            color: #fff; 
            position: relative;
        }
        .auth-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 20px;
            background: #ffffff;
            border-radius: 20px 20px 0 0;
        }
        .auth-header i { font-size: 3rem; margin-bottom: 10px; display: block; }
        
        .form-control { 
            background-color: #f8f9fa; 
            border: 2px solid #eee; 
            color: #495057; 
            border-radius: 10px; 
            padding: 12px; 
            transition: 0.3s; 
            font-weight: 600;
        }
        .form-control:focus { 
            background-color: #fff; 
            border-color: #38ef7d; 
            color: #495057; 
            box-shadow: none;
        }
        
        .btn-reset { 
            background: linear-gradient(to right, #11998e, #38ef7d);
            border: none; 
            border-radius: 50px; 
            padding: 12px; 
            font-weight: 800; 
            color: #fff; 
            transition: 0.3s; 
            text-transform: uppercase; 
            box-shadow: 0 4px 15px rgba(56, 239, 125, 0.4);
        }
        .btn-reset:hover { 
            background: linear-gradient(to right, #38ef7d, #11998e);
            transform: translateY(-2px); 
            color: #fff;
        }
        .btn-reset:disabled { opacity: 0.7; transform: none; }
        
        .text-muted-custom { color: #6c757d !important; font-size: 0.85rem; }
        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body>

<div class="auth-card shadow-lg">
    <div class="auth-header">
        <i class="bi bi-shield-check"></i>
        <h3 class="fw-bold mb-0">KHÔI PHỤC TÀI KHOẢN</h3>
        <p class="small opacity-90 mt-1">Cấp lại mật khẩu quản lý</p>
    </div>

    <div class="card-body p-4 p-md-5 pt-4">
        <!-- Vùng thông báo lỗi/thành công -->
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3 shadow-sm"></div>

        <form id="forgotAdminForm">
            <div class="mb-3">
                <label class="form-label text-muted-custom fw-bold text-uppercase ms-1">Email quản trị viên</label>
                <div class="input-group">
                     <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #eee;"><i class="bi bi-envelope text-success"></i></span>
                     <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="admin@giadung.vn" required style="border-radius: 0 10px 10px 0;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted-custom fw-bold text-uppercase ms-1">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted-custom fw-bold text-uppercase ms-1">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-reset w-100 shadow-sm mb-4">
                Xác nhận thay đổi
            </button>

            <div class="text-center">
                <a href="{{ BASE_URL }}/adminauth/login" class="text-decoration-none small fw-bold text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('forgotAdminForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('auth-alert');
    const formData = new FormData(this);
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>ĐANG XỬ LÝ...';
    alertBox.classList.add('d-none');

    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postForgot', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success d-block border-0 bg-success text-white fw-bold';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 1500);
        } else {
            alertBox.className = 'alert alert-danger d-block border-0 bg-danger text-white animate-shake fw-bold';
            alertBox.innerText = result.message;
            btn.disabled = false;
            btn.innerText = 'XÁC NHẬN THAY ĐỔI';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block border-0 bg-danger text-white fw-bold';
        alertBox.innerText = 'Lỗi kết nối máy chủ!';
        btn.disabled = false;
        btn.innerText = 'XÁC NHẬN THAY ĐỔI';
    }
});
</script>

</body>
</html>