<?php
require_once 'config/database.php';

// Tạo hash mật khẩu mới cho admin123
$new_password_hash = password_hash('admin123', PASSWORD_DEFAULT);

try {
    // Cập nhật mật khẩu admin
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@ratingbook.com'");
    $result = $stmt->execute([$new_password_hash]);
    
    if ($result) {
        echo "✅ Đã cập nhật mật khẩu admin thành công!<br>";
        echo "Email: admin@ratingbook.com<br>";
        echo "Password: admin123<br>";
        echo "<br>Hash mới: " . $new_password_hash . "<br>";
        
        // Kiểm tra lại
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@ratingbook.com'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user && password_verify('admin123', $user['password'])) {
            echo "<br>✅ Xác thực mật khẩu thành công!";
        } else {
            echo "<br>❌ Vẫn có lỗi với mật khẩu";
        }
    } else {
        echo "❌ Lỗi khi cập nhật mật khẩu";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}

echo "<br><br><a href='login.php'>Đi đến trang đăng nhập</a>";
?> 