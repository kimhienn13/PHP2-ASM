<?php

use App\Core\Router;

// 1. Khởi động Session
session_start();

// 2. Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3. Định nghĩa đường dẫn gốc (ROOT)
define('ROOT_DIR', dirname(__DIR__));

// 4. Nạp autoload của Composer (QUAN TRỌNG: để tự động nạp các class có Namespace)
if (file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    require_once ROOT_DIR . '/vendor/autoload.php';
} else {
    // Fallback nếu không dùng composer (tùy cấu trúc cũ của bạn)
    require_once ROOT_DIR . '/Core/bootstrap.php';
}

// 5. Khởi chạy Router
try {
    // Kiểm tra xem class Router có tồn tại không trước khi gọi
    if (!class_exists(Router::class)) {
        throw new Exception("Không tìm thấy class App\Core\Router. Hãy kiểm tra file Core/Router.php có namespace App\Core; chưa và đã chạy 'composer dump-autoload' chưa.");
    }

    $router = new Router();
    $router->dispatch($_SERVER["REQUEST_URI"]);

} catch (Exception $e) {
    http_response_code(500);
    echo "<div style='background:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif; border:1px solid #f87171; border-radius:8px; max-width:800px; margin:50px auto;'>";
    echo "<h2 style='margin-top:0'>❌ Lỗi Hệ Thống</h2>";
    echo "<p><b>Thông báo:</b> " . $e->getMessage() . "</p>";
    echo "<p><b>File:</b> " . $e->getFile() . " (Dòng: " . $e->getLine() . ")</p>";
    echo "</div>";
}