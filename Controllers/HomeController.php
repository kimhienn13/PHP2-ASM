<?php

/**
 * HomeController
 * Controller chịu trách nhiệm xử lý logic cho Trang chủ.
 * Đã cập nhật để đồng bộ cấu trúc với ProductController và Core của hệ thống.
 */
class HomeController extends Controller
{
    /**
     * Hàm index: Hiển thị trang chủ
     * Đường dẫn: BASE_URL/ hoặc BASE_URL/home/index
     */
    public function index()
    {
        // 1. Khởi tạo Model Product (Sử dụng hàm model() chuẩn từ Core Controller)
        $productModel = $this->model('Product');

        // 2. Lấy tham số tìm kiếm và phân trang
        // Action tìm kiếm ở View index đang trỏ về /product/index, 
        // nhưng ta vẫn giữ logic này để hiển thị danh sách sản phẩm nổi bật tại trang chủ.
        $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; // Số lượng sản phẩm hiển thị ở trang chủ

        // 3. Lấy dữ liệu sản phẩm (Sử dụng hàm list() giống ProductController để đồng bộ logic)
        // Hàm list() trong Model trả về mảng ['data' => ..., 'totalPages' => ...]
        $result = $productModel->list($page, $limit, $keyword);

        // 4. Chuẩn bị dữ liệu gửi sang View
        $data = [
            'title'       => 'TechMart Home - Nâng Tầm Không Gian Sống',
            'products'    => $result['data'],       // Danh sách sản phẩm
            'totalPages'  => $result['totalPages'], // Tổng số trang
            'currentPage' => $page,                 // Trang hiện tại
            'search'      => $keyword               // Từ khóa tìm kiếm
        ];

        // 5. Gọi View
        // Load file: views/user/index.blade.php
        $this->view('user.index', $data);
    }

    /**
     * Hàm điều hướng nhanh (Redirect)
     */
    public function products()
    {
        // Sử dụng hàm redirect() có sẵn trong Controller cha
        $this->redirect('product/index');
    }
}