<?php

/**
 * AdminBrandController - Quản lý thương hiệu đối tác.
 * Phiên bản sửa lỗi hiển thị ảnh và đồng bộ Modal.
 */
class AdminBrandController extends AdminController {
    
    /**
     * Hiển thị danh sách thương hiệu
     */
    public function index() {
        $brandModel = $this->model('Brand');
        
        // 1. Lấy tham số phân trang và tìm kiếm từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; // Hiển thị 8 thương hiệu mỗi trang (vì giao diện dùng grid 4 cột)

        // 2. Gọi hàm list từ Model (Model Brand đã có sẵn hàm list kế thừa paginate)
        $result = $brandModel->list($page, $limit, $search);
        
        // 3. Trả về view với đầy đủ dữ liệu phân trang
        $this->view('admin.brand.index', [
            'title'       => 'Quản lý Thương hiệu',
            'brands'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    /**
     * Xử lý lưu thương hiệu mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $model = $this->model('Brand');

            // 1. Kiểm tra tính hợp lệ
            if (empty($name)) {
                $_SESSION['error'] = "Vui lòng nhập tên thương hiệu!";
                $this->handleError('add', $_POST);
                return;
            }

            // Kiểm tra trùng (Yêu cầu Model có hàm exists)
            if (method_exists($model, 'exists') && $model->exists($name)) {
                $_SESSION['error'] = "Thương hiệu '$name' đã tồn tại!";
                $this->handleError('add', $_POST);
                return;
            }

            // 2. Xử lý Upload Ảnh
            $logoName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) {
                    $logoName = $uploaded;
                } else {
                    $_SESSION['error'] = "Lỗi: Không thể lưu file ảnh vào thư mục uploads/brands/";
                    $this->handleError('add', $_POST);
                    return;
                }
            }

            // 3. Lưu vào DB
            try {
                $model->create([
                    'name'        => $name,
                    'description' => trim($_POST['description'] ?? ''),
                    'image'       => $logoName
                ]);

                $_SESSION['success'] = "Đã thêm thương hiệu mới thành công!";
                session_write_close();
                $this->redirect('adminbrand/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi Database: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    /**
     * Xử lý cập nhật thương hiệu
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->model('Brand');
            
            // Kiểm tra Model có hàm show() không để lấy ảnh cũ
            $current = method_exists($model, 'show') ? $model->show($id) : null;
            
            if (!$current) {
                $_SESSION['error'] = "Dữ liệu không tồn tại!";
                $this->redirect('adminbrand/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = "Tên không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            // QUAN TRỌNG: Giữ ảnh cũ nếu người dùng không chọn ảnh mới
            $logoName = $current['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) {
                    $logoName = $uploaded;
                }
            }

            try {
                // Đảm bảo Model có hàm update()
                if (method_exists($model, 'update')) {
                    $model->update($id, [
                        'name'        => $name,
                        'description' => trim($_POST['description'] ?? ''),
                        'image'       => $logoName
                    ]);
                    $_SESSION['success'] = "Cập nhật thành công thương hiệu!";
                } else {
                    $_SESSION['error'] = "Lỗi: Model Brand thiếu phương thức update()";
                }
                
                session_write_close();
                $this->redirect('adminbrand/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi cập nhật: " . $e->getMessage();
                $this->handleError('edit', $_POST);
            }
        }
    }

    /**
     * Xóa thương hiệu (Xóa mềm)
     */
    public function destroy($id) {
        $this->model('Brand')->delete($id);
        $_SESSION['success'] = "Đã xóa thương hiệu khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminbrand/index');
    }

    /**
     * Hàm phụ trách xử lý lỗi cho Modal (Tự động bật lại Modal khi sai)
     */
    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminbrand/index');
    }

    /**
     * HÀM UPLOAD FILE - SỬA LỖI ĐƯỜNG DẪN TẠI ĐÂY
     */
    private function uploadFile($file) {
        /**
         * 1. Xác định đường dẫn vật lý trên ổ đĩa Laragon
         * BASE_PATH thường là C:\laragon\www\PHP2
         */
        $targetDir = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'brands' . DIRECTORY_SEPARATOR;
        
        // 2. Tự động tạo thư mục nếu chưa có (Phải có quyền Write)
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                return null; // Trả về null nếu không tạo được thư mục
            }
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

        if (in_array($ext, $allowed)) {
            // Tên file duy nhất: brand_123456789_abc.png
            $fileName = "brand_" . time() . "_" . uniqid() . "." . $ext;
            
            // 3. Thực hiện di chuyển file từ bộ nhớ tạm vào thư mục đích
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                return $fileName; // Thành công: Trả về tên file để lưu vào DB
            }
        }
        return null;
    }
}