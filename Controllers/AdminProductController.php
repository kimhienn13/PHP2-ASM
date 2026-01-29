<?php

class AdminProductController extends AdminController {
    
    public function index() {
        $productModel = $this->model('Product');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);
        
        $categories = $this->model('Category')->getAll();
        $brands = $this->model('Brand')->getAll();
        
        $colors = $this->model('ProductAttribute')->getByType('color');
        $sizes = $this->model('ProductAttribute')->getByType('size');
        
        $this->view('admin.Product.index', [
            'title'          => 'Hệ thống Quản lý Sản phẩm',
            'products'       => $result['data'],
            'totalPages'     => $result['totalPages'],
            'currentPage'    => $page,
            'search'         => $search,
            'all_categories' => $categories, 
            'all_brands'     => $brands,
            'all_colors'     => $colors, 
            'all_sizes'      => $sizes   
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            
            // Lấy ID từ select hoặc xử lý tạo mới
            $category_id = $_POST['category_id'] ?: null;
            $brand_id = $_POST['brand_id'] ?: null;

            // --- XỬ LÝ THÊM NHANH DANH MỤC ---
            if (!empty($_POST['new_category'])) {
                $newCatName = trim($_POST['new_category']);
                $catModel = $this->model('Category');
                try {
                    $catModel->create(['name' => $newCatName, 'image' => 'default.jpg']);
                    $newCat = $catModel->query("SELECT id FROM category WHERE name = '$newCatName' ORDER BY id DESC LIMIT 1")->fetch();
                    if ($newCat) {
                        $category_id = $newCat['id'];
                    }
                } catch (Exception $e) { }
            }

            // --- XỬ LÝ THÊM NHANH THƯƠNG HIỆU ---
            if (!empty($_POST['new_brand'])) {
                $newBrandName = trim($_POST['new_brand']);
                $brandModel = $this->model('Brand');
                try {
                    $brandModel->create([
                        'name' => $newBrandName, 
                        'description' => 'Tạo nhanh từ sản phẩm',
                        'image' => 'default.jpg'
                    ]);
                    $newBrand = $brandModel->query("SELECT id FROM brands WHERE name = '$newBrandName' ORDER BY id DESC LIMIT 1")->fetch();
                    if ($newBrand) {
                        $brand_id = $newBrand['id'];
                    }
                } catch (Exception $e) { }
            }

            if (empty($name) || $price <= 0) {
                $_SESSION['error'] = "Tên và giá không hợp lệ!";
                $this->redirect('adminproduct/index');
                return;
            }

            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                // 1. Tạo sản phẩm
                $newProductId = $this->model('Product')->create([
                    'name'        => $name,
                    'price'       => $price,
                    'image'       => $imageName,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id
                ]);

                // 2. Thêm biến thể (Màu/Size)
                if ($newProductId && !empty($_POST['variants_color'])) {
                    $variantModel = $this->model('ProductVariant');
                    $colors = $_POST['variants_color'];
                    $sizes = $_POST['variants_size'];
                    $qtys = $_POST['variants_qty'];

                    for ($i = 0; $i < count($colors); $i++) {
                        if (!empty($colors[$i]) && !empty($sizes[$i])) {
                            if (!$variantModel->checkDuplicate($newProductId, $colors[$i], $sizes[$i])) {
                                $variantModel->create([
                                    'product_id' => $newProductId,
                                    'color_id'   => $colors[$i],
                                    'size_id'    => $sizes[$i],
                                    'quantity'   => (int)$qtys[$i]
                                ]);
                            }
                        }
                    }
                }

                $_SESSION['success'] = "Sản phẩm đã được tạo thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            }
            
            session_write_close();
            $this->redirect('adminproduct/index');
        }
    }

    /**
     * SỬA LỖI: Bỏ tham số $id trong hàm update vì URL dùng query param (?id=...) 
     * chứ không phải path param (/update/id).
     */
    public function update() {
        // Lấy ID từ URL (query string)
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
             // Nếu không có ID, quay về trang chủ hoặc báo lỗi
             $this->redirect('adminproduct/index');
             return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             if (session_status() === PHP_SESSION_NONE) session_start();
             
             $name = trim($_POST['name']);
             $price = (float)$_POST['price'];
             $category_id = $_POST['category_id'] ?: null;
             $brand_id = $_POST['brand_id'] ?: null;
             $imageName = $_POST['current_image'] ?? 'default.jpg';

             if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

             $this->model('Product')->update($id, [
                 'name' => $name, 'price' => $price, 'image' => $imageName,
                 'category_id' => $category_id, 'brand_id' => $brand_id
             ]);

             $_SESSION['success'] = "Cập nhật thành công!";
             session_write_close();
             $this->redirect('adminproduct/index');
        }
    }

    /**
     * SỬA LỖI: Hàm xóa cũng cần lấy ID từ $_GET vì link là delete?id=...
     * Trong router của bạn có thể ánh xạ adminproduct/delete -> method destroy hoặc delete.
     * Ở đây tôi sửa method destroy để an toàn.
     */
    public function destroy() { // Bỏ tham số $id
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model('ProductVariant')->deleteByProductId($id);
            $this->model('Product')->delete($id);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['success'] = "Đã xóa sản phẩm!";
        }
        $this->redirect('adminproduct/index');
    }

    // Các hàm Variants giữ nguyên vì URL có dạng /variants/123 nên Router vẫn truyền ID vào
    public function variants($id) {
        $product = $this->model('Product')->show($id);
        if(!$product) { $this->redirect('adminproduct'); return; }

        $variantModel = $this->model('ProductVariant');
        $variants = $variantModel->getByProductId($id);
        $totalStock = $variantModel->getTotalStock($id);
        $attrModel = $this->model('ProductAttribute');
        $colors = $attrModel->getByType('color');
        $sizes = $attrModel->getByType('size');
        $isIframe = isset($_GET['iframe']) && $_GET['iframe'] == 'true';

        $this->view('admin.Product.variants', [
            'product' => $product, 'variants' => $variants,
            'colors' => $colors, 'sizes' => $sizes, 'total_stock'=> $totalStock,
            'is_iframe' => $isIframe
        ]);
    }
    
    public function storeVariant() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             if (session_status() === PHP_SESSION_NONE) session_start();
             $product_id = $_POST['product_id'];
             $isIframe = isset($_POST['is_iframe']) && $_POST['is_iframe'] == '1';
             
             $this->model('ProductVariant')->create([
                 'product_id' => $product_id,
                 'color_id' => $_POST['color_id'],
                 'size_id' => $_POST['size_id'],
                 'quantity' => $_POST['quantity']
             ]);
             
             if ($isIframe) $this->redirect('adminproduct/variants/' . $product_id . '?iframe=true');
             else $this->redirect('adminproduct/variants/' . $product_id);
        }
    }
    
    public function updateVariantStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model('ProductVariant')->updateStock($_POST['variant_id'], $_POST['quantity']);
            $isIframe = isset($_POST['is_iframe']) && $_POST['is_iframe'] == '1';
            if ($isIframe) $this->redirect('adminproduct/variants/' . $_POST['product_id'] . '?iframe=true');
            else $this->redirect('adminproduct/variants/' . $_POST['product_id']);
        }
    }

    public function deleteVariant() {
        $id = $_GET['id']; $pid = $_GET['product_id'];
        $this->model('ProductVariant')->delete($id);
        $isIframe = isset($_GET['iframe']) && $_GET['iframe'] == 'true';
        if ($isIframe) $this->redirect('adminproduct/variants/' . $pid . '?iframe=true');
        else $this->redirect('adminproduct/variants/' . $pid);
    }

    private function uploadFile($file) {
        $targetDir = BASE_PATH . "/public/uploads/products/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $fileName = "techmart_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) return $fileName;
        }
        return null;
    }
}