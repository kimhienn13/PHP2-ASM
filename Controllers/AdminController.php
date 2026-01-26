<?php

/**
 * Controller cơ sở cho tất cả các trang quản trị
 * File này đảm bảo an ninh cho toàn bộ hệ thống Admin
 */
class AdminController extends Controller {
    public function __construct() {
        // Đảm bảo session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Kiểm tra xem người dùng đã đăng nhập chưa và có phải admin không
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            // SỬA LỖI: Đẩy ra trang login của ADMIN thay vì trang login của USER
            $_SESSION['error'] = "Vui lòng đăng nhập tài khoản Quản trị viên!";
            session_write_close();
            $this->redirect('adminauth/login');
        }
    }
}