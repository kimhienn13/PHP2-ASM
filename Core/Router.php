<?php

/**
 * Lớp Router xử lý điều hướng URL
 * Đã sửa lỗi: Tự động nhận diện thư mục dự án và fix lỗi undefined variable
 */
class Router
{
    /**
     * Phân tích URI và gọi Controller/Action tương ứng
     * @param string $uri $_SERVER['REQUEST_URI']
     */
    public function dispatch(string $uri): void
    {
        // 1. Tách phần path sạch của URL (bỏ qua các tham số sau dấu ?)
        $path = parse_url($uri, PHP_URL_PATH) ?? '';
        
        // 2. XÁC ĐỊNH THƯ MỤC GỐC DỰ ÁN (Ví dụ: /PHP2)
        // Sử dụng dirname của SCRIPT_NAME để lấy đường dẫn thực tế của dự án
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        
        // Loại bỏ phần '/public' nếu có để lấy thư mục gốc dự án
        $baseDir = rtrim(str_replace('/public', '', $scriptDir), '/');
        
        // 3. LOẠI BỎ THƯ MỤC GỐC KHỎI PATH (Ví dụ: /PHP2/auth/login -> /auth/login)
        if ($baseDir !== '' && strpos($path, $baseDir) === 0) {
            $path = substr($path, strlen($baseDir));
        }

        // 4. LOẠI BỎ "index.php" nếu xuất hiện trên URL
        $path = str_replace('/index.php', '', $path);

        // 5. Làm sạch đường dẫn (Xóa dấu / ở đầu và cuối)
        $path = trim($path, '/');
        
        // Nếu đường dẫn rỗng sau khi xử lý, mặc định là trang chủ
        if ($path === '') {
            $segments = ['home', 'index'];
        } else {
            $segments = explode('/', $path);
        }
        
        // 6. Xác định Controller và Action
        $controllerPart = $segments[0] ?? 'home';
        $controllerName = ucfirst($controllerPart) . 'Controller';
        $action = $segments[1] ?? 'index';
        $params = array_slice($segments, 2);

        // 7. Kiểm tra sự tồn tại của Class Controller
        if (!class_exists($controllerName)) {
            // FIX: Truyền biến $controllerName vào hàm show404 để tránh lỗi Undefined
            $this->show404("Hệ thống không tìm thấy bộ xử lý: <b>$controllerName</b>.<br>Đường dẫn Route thực tế: <code>$path</code>", $controllerName);
            return;
        }

        // Khởi tạo Controller
        $controller = new $controllerName();

        // 8. Kiểm tra sự tồn tại của Method (Action)
        if (!method_exists($controller, $action)) {
            $this->show404("Hành động <b>$action</b> không tồn tại trong <b>$controllerName</b>.", $controllerName);
            return;
        }

        /**
         * 9. THỰC THI
         */
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Hiển thị giao diện lỗi 404 thân thiện cho việc Debug
     * FIX: Thêm tham số $controllerName vào hàm
     */
    private function show404($message, $controllerName = ''): void
    {
        http_response_code(404);
        echo "<div style='text-align:center; padding:100px 20px; font-family: sans-serif; background:#f8fafc; min-height:100vh;'>";
        echo "<h1 style='color: #cbd5e1; font-size: 100px; margin:0; line-height:1;'>404</h1>";
        echo "<h2 style='color: #1e293b; margin-top:20px; text-transform: uppercase;'>Lỗi điều hướng</h2>";
        echo "<div style='color: #64748b; max-width:600px; margin: 20px auto; padding: 25px; background: #fff; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); text-align:left;'>";
        echo $message;
        echo "<hr style='border:0; border-top:1px solid #f1f5f9; margin:20px 0;'>";
        echo "<small><b>Gợi ý:</b> Hãy kiểm tra xem file Controller có đặt tên đúng là <code>" . ($controllerName ?: 'TênController') . ".php</code> (viết hoa chữ đầu) trong thư mục <code>app/controllers/</code> hay không.</small>";
        echo "</div>";
        echo "<a href='".BASE_URL."' style='display:inline-block; background:#2563eb; color:#fff; padding:12px 35px; border-radius:50px; text-decoration:none; font-weight:bold; shadow: 0 10px 15px -3px rgba(37,99,235,0.3);'>Quay về trang chủ</a>";
        echo "</div>";
    }
}