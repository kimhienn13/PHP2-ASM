<?php

use Jenssegers\Blade\Blade;

/**
 * Lớp Controller cơ sở - Phiên bản sử dụng Thư viện Jenssegers/Blade chính thức
 * Đảm bảo biên dịch giao diện chuẩn xác và không bị lỗi cú pháp cache.
 */
class Controller
{
    /**
     * Hàm hiển thị giao diện sử dụng Blade Engine
     * @param string $path Đường dẫn view (ví dụ: 'user.index')
     * @param array $data Dữ liệu truyền xuống view
     */
    public function view($path, $data = [])
    {
        // Đường dẫn đến thư mục chứa các file .blade.php
        $viewPath = VIEW_PATH;
        // Đường dẫn đến thư mục lưu cache của Blade (Cần tạo thư mục này)
        $cachePath = STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';

        // Tự động tạo thư mục cache nếu chưa có
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        try {
            // Khởi tạo Blade Engine từ thư viện
            $blade = new Blade($viewPath, $cachePath);

            /**
             * Render giao diện và xuất ra trình duyệt
             * Cú pháp gọi trong Controller: $this->view('product.index', $data);
             */
            echo $blade->make($path, $data)->render();
        } catch (Exception $e) {
            // Hiển thị lỗi chi tiết nếu file .blade.php có vấn đề về cú pháp
            die("<div style='padding:20px; border:1px solid red; font-family:sans-serif;'>
                    <h3 style='color:red;'>Lỗi Blade Engine:</h3>
                    <p>" . $e->getMessage() . "</p>
                    <p><b>File:</b> " . $e->getFile() . " (Dòng: " . $e->getLine() . ")</p>
                 </div>");
        }
    }

    /**
     * Hàm khởi tạo và trả về đối tượng Model
     */
    public function model($name)
    {
        $class = ucfirst($name);
        if (!class_exists($class)) {
            // Thử nạp file model nếu chưa được nạp
            $modelFile = APP_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class . '.php';
            if (file_exists($modelFile)) {
                require_once $modelFile;
            } else {
                throw new Exception("Lỗi: Lớp Model '$class' không tồn tại.");
            }
        }
        return new $class();
    }

    /**
     * Hàm chuyển hướng trang (Redirect)
     * Luôn nối BASE_URL để đảm bảo đường dẫn tuyệt đối chuẩn xác
     */
    public function redirect($path)
    {
        $path = ltrim($path, '/');
        $target = BASE_URL . '/' . $path;
        header("Location: $target");
        exit();
    }

    /**
     * Hiển thị lỗi 404
     */
    public function notfound($message = "Trang không tồn tại"): void
    {
        http_response_code(404);
        echo "<div style='text-align:center; margin-top:50px; font-family: sans-serif;'>";
        echo "<h1 style='color: #cbd5e1; font-size: 100px; margin:0;'>404</h1>";
        echo "<h2>Không tìm thấy trang</h2>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
        echo "<a href='" . BASE_URL . "'>Quay lại trang chủ</a>";
        echo "</div>";
        exit();
    }
}