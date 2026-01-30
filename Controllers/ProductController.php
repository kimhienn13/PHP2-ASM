<?php

/**
 * ProductController xử lý các hành động liên quan đến hiển thị sản phẩm cho khách hàng.
 * Tương thích với Router và Core Controller hiện tại.
 */
class ProductController extends Controller {
    
    /**
     * Hiển thị danh sách sản phẩm (Trang cửa hàng)
     */
    public function index() {
        $productModel = $this->model('Product');
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);

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
     * CẬP NHẬT: Lấy thêm Variants để hiển thị chọn Màu/Size
     * FIX: Lấy thêm Gallery để hiển thị album ảnh
     */
    public function show($id) {
        if (!$id) {
            $this->redirect('product/index');
            return;
        }

        $productModel = $this->model('Product');
        $product = $productModel->show($id);

        if (!$product) {
            $this->notfound("Sản phẩm mà bạn tìm kiếm không tồn tại hoặc đã bị gỡ bỏ.");
            return;
        }

        // 1. LẤY BIẾN THỂ (Màu/Size)
        $variantModel = $this->model('ProductVariant');
        $variants = $variantModel->getByProductId($id);

        // 2. [FIX] LẤY ALBUM ẢNH (GALLERY)
        // Đoạn này bị thiếu trong code cũ khiến ảnh không hiển thị
        $galleryModel = $this->model('ProductGallery');
        $gallery = $galleryModel->getByProductId($id);

        $this->view('user.product.detail', [
            'title'    => $product['name'] . ' - TechMart',
            'product'  => $product,
            'variants' => $variants,
            'gallery'  => $gallery // Truyền biến gallery sang view
        ]);
    }
}