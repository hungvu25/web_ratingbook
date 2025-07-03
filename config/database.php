<?php
// Cấu hình database cho hosting online
// Thay đổi các thông tin này theo database của bạn

// Cấu hình database - Aiven Cloud
define('DB_HOST', 'learnenglish-dental-st.b.aivencloud.com');
define('DB_PORT', 13647);
define('DB_NAME', 'web_ratingbook');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'AVNS_PABpPxTbYo7xMw3ictV');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình site
define('SITE_NAME', 'BookReview');
define('SITE_URL', 'https://yourdomain.com'); // URL website của bạn
define('SITE_DESCRIPTION', 'Đánh giá và chia sẻ những cuốn sách tuyệt vời nhất');

// Error reporting (tắt trên production)
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    // Development environment
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('ENVIRONMENT', 'development');
} else {
    // Production environment
    error_reporting(0);
    ini_set('display_errors', 0);
    define('ENVIRONMENT', 'production');
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
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
