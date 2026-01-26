<?php

/**
 * AdminCategoryController - Quản lý danh mục sản phẩm (Laptop, Điện thoại, Phụ kiện...)
 * Phiên bản tối ưu: Sửa lỗi chính tả, tối ưu hóa xử lý Modal và Upload ảnh.
 */
class AdminCategoryController extends AdminController {
    
    /**
     * Hiển thị danh sách danh mục kèm phân trang và tìm kiếm.
     */
    public function index() {
        $model = $this->model('Category');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        
        // Lấy dữ liệu phân trang từ Model Category
        $result = $model->list($page, 8, $search);

        $this->view('admin.category.index', [
            'categories'  => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'title'       => 'Quản lý Danh mục'
        ]);
    }

    /**
     * Xử lý lưu danh mục mới (Store).
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $name = trim($_POST['name'] ?? '');
            $model = $this->model('Category');

            // 1. Kiểm tra tính hợp lệ cơ bản
            if (empty($name)) {
                $_SESSION['error'] = "Vui lòng nhập tên danh mục!";
                $this->handleError('add', $_POST);
                return;
            }

            // 2. Kiểm tra trùng lặp
            if ($model->isDuplicate($name)) {
                $_SESSION['error'] = "Danh mục '$name' đã tồn tại!";
                $this->handleError('add', $_POST);
                return;
            }

            // 3. Xử lý hình ảnh đại diện
            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) {
                    $imageName = $uploaded;
                }
            }

            try {
                // Lưu vào database
                $model->create([
                    'name'  => $name, 
                    'image' => $imageName
                ]);

                $_SESSION['success'] = "Đã thêm danh mục mới thành công!";
                session_write_close();
                $this->redirect('admincategory/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    /**
     * Xử lý cập nhật danh mục (Update).
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $model = $this->model('Category');
            $current = $model->show($id);
            
            if (!$current) {
                $_SESSION['error'] = "Không tìm thấy dữ liệu danh mục!";
                $this->redirect('admincategory/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = "Tên danh mục không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            // Kiểm tra trùng tên (trừ ID hiện tại)
            if ($model->isDuplicate($name, $id)) {
                $_SESSION['error'] = "Tên danh mục này đã được sử dụng!";
                $this->handleError('edit', $_POST);
                return;
            }

            // Logic xử lý ảnh: Giữ ảnh cũ nếu không chọn file mới
            $imageName = $current['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) {
                    $imageName = $uploaded;
                }
            }

            try {
                $model->update($id, [
                    'name'  => $name, 
                    'image' => $imageName
                ]);

                $_SESSION['success'] = "Cập nhật danh mục thành công!";
                session_write_close();
                $this->redirect('admincategory/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi cập nhật: " . $e->getMessage();
                $this->handleError('edit', $_POST);
            }
        }
    }

    /**
     * Xóa danh mục (Soft Delete).
     */
    public function destroy($id) {
        $this->model('Category')->delete($id);
        $_SESSION['success'] = "Đã xóa danh mục khỏi hệ thống!";
        session_write_close();
        $this->redirect('admincategory/index');
    }

    /**
     * Hàm xử lý lỗi để trả dữ liệu cũ về Modal.
     */
    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type;
        $_SESSION['old'] = $postData;
        session_write_close();
        $this->redirect('admincategory/index');
    }

    /**
     * Hàm hỗ trợ tải lên hình ảnh vào thư mục public/uploads/categories/
     */
    private function uploadFile($file) {
        // Sử dụng DIRECTORY_SEPARATOR để đảm bảo đường dẫn chuẩn trên mọi hệ điều hành
        $targetDir = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR;
        
        // Tự động tạo thư mục nếu chưa tồn tại
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            // Tạo tên file độc nhất để không bị ghi đè
            $fileName = "cat_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                return $fileName;
            }
        }
        return null;
    }
}