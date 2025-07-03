<?php
require_once 'config/database.php';

echo "<h2>🔧 Sửa chữa Database Schema</h2>";
echo "<hr>";

try {
    // Kiểm tra bảng users
    echo "<h3>📋 Kiểm tra bảng users...</h3>";
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll();
    $columnNames = array_column($columns, 'Field');
    
    echo "<p>Columns hiện tại: <strong>" . implode(', ', $columnNames) . "</strong></p>";
    
    $needsFix = false;
    $fixQueries = [];
    
    // Kiểm tra các column cần thiết
    if (!in_array('full_name', $columnNames) && !in_array('name', $columnNames) && !in_array('username', $columnNames)) {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>Vấn đề:</strong> Không tìm thấy column tên người dùng phù hợp!<br>";
        echo "📝 <strong>Giải pháp:</strong> Thêm column 'full_name' vào bảng users";
        echo "</div>";
        
        $fixQueries[] = "ALTER TABLE users ADD COLUMN full_name VARCHAR(100) AFTER email";
        $needsFix = true;
    } elseif (!in_array('full_name', $columnNames)) {
        if (in_array('name', $columnNames)) {
            echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "⚠️ <strong>Thông báo:</strong> Có column 'name' nhưng không có 'full_name'<br>";
            echo "📝 <strong>Gợi ý:</strong> Rename 'name' thành 'full_name' cho consistency";
            echo "</div>";
            
            $fixQueries[] = "ALTER TABLE users CHANGE name full_name VARCHAR(100)";
        } elseif (in_array('username', $columnNames)) {
            echo "<div style='background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "ℹ️ <strong>Thông báo:</strong> Có column 'username' - sẽ sử dụng làm display name<br>";
            echo "📝 <strong>Gợi ý:</strong> Thêm 'full_name' để có tên đầy đủ";
            echo "</div>";
            
            $fixQueries[] = "ALTER TABLE users ADD COLUMN full_name VARCHAR(100) AFTER username";
        }
    } else {
        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>OK:</strong> Bảng users có cấu trúc hợp lệ với column 'full_name'";
        echo "</div>";
    }
    
    // Kiểm tra admin user
    echo "<h3>👤 Kiểm tra admin user...</h3>";
    $adminStmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $adminUser = $adminStmt->fetch();
    
    if (!$adminUser) {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>Vấn đề:</strong> Không tìm thấy admin user!<br>";
        echo "📝 <strong>Giải pháp:</strong> Tạo admin user mới";
        echo "</div>";
        
        $nameColumn = in_array('full_name', $columnNames) ? 'full_name' : 
                      (in_array('name', $columnNames) ? 'name' : 'username');
        
        $fixQueries[] = "INSERT INTO users (username, email, password, $nameColumn, role, email_verified) VALUES ('admin', 'admin@bookstore.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin', TRUE)";
        $needsFix = true;
    } else {
        echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>OK:</strong> Đã có admin user";
        echo "</div>";
    }
    
    // Hiển thị các fix queries
    if (!empty($fixQueries)) {
        echo "<h3>🔧 Các lệnh SQL để sửa:</h3>";
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;'>";
        foreach ($fixQueries as $query) {
            echo htmlspecialchars($query) . ";<br><br>";
        }
        echo "</div>";
        
        // Option to auto-fix
        if (isset($_POST['auto_fix'])) {
            echo "<h3>⚡ Đang thực hiện auto-fix...</h3>";
            $success = true;
            foreach ($fixQueries as $query) {
                try {
                    $pdo->exec($query);
                    echo "✅ " . htmlspecialchars($query) . "<br>";
                } catch (PDOException $e) {
                    echo "❌ " . htmlspecialchars($query) . " - Lỗi: " . $e->getMessage() . "<br>";
                    $success = false;
                }
            }
            
            if ($success) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
                echo "<strong>🎉 Đã sửa xong! Database đã được cập nhật.</strong><br>";
                echo "Bây giờ bạn có thể truy cập admin dashboard bình thường.";
                echo "</div>";
            }
        } else {
            echo "<form method='POST' style='margin: 15px 0;'>";
            echo "<button type='submit' name='auto_fix' class='btn btn-warning' style='background: #ffc107; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;'>";
            echo "🔧 Tự động sửa chữa database";
            echo "</button>";
            echo "</form>";
            
            echo "<p><small>⚠️ Lưu ý: Backup database trước khi chạy auto-fix</small></p>";
        }
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
        echo "<strong>🎉 Database đã hoàn hảo! Không cần sửa gì thêm.</strong>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "❌ <strong>Lỗi kết nối database:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "<hr>";
echo "<p>";
echo "<a href='admin/dashboard.php' style='text-decoration: none; margin-right: 15px;'>🏠 Dashboard</a>";
echo "<a href='test_connection.php' style='text-decoration: none; margin-right: 15px;'>🔗 Test Connection</a>";
echo "<a href='check_database_structure.php' style='text-decoration: none;'>🔍 Check Structure</a>";
echo "</p>";
?> 