<?php
// 1. PHẢI CÓ DÒNG NÀY ĐẦU TIÊN để hệ thống nhớ trạng thái đăng nhập
session_start();

// 2. Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 3. Nạp file bootstrap
require_once dirname(__DIR__) . '/app/Core/bootstrap.php';

// 4. Khởi chạy Router
try {
    $router = new Router();
    $router->dispatch($_SERVER["REQUEST_URI"]);
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Lỗi hệ thống</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}