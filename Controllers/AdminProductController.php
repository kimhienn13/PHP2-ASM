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
            $category_id = $_POST['category_id'] ?: null;
            $brand_id = $_POST['brand_id'] ?: null;

            // Xử lý tạo nhanh Category/Brand (Giữ nguyên logic cũ)
            if (!empty($_POST['new_category'])) {
                $newCatName = trim($_POST['new_category']);
                $catModel = $this->model('Category');
                try {
                    $catModel->create(['name' => $newCatName, 'image' => 'default.jpg']);
                    $newCat = $catModel->query("SELECT id FROM category WHERE name = '$newCatName' ORDER BY id DESC LIMIT 1")->fetch();
                    if ($newCat) $category_id = $newCat['id'];
                } catch (Exception $e) { }
            }
            if (!empty($_POST['new_brand'])) {
                $newBrandName = trim($_POST['new_brand']);
                $brandModel = $this->model('Brand');
                try {
                    $brandModel->create(['name' => $newBrandName, 'description' => 'Tạo nhanh', 'image' => 'default.jpg']);
                    $newBrand = $brandModel->query("SELECT id FROM brands WHERE name = '$newBrandName' ORDER BY id DESC LIMIT 1")->fetch();
                    if ($newBrand) $brand_id = $newBrand['id'];
                } catch (Exception $e) { }
            }

            if (empty($name) || $price < 0) { 
                $_SESSION['error'] = "Tên không được trống và giá không được âm!";
                $this->redirect('adminproduct/index');
                return;
            }

            // 1. Xử lý ảnh đại diện chính
            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                // Tạo sản phẩm
                $newProductId = $this->model('Product')->create([
                    'name' => $name, 'price' => $price, 'image' => $imageName,
                    'category_id' => $category_id, 'brand_id' => $brand_id
                ]);

                // 2. [NEW] Xử lý Album ảnh (Gallery)
                if ($newProductId && !empty($_FILES['gallery']['name'][0])) {
                    $galleryModel = $this->model('ProductGallery');
                    $files = $_FILES['gallery'];
                    $count = count($files['name']);
                    
                    for ($i = 0; $i < $count; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $fileData = [
                                'name' => $files['name'][$i],
                                'tmp_name' => $files['tmp_name'][$i],
                                'size' => $files['size'][$i]
                            ];
                            $galleryImg = $this->uploadFile($fileData);
                            if ($galleryImg) {
                                $galleryModel->add($newProductId, $galleryImg);
                            }
                        }
                    }
                }

                // 3. Xử lý biến thể nhanh (nếu có)
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
                                    'color_id' => $colors[$i],
                                    'size_id' => $sizes[$i],
                                    'quantity' => max(0, (int)$qtys[$i]), 
                                    'price' => 0,
                                    'image' => null // Biến thể tạo nhanh chưa có ảnh
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

    public function update() {
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('adminproduct/index'); return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             if (session_status() === PHP_SESSION_NONE) session_start();
             $name = trim($_POST['name']);
             $price = (float)$_POST['price'];
             $category_id = $_POST['category_id'] ?: null;
             $brand_id = $_POST['brand_id'] ?: null;
             $imageName = $_POST['current_image'] ?? 'default.jpg';

             if ($price < 0) {
                 $_SESSION['error'] = "Giá sản phẩm không được âm!";
                 $this->redirect('adminproduct/index');
                 return;
             }

             if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

             $this->model('Product')->update($id, [
                 'name' => $name, 'price' => $price, 'image' => $imageName,
                 'category_id' => $category_id, 'brand_id' => $brand_id
             ]);

             // [NEW] Xử lý thêm ảnh vào Gallery khi update
             if (!empty($_FILES['gallery']['name'][0])) {
                $galleryModel = $this->model('ProductGallery');
                $files = $_FILES['gallery'];
                $count = count($files['name']);
                
                for ($i = 0; $i < $count; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $fileData = [
                            'name' => $files['name'][$i],
                            'tmp_name' => $files['tmp_name'][$i]
                        ];
                        $galleryImg = $this->uploadFile($fileData);
                        if ($galleryImg) {
                            $galleryModel->add($id, $galleryImg);
                        }
                    }
                }
             }

             $_SESSION['success'] = "Cập nhật thành công!";
             session_write_close();
             $this->redirect('adminproduct/index');
        }
    }

    public function destroy() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model('ProductVariant')->deleteByProductId($id);
            // Có thể thêm xóa gallery nếu muốn dọn dẹp triệt để
            $this->model('Product')->delete($id);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['success'] = "Đã xóa sản phẩm!";
        }
        $this->redirect('adminproduct/index');
    }

    // [NEW] API Xóa ảnh trong gallery
    public function deleteGalleryImage() {
        $id = $_GET['id'] ?? null;
        $pid = $_GET['product_id'] ?? null;
        if ($id) {
            $this->model('ProductGallery')->delete($id);
        }
        if ($pid) {
             // Redirect lại modal edit thông qua view variants (trick) hoặc trả về json nếu dùng ajax
             // Ở đây redirect về variants iframe để refresh state
             $this->redirect('adminproduct/variants/' . $pid . '?iframe=true'); 
        }
    }

    public function variants($id) {
        $product = $this->model('Product')->show($id);
        if(!$product) { $this->redirect('adminproduct'); return; }

        $variantModel = $this->model('ProductVariant');
        $variants = $variantModel->getByProductId($id);

        foreach ($variants as &$variant) {
            $variant['price'] = (float)$variant['price']; 
        }
        unset($variant);

        // Lấy Gallery
        $gallery = $this->model('ProductGallery')->getByProductId($id);

        $totalStock = $variantModel->getTotalStock($id);
        $attrModel = $this->model('ProductAttribute');
        $colors = $attrModel->getByType('color');
        $sizes = $attrModel->getByType('size');
        
        $isIframe = isset($_GET['iframe']) && $_GET['iframe'] == 'true';

        $this->view('admin.Product.variants', [
            'product' => $product, 
            'variants' => $variants,
            'gallery' => $gallery, // Truyền gallery sang view
            'colors' => $colors, 
            'sizes' => $sizes, 
            'total_stock'=> $totalStock, 
            'is_iframe' => $isIframe
        ]);
    }

    // --- HÀM STORE VARIANT CÓ ẢNH ---
    public function storeVariant() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             if (session_status() === PHP_SESSION_NONE) session_start();
             
             $product_id = $_POST['product_id'];
             $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
             $quantity = (int)$_POST['quantity'];

             // Xử lý ảnh biến thể
             $variantImage = null;
             if (isset($_FILES['variant_image']) && $_FILES['variant_image']['error'] === UPLOAD_ERR_OK) {
                 $variantImage = $this->uploadFile($_FILES['variant_image']);
             }

             if ($price < 0 || $quantity < 0) {
                 $_SESSION['error'] = "Giá và số lượng không được âm!";
                 $this->redirectBack($product_id);
                 return;
             }

             try {
                 $variantModel = $this->model('ProductVariant');
                 if ($variantModel->checkDuplicate($product_id, $_POST['color_id'], $_POST['size_id'])) {
                     $_SESSION['error'] = "Biến thể màu và size này đã tồn tại!";
                 } else {
                     $variantModel->create([
                         'product_id' => $product_id,
                         'color_id' => $_POST['color_id'],
                         'size_id' => $_POST['size_id'],
                         'quantity' => $quantity,
                         'price'    => $price,
                         'image'    => $variantImage // Lưu ảnh
                     ]);
                     $_SESSION['success'] = "Thêm biến thể thành công";
                 }
             } catch (Exception $e) {
                 $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
             }
             
             $this->redirectBack($product_id);
        }
    }
    
    // --- UPDATE VARIANT (Stock + Price + Image) ---
    public function updateVariantStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $variant_id = $_POST['variant_id'];
            $product_id = $_POST['product_id'];
            
            $quantity = (int)($_POST['quantity'] ?? 0);
            $price    = (float)($_POST['price'] ?? 0);

            if ($quantity < 0 || $price < 0) {
                $_SESSION['error'] = "Số lượng và giá không được nhỏ hơn 0!";
                $this->redirectBack($product_id);
                return;
            }

            try {
                $dataToUpdate = [
                    'stock' => $quantity, 
                    'price' => $price
                ];

                // Check nếu có upload ảnh mới cho biến thể này
                if (isset($_FILES['variant_image']) && $_FILES['variant_image']['error'] === UPLOAD_ERR_OK) {
                    $newImg = $this->uploadFile($_FILES['variant_image']);
                    if ($newImg) {
                        $dataToUpdate['image'] = $newImg;
                    }
                }

                $this->model('ProductVariant')->update($variant_id, $dataToUpdate);
                
                $_SESSION['success'] = "Cập nhật thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi cập nhật: " . $e->getMessage();
            }

            $this->redirectBack($product_id);
        }
    }

    private function redirectBack($product_id) {
        $isIframe = isset($_REQUEST['is_iframe']) || (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'iframe=true') !== false);
        if (isset($_POST['is_iframe']) && $_POST['is_iframe'] == '1') $isIframe = true;

        if ($isIframe) $this->redirect('adminproduct/variants/' . $product_id . '?iframe=true');
        else $this->redirect('adminproduct/variants/' . $product_id);
    }

    public function deleteVariant() {
        $id = $_GET['id']; 
        $pid = $_GET['product_id'];
        
        $this->model('ProductVariant')->delete($id);
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['success'] = "Đã xóa biến thể";
        
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