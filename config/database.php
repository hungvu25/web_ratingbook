<?php
/**
 * Database Configuration
 * Direct configuration without environment variables
 */

// Load charset fix functions
require_once __DIR__ . '/charset_fix.php';

// Database configuration - Auto-detect environment
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    
    if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false || strpos($host, 'localhost:') !== false) {
        // Local development configuration
        define('DB_HOST', 'localhost');
        define('DB_PORT', 3306);
        define('DB_NAME', 'web_ratingbook'); // Local database name
        define('DB_USER', 'root');
        define('DB_PASS', ''); // XAMPP default
        define('DB_CHARSET', 'utf8mb4');
        define('DB_COLLATION', 'utf8mb4_unicode_ci');
        if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
    } else {
        // InfinityFree production configuration
        define('DB_HOST', 'sql106.infinityfree.com');
        define('DB_PORT', 3306);
        define('DB_NAME', 'if0_39381228_webbook');
        define('DB_USER', 'if0_39381228');
        define('DB_PASS', 'oi14KDGIotsJiP');
        define('DB_CHARSET', 'utf8mb4');
        define('DB_COLLATION', 'utf8mb4_unicode_ci');
        if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
    }
} else {
    // Fallback - assume production
    define('DB_HOST', 'sql106.infinityfree.com');
    define('DB_PORT', 3306);
    define('DB_NAME', 'if0_39381228_webbook');
    define('DB_USER', 'if0_39381228');
    define('DB_PASS', 'oi14KDGIotsJiP');
    define('DB_CHARSET', 'utf8mb4');
    define('DB_COLLATION', 'utf8mb4_unicode_ci');
    if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
}

// Site configuration for InfinityFree
define('SITE_NAME', 'BookReview');

// Auto-detect SITE_URL based on server
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false || strpos($host, 'localhost:') !== false) {
        define('SITE_URL', 'http://localhost/');
        if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
    } else {
        define('SITE_URL', 'https://sachzone.infy.uk/');
        if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
    }
} else {
    // Fallback for command line or other contexts
    define('SITE_URL', 'https://sachzone.infy.uk/');
    if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
}

define('SITE_DESCRIPTION', 'Đánh giá và chia sẻ những cuốn sách tuyệt vời nhất');

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Database connection for InfinityFree
$pdo = null;

try {
    // Thử kết nối đơn giản hơn
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 60, // Tăng timeout lên 60s
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Áp dụng hàm sửa lỗi charset từ charset_fix.php
    fixDatabaseEncoding($pdo);
    
    define('DB_CONNECTION_TYPE', 'infinityfree');
    
} catch (PDOException $e) {
    // Error handling với thông tin chi tiết
    if (ENVIRONMENT === 'development') {
        die("Database connection failed: " . $e->getMessage() . "<br>DSN: " . $dsn . "<br>User: " . DB_USER . "<br>Host: " . DB_HOST);
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Test connection function
function testDatabaseConnection() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT 1 as test");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

?>
