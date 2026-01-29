<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\ProductVariant;

class AdminProductVariantController extends Controller {
    
    // Hiển thị danh sách biến thể của 1 sản phẩm
    public function index($id) {
        $productModel = new Product();
        $variantModel = new ProductVariant();
        
        $product = $productModel->find($id);
        $variants = $variantModel->getByProductId($id);
        
        return $this->view('admin.product_variants.index', [
            'product' => $product,
            'variants' => $variants
        ]);
    }

    // Thêm biến thể mới
    public function store($product_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'product_id' => $product_id,
                'name' => $_POST['name'], // Ví dụ: Đỏ - Size L
                'sku' => $_POST['sku'],
                'price' => $_POST['price'],
                'stock' => $_POST['stock'],
            ];

            // Xử lý upload ảnh riêng cho variant
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "uploads/";
                $file_name = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $file_name);
                $data['image'] = $file_name;
            }

            $model = new ProductVariant();
            $model->create($data);
            
            header('Location: /admin/products/variants/' . $product_id);
        }
    }

    public function delete($id, $product_id) {
        $model = new ProductVariant();
        $model->delete($id);
        header('Location: /admin/products/variants/' . $product_id);
    }
}