<?php
require_once 'config/database.php';

echo "<h2>🔍 Kiểm tra cấu trúc Database</h2>";
echo "<hr>";

try {
    // Kiểm tra cấu trúc bảng users
    echo "<h3>📋 Cấu trúc bảng 'users':</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Kiểm tra dữ liệu users mẫu
    echo "<h3>👥 Dữ liệu users mẫu:</h3>";
    $stmt = $pdo->query("SELECT * FROM users LIMIT 3");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        $firstUser = $users[0];
        echo "<tr>";
        foreach (array_keys($firstUser) as $key) {
            if (!is_numeric($key)) {
                echo "<th>$key</th>";
            }
        }
        echo "</tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            foreach ($user as $key => $value) {
                if (!is_numeric($key)) {
                    if ($key === 'password') {
                        echo "<td>" . substr($value, 0, 20) . "...</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không có dữ liệu users</p>";
    }
    
    // Kiểm tra tất cả bảng
    echo "<h3>🗂️ Tất cả bảng trong database:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        // Đếm số records
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countStmt->fetch()['count'];
        echo "<li><strong>$table</strong> - $count records</li>";
    }
    echo "</ul>";
    
    // Gợi ý fix
    echo "<h3>🔧 Gợi ý sửa lỗi:</h3>";
    $userColumns = array_column($columns, 'Field');
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    
    if (in_array('full_name', $userColumns)) {
        echo "✅ Có column 'full_name' - Dashboard sẽ hoạt động bình thường<br>";
    } elseif (in_array('name', $userColumns)) {
        echo "⚠️ Có column 'name' thay vì 'full_name' - Cần update query<br>";
        echo "💡 Thay 'u.full_name' thành 'u.name' trong admin/dashboard.php<br>";
    } elseif (in_array('username', $userColumns)) {
        echo "⚠️ Chỉ có column 'username' - Cần update query<br>";
        echo "💡 Thay 'u.full_name' thành 'u.username' trong admin/dashboard.php<br>";
    } else {
        echo "❌ Không tìm thấy column tên phù hợp<br>";
    }
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}

echo "<hr>";
echo "<p><a href='admin/dashboard.php'>🔙 Quay lại Dashboard</a> | <a href='test_connection.php'>🔗 Test Connection</a></p>";
?> 