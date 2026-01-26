<?php

/**
 * CartController xử lý toàn bộ logic giỏ hàng và mã giảm giá cho TechMart.
 */
class CartController extends Controller {
    
    /**
     * Hiển thị trang giỏ hàng với các tính toán tổng tiền và giảm giá.
     */
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Lấy dữ liệu giỏ hàng từ Session
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        
        // Tính tạm tính
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // 2. Xử lý logic mã giảm giá (áp dụng từ session)
        $discount = 0;
        $coupon = $_SESSION['applied_coupon'] ?? null;

        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                // Giảm theo % (ví dụ: 10% của 1.000.000đ)
                $discount = ($subtotal * $coupon['value']) / 100;
            } else {
                // Giảm trực tiếp tiền mặt
                $discount = $coupon['value'];
            }
        }

        // Đảm bảo tiền giảm không lớn hơn tổng tiền
        if ($discount > $subtotal) $discount = $subtotal;
        
        $total = $subtotal - $discount;

        // 3. Trả về view Canvas với đầy đủ dữ liệu
        $this->view('user.cart.index', [
            'title'    => 'Giỏ hàng của bạn - TechMart',
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon'   => $coupon,
            'total'    => $total
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng và quay lại trang hiện tại (không chuyển trang).
     */
    public function add($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $productModel = $this->model('Product');
        $product = $productModel->show($id);

        if ($product) {
            // Nếu chưa có sản phẩm này trong giỏ, tạo mới
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => 1
                ];
            } else {
                // Nếu có rồi thì tăng số lượng
                $_SESSION['cart'][$id]['quantity']++;
            }
            
            $_SESSION['success'] = "Đã thêm '" . $product['name'] . "' vào giỏ hàng!";
            
            // Ghi session ngay lập tức trước khi redirect
            session_write_close();
        }

        // Quay lại trang trước đó (Trang SP hoặc Chi tiết)
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/product/index');
        header("Location: " . $referer);
        exit();
    }

    /**
     * Cập nhật số lượng sản phẩm ngay tại trang giỏ hàng.
     */
    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $id = $_POST['id'];
            $quantity = (int)$_POST['quantity'];

            if ($quantity > 0) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$id]);
            }
            
            session_write_close();
            $this->redirect('cart/index');
        }
    }

    /**
     * Xử lý áp dụng mã giảm giá.
     */
    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
            $couponModel = $this->model('Coupon');
            
            // Tìm mã trong Database
            $coupon = $couponModel->findByCode($code);

            if ($coupon && (int)$coupon['status'] === 1) {
                $_SESSION['applied_coupon'] = $coupon;
                $_SESSION['success'] = "Áp dụng mã '" . $coupon['code'] . "' thành công!";
            } else {
                $_SESSION['error'] = "Mã giảm giá không chính xác hoặc đã hết hạn!";
            }

            session_write_close();
            $this->redirect('cart/index');
        }
    }

    /**
     * Hủy bỏ mã giảm giá đã áp dụng.
     */
    public function removeCoupon() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['applied_coupon']);
        $_SESSION['success'] = "Đã hủy bỏ mã giảm giá.";
        session_write_close();
        $this->redirect('cart/index');
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng.
     */
    public function remove($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart'][$id]);
        session_write_close();
        $this->redirect('cart/index');
    }

    /**
     * Xóa sạch toàn bộ giỏ hàng.
     */
    public function clear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        session_write_close();
        $this->redirect('cart/index');
    }
}