<?php

/**
 * ProductController xử lý các hành động liên quan đến hiển thị sản phẩm cho khách hàng.
 * Tương thích với Router và Core Controller hiện tại.
 */
class ProductController extends Controller {
    
    /**
     * Hiển thị danh sách sản phẩm (Trang cửa hàng)
     * Hỗ trợ tìm kiếm và phân trang.
     */
    public function index() {
        // 1. Khởi tạo Model Product
        $productModel = $this->model('Product');

        // 2. Lấy dữ liệu từ URL (Search & Pagination)
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; // Số sản phẩm hiển thị trên mỗi trang

        // 3. Gọi hàm list từ Model để lấy dữ liệu
        $result = $productModel->list($page, $limit, $search);

        // 4. Trả về View với dữ liệu tương ứng
        // Đường dẫn view: app/views/user/product/index.blade.php
        $this->view('user.product.index', [
            'title'       => 'Sản phẩm - TechMart',
            'products'    => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    /**
     * Hiển thị chi tiết một sản phẩm cụ thể
     * @param int $id ID của sản phẩm cần xem
     */
    public function show($id) {
        if (!$id) {
            $this->redirect('product/index');
            return;
        }

        $productModel = $this->model('Product');
        $product = $productModel->show($id);

        // Nếu không tìm thấy sản phẩm, hiển thị trang 404
        if (!$product) {
            $this->notfound("Sản phẩm mà bạn tìm kiếm không tồn tại hoặc đã bị gỡ bỏ.");
            return;
        }

        // Trả về View chi tiết sản phẩm
        // Đường dẫn view: app/views/user/product/detail.blade.php
        $this->view('user.product.detail', [
            'title'   => $product['name'] . ' - TechMart',
            'product' => $product
        ]);
    }
}