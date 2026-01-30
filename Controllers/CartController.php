<?php

class CartController extends Controller {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        foreach ($cart as $item) $subtotal += $item['price'] * $item['quantity'];

        $discount = 0;
        $coupon = $_SESSION['applied_coupon'] ?? null;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value']) / 100 : $coupon['value'];
        }
        if ($discount > $subtotal) $discount = $subtotal;
        $total = $subtotal - $discount;

        $this->view('user.cart.index', [
            'title' => 'Giỏ hàng của bạn', 
            'cart' => $cart, 
            'subtotal' => $subtotal,
            'discount' => $discount, 
            'coupon' => $coupon, 
            'total' => $total
        ]);
    }

    /**
     * XỬ LÝ THÊM VÀO GIỎ HÀNG (STRICT MODE)
     */
    public function add($id = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Chỉ chấp nhận phương thức POST từ form chi tiết sản phẩm
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Nếu khách click "Quick Add" (GET), chuyển hướng về trang chi tiết để chọn màu/size
            if ($id) {
                $this->redirect('product/show/' . $id);
                return;
            }
            $this->redirect('product/index');
            return;
        }

        // 2. Lấy dữ liệu đầu vào
        $productId = $_POST['product_id'] ?? null;
        $variantId = $_POST['variant_id'] ?? null;
        $quantity  = (int)($_POST['quantity'] ?? 1);

        // 3. Validate: Bắt buộc phải chọn biến thể
        if (!$variantId || empty($variantId)) {
            $_SESSION['error'] = "Vui lòng chọn Màu sắc và Kích thước trước khi thêm vào giỏ!";
            $this->redirect('product/show/' . $productId);
            return;
        }

        if ($quantity < 1) $quantity = 1;

        // 4. Gọi Model lấy thông tin biến thể thực tế từ DB
        $variantModel = $this->model('ProductVariant');
        $variant = $variantModel->findVariantDetail($variantId);

        // Kiểm tra biến thể có tồn tại không
        if (!$variant) {
            $_SESSION['error'] = "Sản phẩm hoặc phiên bản này không còn tồn tại!";
            $this->redirect('product/show/' . $productId);
            return;
        }

        // 5. Xử lý Logic Giá (Ưu tiên giá Variant, nếu Variant giá 0 thì lấy giá Product)
        $finalPrice = ($variant['price'] > 0) ? $variant['price'] : $variant['product_base_price'];
        
        // Xử lý Ảnh (Ưu tiên ảnh Variant, nếu không có thì lấy ảnh Product)
        // Giả sử cột image trong variants chưa có thì dùng product_image
        $finalImage = !empty($variant['image']) ? $variant['image'] : $variant['product_image'];

        // 6. Kiểm tra Tồn kho (Inventory Check)
        $cartKey = 'v_' . $variantId; // Key duy nhất cho giỏ hàng
        $currentQtyInCart = isset($_SESSION['cart'][$cartKey]) ? $_SESSION['cart'][$cartKey]['quantity'] : 0;
        $totalQtyRequested = $currentQtyInCart + $quantity;

        // Lấy tồn kho thực tế (cột stock)
        $realStock = (int)$variant['stock'];

        if ($realStock <= 0) {
            $_SESSION['error'] = "Sản phẩm này hiện tại đã hết hàng!";
            $this->redirect('product/show/' . $productId);
            return;
        }

        if ($totalQtyRequested > $realStock) {
            $canAdd = $realStock - $currentQtyInCart;
            if ($canAdd > 0) {
                $_SESSION['error'] = "Kho chỉ còn $realStock sản phẩm. Bạn đã có $currentQtyInCart trong giỏ, chỉ có thể thêm tối đa $canAdd nữa.";
            } else {
                $_SESSION['error'] = "Bạn đã thêm toàn bộ số lượng có sẵn trong kho vào giỏ hàng rồi!";
            }
            $this->redirect('product/show/' . $productId);
            return;
        }

        // 7. Thêm vào Session Cart
        if (!isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey] = [
                'id'           => $productId,
                'variant_id'   => $variantId,
                'name'         => $variant['product_name'] . ' - ' . $variant['color_name'] . ' / ' . $variant['size_name'],
                'price'        => $finalPrice,
                'image'        => $finalImage,
                'quantity'     => $quantity,
                'max_stock'    => $realStock // Lưu lại để JS trang giỏ hàng giới hạn
            ];
        } else {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        }

        // 8. Thông báo thành công
        $_SESSION['success'] = "Đã thêm sản phẩm vào giỏ hàng thành công!";
        session_write_close();
        
        // Ở lại trang hiện tại hoặc chuyển sang giỏ hàng (tùy nhu cầu, ở đây giữ lại trang chi tiết)
        $this->redirect('product/show/' . $productId);
    }

    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $key = $_POST['id']; 
            $quantity = (int)$_POST['quantity'];

            if (isset($_SESSION['cart'][$key])) {
                $maxStock = $_SESSION['cart'][$key]['max_stock'] ?? 100;
                
                if ($quantity > $maxStock) {
                    $quantity = $maxStock;
                    $_SESSION['error'] = "Kho chỉ còn $maxStock sản phẩm!";
                }

                if ($quantity > 0) $_SESSION['cart'][$key]['quantity'] = $quantity;
                else unset($_SESSION['cart'][$key]);
            }
            
            session_write_close();
            $this->redirect('cart/index');
        }
    }

    // Các hàm khác giữ nguyên (applyCoupon, remove, clear...)
    public function remove($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart'][$id]);
        $this->redirect('cart/index');
    }

    public function clear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        $this->redirect('cart/index');
    }
}