<?php
/**
 * charset_fix.php - Tệp riêng để xử lý vấn đề với bảng mã UTF-8 trong database
 */

// Thiết lập encoding cho PHP script
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

// Đảm bảo output charset
header('Content-Type: text/html; charset=utf-8');

/**
 * Đảm bảo dữ liệu tiếng Việt được hiển thị và lưu trữ đúng cách
 * @param PDO $pdo
 */
function fixDatabaseEncoding($pdo) {
    if (!$pdo) return;
    
    try {
        // Đặt charset cho connection - quan trọng để làm việc với tiếng Việt
        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("SET character_set_connection = utf8mb4");
        $pdo->exec("SET character_set_client = utf8mb4");
        $pdo->exec("SET character_set_results = utf8mb4");
        
        // Đặt collation để hỗ trợ đầy đủ Unicode và tiếng Việt
        $pdo->exec("SET collation_connection = utf8mb4_unicode_ci");
        $pdo->exec("SET SESSION collation_connection = utf8mb4_unicode_ci");
        
        // Thêm cài đặt cho TIMESTAMP sử dụng đúng timezone
        $pdo->exec("SET time_zone = '+07:00'");
    } catch (PDOException $e) {
        // Ghi log lỗi nếu cần
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            error_log("Error setting database encoding: " . $e->getMessage());
        }
    }
}

/**
 * Chuyển đổi chuỗi sang UTF-8 nếu cần
 * @param string $text
 * @return string
 */
function ensureUtf8($text) {
    if (!is_string($text)) return $text;
    
    // Kiểm tra và chuyển đổi sang UTF-8 nếu cần
    if (!mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true));
    }
    
    // Loại bỏ các ký tự điều khiển không hợp lệ
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);
    
    return $text;
}

/**
 * Xử lý mảng dữ liệu từ database để đảm bảo encoding đúng
 * @param mixed $data
 * @return mixed
 */
function sanitizeDataFromDb($data) {
    if (!is_array($data)) {
        return ensureUtf8($data);
    }
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = sanitizeDataFromDb($value);
        } else if (is_string($value)) {
            $data[$key] = ensureUtf8($value);
        }
    }
    
    return $data;
}

/**
 * Xử lý văn bản hiển thị an toàn
 * @param string $text
 * @return string
 */
function safeText($text) {
    return htmlspecialchars(ensureUtf8($text), ENT_QUOTES, 'UTF-8');
}
