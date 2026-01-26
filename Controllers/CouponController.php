<?php

/**
 * CouponController phục vụ các thao tác của khách hàng liên quan đến mã giảm giá.
 * Hỗ trợ cả hiển thị giao diện chính và trả về dữ liệu AJAX JSON.
 */
class CouponController extends Controller {
    
    /**
     * Hiển thị danh sách mã giảm giá.
     * Tự động nhận diện yêu cầu AJAX dựa trên tham số 'ajax=1'.
     */
    public function index() {
        // 1. Khởi tạo Model Coupon
        $couponModel = $this->model('Coupon');

        // 2. Lấy các tham số lọc từ URL (GET)
        $search = $_GET['search'] ?? '';
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit  = 9; // Hiển thị 9 mã mỗi trang để khớp với lưới 3 cột

        /**
         * 3. Lấy dữ liệu phân trang từ Database
         * Mặc định chỉ lấy các mã có status = 1 (đang hoạt động)
         * Hàm paginate() đã được định nghĩa trong Model cơ sở.
         */
        $result = $couponModel->paginate(
            'coupons',     // Tên bảng
            $page,         // Trang hiện tại
            $limit,        // Số lượng mỗi trang
            $search,       // Từ khóa tìm kiếm
            ['status' => 1], // Chỉ lấy mã đang hoạt động
            'code'         // Cột dùng để tìm kiếm
        );

        /**
         * 4. XỬ LÝ YÊU CẦU AJAX
         * Nếu tệp giao diện (Canvas) gửi yêu cầu kèm ajax=1, chúng ta trả về JSON.
         */
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'     => true,
                'data'        => $result['data'],
                'totalPages'  => $result['totalPages'],
                'currentPage' => $page
            ]);
            exit; // Ngắt luồng để không nạp Header/Footer của view
        }

        /**
         * 5. TRẢ VỀ GIAO DIỆN THÔNG THƯỜNG
         * Dùng cho lần nạp trang đầu tiên.
         */
        $this->view('user.coupon.index', [
            'coupons'     => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'title'       => 'Kho Voucher TechMart - Săn Ưu Đãi'
        ]);
    }

    /**
     * API kiểm tra mã giảm giá (Dùng cho trang Giỏ hàng/Thanh toán nếu cần)
     */
    public function check($code) {
        header('Content-Type: application/json');
        $couponModel = $this->model('Coupon');
        $coupon = $couponModel->findByCode($code);

        if ($coupon && $coupon['status'] == 1) {
            echo json_encode(['success' => true, 'data' => $coupon]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!']);
        }
    }
}