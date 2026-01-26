<?php

/**
 * AuthController xử lý các tác vụ xác thực người dùng (Đăng nhập, Đăng ký, Quên mật khẩu, Đăng xuất)
 * Phiên bản tích hợp AJAX: Trả về JSON để hiển thị thông báo mà không load lại trang.
 */
class AuthController extends Controller {
    
    // ==========================================
    // 1. ĐĂNG NHẬP (LOGIN)
    // ==========================================

    /**
     * Hiển thị trang đăng nhập
     */
    public function login() {
        $this->view('user.auth.login', ['title' => 'Đăng nhập hệ thống']);
    }

    /**
     * Xử lý dữ liệu đăng nhập từ Form (AJAX)
     */
    public function postLogin() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ email và mật khẩu!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                if ($user) {
                    $isPasswordCorrect = false;
                    // Kiểm tra mật khẩu (hỗ trợ cả hash và chuỗi thuần để dev)
                    if (password_verify($password, $user['password'])) {
                        $isPasswordCorrect = true;
                    } elseif ($password === $user['password']) {
                        $isPasswordCorrect = true;
                    }

                    if ($isPasswordCorrect) {
                        $userRole = strtolower(trim($user['role'] ?? 'user'));

                        $_SESSION['user'] = [
                            'id'       => $user['id'],
                            'fullname' => $user['fullname'],
                            'email'    => $user['email'],
                            'role'     => $userRole
                        ];
                        
                        session_write_close();
                        
                        // Xác định trang đích dựa trên quyền hạn
                        $target = ($userRole === 'admin') ? 'adminproduct/index' : 'product/index';
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Đăng nhập thành công!',
                            'redirect' => BASE_URL . '/' . $target
                        ]);
                        return;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Mật khẩu cung cấp không chính xác!']);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại trên hệ thống!']);
                    return;
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
                return;
            }
        }
    }

    // ==========================================
    // 2. ĐĂNG KÝ (REGISTER)
    // ==========================================

    /**
     * Hiển thị trang đăng ký
     */
    public function register() {
        $this->view('user.auth.register', ['title' => 'Đăng ký tài khoản']);
    }

    /**
     * Xử lý dữ liệu đăng ký thành viên mới (AJAX)
     */
    public function postRegister() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($fullname) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc!']);
                return;
            }

            if ($password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không trùng khớp!']);
                return;
            }

            try {
                $userModel = $this->model('User');
                // Kiểm tra email đã tồn tại hay chưa
                if ($userModel->exists($email)) {
                    echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng cho một tài khoản khác!']);
                    return;
                }

                // Tiến hành tạo tài khoản mới (Model User sẽ lo phần hash mật khẩu)
                $result = $userModel->create([
                    'fullname' => $fullname,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => 'user'
                ]);

                if ($result) {
                    session_write_close();
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Đăng ký thành công! Hãy đăng nhập ngay.',
                        'redirect' => BASE_URL . '/auth/login'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra trong quá trình đăng ký!']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
        }
    }

    // ==========================================
    // 3. QUÊN MẬT KHẨU (FORGOT PASSWORD)
    // ==========================================

    /**
     * Hiển thị trang quên mật khẩu
     */
    public function forgot() {
        $this->view('user.auth.forgot', ['title' => 'Khôi phục mật khẩu']);
    }

    /**
     * Xử lý đặt lại mật khẩu trực tiếp (AJAX)
     */
    public function postForgot() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($email) || empty($password) || empty($confirm_password)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ các trường thông tin!']);
                return;
            }

            $userModel = $this->model('User');

            // Kiểm tra tài khoản có tồn tại không
            if (!$userModel->exists($email)) {
                echo json_encode(['success' => false, 'message' => 'Email này không tồn tại trên hệ thống!']);
                return;
            }

            // Kiểm tra mật khẩu khớp nhau
            if ($password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Xác nhận mật khẩu mới không khớp!']);
                return;
            }

            try {
                // Cập nhật mật khẩu mới (Model sẽ tự động mã hóa mật khẩu)
                $userModel->updatePassword($email, $password);
                session_write_close();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Đổi mật khẩu thành công! Hãy đăng nhập lại bằng mật khẩu mới.',
                    'redirect' => BASE_URL . '/auth/login'
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật mật khẩu!']);
            }
        }
    }

    // ==========================================
    // 4. ĐĂNG XUẤT (LOGOUT)
    // ==========================================

    /**
     * Xử lý đăng xuất người dùng
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Xóa sạch dữ liệu phiên
        $_SESSION = [];
        session_destroy();
        
        // Đá ra trang sản phẩm
        $this->redirect('product/index');
    }
}