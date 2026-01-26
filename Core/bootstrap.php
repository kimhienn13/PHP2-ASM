<?php

/**
 * FILE KHỞI TẠO HỆ THỐNG (BOOTSTRAP)
 * Tự động nhận diện thư mục dự án để fix lỗi điều hướng trên Laragon/XAMPP
 * Đã tối ưu hóa để loại bỏ hoàn toàn index.php khỏi BASE_URL
 */

// 1. Định nghĩa các đường dẫn vật lý (Physical Paths)
define('BASE_PATH', dirname(__DIR__, 2));            
define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app');
define('CORE_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Core');
define('VIEW_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'views');
define('STORAGE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'storage');

// 2. Tự động xác định BASE_URL chuẩn xác
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME']; 

/**
 * FIX QUAN TRỌNG:
 * Loại bỏ các thành phần dư thừa trong đường dẫn để lấy thư mục gốc sạch.
 * Ví dụ: Nếu file chạy là /PHP2/public/index.php -> baseDir sẽ là /PHP2
 */
$baseDir = str_replace(['/public/index.php', '/index.php', '/public'], '', $scriptName);
$baseDir = rtrim($baseDir, '/');

// Định nghĩa hằng số BASE_URL dùng xuyên suốt dự án (Không có dấu gạch chéo cuối cùng)
define('BASE_URL', $protocol . "://" . $host . $baseDir);

// 3. Nạp Autoloader của Composer (Nếu có dùng thư viện ngoài)
$autoloadPath = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// 4. Nạp cấu hình từ file .env (Sử dụng cho Model kết nối Database)
if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}

// 5. Nạp các file Core nền tảng (Bắt buộc)
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Model.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Controller.php';
require_once CORE_PATH . DIRECTORY_SEPARATOR . 'Router.php';

// 6. Đăng ký Autoload tự động cho Controller và Model
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR,
        APP_PATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 7. Tự động tạo thư mục cache cho Blade Engine nếu chưa tồn tại
$cachePath = STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';
if (!file_exists($cachePath)) {
    mkdir($cachePath, 0777, true);
}