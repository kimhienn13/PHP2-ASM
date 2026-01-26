<?php

/**
 * AdminUserController - Quản lý thành viên hệ thống TechMart
 * Đảm bảo an toàn dữ liệu và phân quyền chính xác.
 */
class AdminUserController extends AdminController {
    
    /**
     * Hiển thị danh sách thành viên kèm phân trang và tìm kiếm
     */
    public function index() {
        $userModel = $this->model('User');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 10; 

        // Lấy dữ liệu từ Model User
        $result = $userModel->list($page, $limit, $search);

        // Trả về giao diện index của quản trị viên
        $this->view('admin.user.index', [
            'title'       => 'Quản lý Thành viên',
            'users'       => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    /**
     * Xử lý thêm thành viên mới (Hàm Store)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            $userModel = $this->model('User');

            // 1. Kiểm tra rỗng
            if (empty($fullname) || empty($email) || empty($password)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
                $this->handleError('add', $_POST);
                return;
            }

            // 2. Kiểm tra định dạng email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Định dạng email không hợp lệ!";
                $this->handleError('add', $_POST);
                return;
            }

            // 3. Kiểm tra email đã tồn tại
            if ($userModel->exists($email)) {
                $_SESSION['error'] = "Địa chỉ email '$email' đã được sử dụng!";
                $this->handleError('add', $_POST);
                return;
            }

            try {
                // 4. Lưu thông tin (Model sẽ tự động mã hóa mật khẩu)
                $userModel->create([
                    'fullname' => $fullname,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => $role
                ]);

                $_SESSION['success'] = "Đã thêm thành viên '$fullname' thành công!";
                session_write_close();
                $this->redirect('adminuser/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    /**
     * Xử lý cập nhật thông tin thành viên (Hàm Update)
     * CẬP NHẬT: Không xử lý mật khẩu theo yêu cầu bảo mật.
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userModel = $this->model('User');
            $currentUser = $userModel->show($id);
            
            if (!$currentUser) {
                $_SESSION['error'] = "Thành viên không tồn tại!";
                $this->redirect('adminuser/index');
                return;
            }

            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'user';

            // Kiểm tra rỗng
            if (empty($fullname) || empty($email)) {
                $_SESSION['error'] = "Họ tên và Email không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            // Kiểm tra trùng email (trừ chính user đang sửa)
            if ($userModel->exists($email, $id)) {
                $_SESSION['error'] = "Email này đã thuộc về người dùng khác!";
                $this->handleError('edit', $_POST);
                return;
            }

            try {
                // Cập nhật thông tin cơ bản
                $userModel->update($id, [
                    'fullname' => $fullname,
                    'email'    => $email,
                    'role'     => $role
                ]);

                $_SESSION['success'] = "Đã cập nhật thông tin thành công!";
                session_write_close();
                $this->redirect('adminuser/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi lưu dữ liệu!";
                $this->handleError('edit', $_POST);
            }
        }
    }

    /**
     * Xử lý xóa thành viên (Hàm Destroy)
     * CẬP NHẬT: Ngăn chặn xóa Admin ở tầng Server.
     */
    public function destroy($id) {
        $userModel = $this->model('User');
        $user = $userModel->show($id);

        /**
         * CHẶN XÓA ADMIN (BACKEND PROTECTION)
         */
        if ($user && $user['role'] === 'admin') {
            $_SESSION['error'] = "CẢNH BÁO: Hệ thống không cho phép xóa tài khoản Quản trị viên!";
            session_write_close();
            $this->redirect('adminuser/index');
            return;
        }

        $userModel->delete($id);
        $_SESSION['success'] = "Đã gỡ bỏ thành viên khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminuser/index');
    }

    /**
     * Hàm hỗ trợ xử lý lỗi để bật lại Modal và giữ dữ liệu cũ
     */
    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminuser/index');
    }
}