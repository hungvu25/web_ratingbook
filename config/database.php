<?php
// Load environment variables
require_once __DIR__ . '/env.php';

// Cấu hình database từ environment variables
define('DB_HOST', env('DB_HOST'));
define('DB_PORT', env('DB_PORT', 3306));
define('DB_NAME', env('DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASS', env('DB_PASS'));
define('DB_CHARSET', env('DB_CHARSET'));

// Cấu hình site
define('SITE_NAME', env('SITE_NAME', 'BookReview'));
define('SITE_URL', env('SITE_URL', 'http://localhost')); 
define('SITE_DESCRIPTION', env('SITE_DESCRIPTION', 'Đánh giá và chia sẻ những cuốn sách tuyệt vời nhất'));

// Environment
define('ENVIRONMENT', env('ENVIRONMENT', 'development'));

// Error reporting
if (ENVIRONMENT === 'development' || $_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Development environment
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Production environment
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kết nối database sử dụng PDO với SSL cho Aiven Cloud
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => false
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Hàm kiểm tra kết nối
function testDatabaseConnection() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Security configurations
if (ENVIRONMENT === 'production') {
    // Bảo mật cho production
    ini_set('session.cookie_httponly', env('SESSION_HTTPONLY', true));
    ini_set('session.cookie_secure', env('SESSION_SECURE', false));
    ini_set('session.use_only_cookies', 1);
}

// Start session - chỉ khi cần thiết, không tự động
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
?>
