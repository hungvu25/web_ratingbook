<?php
// Test kết nối database với Environment Variables
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
require_once __DIR__ . '/env.php';

echo "<h2>🔍 Test Kết Nối Database với Environment Variables</h2>";
echo "<hr>";

// Thông tin kết nối từ environment variables
$host = env('DB_HOST', 'localhost');
$port = env('DB_PORT', 3306);
$dbname = env('DB_NAME', 'web_ratingbook');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');

echo "<h3>📋 Thông tin kết nối:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Port:</strong> $port</li>";
echo "<li><strong>Database:</strong> $dbname</li>";
echo "<li><strong>Username:</strong> $user</li>";
echo "<li><strong>Password:</strong> " . ($pass ? str_repeat('*', min(strlen($pass), 10)) : 'No password') . "</li>";
echo "<li><strong>Environment:</strong> " . env('ENVIRONMENT', 'development') . "</li>";
echo "</ul>";

// Kiểm tra nếu .env file tồn tại
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>File .env đã được tìm thấy và load thành công!</strong>";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "⚠️ <strong>File .env không tồn tại.</strong> Đang sử dụng default values hoặc system environment variables.";
    echo "</div>";
}

echo "<h3>🔌 Kiểm tra kết nối:</h3>";

try {
    // Tạo DSN
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    // Cấu hình PDO với SSL (nếu cần cho cloud database)
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => false
    ];
    
    echo "⏳ Đang kết nối...<br>";
    
    // Tạo kết nối PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "✅ <strong style='color: green;'>Kết nối thành công!</strong><br><br>";
    
    // Test một số query cơ bản
    echo "<h3>📊 Kiểm tra database:</h3>";
    
    // Kiểm tra các tables
    echo "<h4>🗂️ Danh sách tables:</h4>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Chưa có tables nào. Bạn cần chạy file aiven_schema.sql</p>";
    }
    
    // Nếu có tables, kiểm tra dữ liệu
    if (in_array('categories', $tables)) {
        echo "<h4>📚 Dữ liệu categories:</h4>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $count = $stmt->fetch()['count'];
        echo "<p>Có <strong>$count</strong> categories trong database</p>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM categories LIMIT 5");
            $categories = $stmt->fetchAll();
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Tên</th><th>Mô tả</th></tr>";
            foreach ($categories as $cat) {
                echo "<tr>";
                echo "<td>{$cat['id']}</td>";
                echo "<td>{$cat['name']}</td>";
                echo "<td>" . substr($cat['description'], 0, 50) . "...</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    if (in_array('books', $tables)) {
        echo "<h4>📖 Dữ liệu books:</h4>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
        $count = $stmt->fetch()['count'];
        echo "<p>Có <strong>$count</strong> books trong database</p>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM books LIMIT 3");
            $books = $stmt->fetchAll();
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Tên sách</th><th>Tác giả</th><th>Thể loại</th></tr>";
            foreach ($books as $book) {
                echo "<tr>";
                echo "<td>{$book['id']}</td>";
                echo "<td>{$book['title']}</td>";
                echo "<td>{$book['author']}</td>";
                echo "<td>{$book['category_id']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    if (in_array('users', $tables)) {
        echo "<h4>👥 Dữ liệu users:</h4>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch()['count'];
        echo "<p>Có <strong>$count</strong> users trong database</p>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users");
            $users = $stmt->fetchAll();
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Tạo lúc</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['username']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['role']}</td>";
                echo "<td>{$user['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h3>🎉 Kết luận:</h3>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
    echo "<strong style='color: #155724;'>✅ Database đã sẵn sàng sử dụng với Environment Variables!</strong><br>";
    echo "Thông tin nhạy cảm đã được bảo vệ và không còn hard-code trong source code.";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>Lỗi kết nối:</strong><br>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Code:</strong> " . $e->getCode() . "<br>";
    echo "</div>";
    
    echo "<h3>🔧 Cách khắc phục:</h3>";
    echo "<ol>";
    echo "<li>Kiểm tra file .env có đúng thông tin database không</li>";
    echo "<li>Đảm bảo database đã được tạo</li>";
    echo "<li>Kiểm tra firewall/IP whitelist (nếu dùng cloud database)</li>";
    echo "<li>Kiểm tra SSL certificate settings</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<h3>📝 Hướng dẫn sử dụng Environment Variables:</h3>";
echo "<div style='background: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; border-radius: 5px;'>";
echo "<ol>";
echo "<li>Tạo file <code>.env</code> từ file <code>env.example</code></li>";
echo "<li>Cập nhật thông tin database trong file <code>.env</code></li>";
echo "<li>File <code>.env</code> sẽ không được push lên Git (đã có trong .gitignore)</li>";
echo "<li>Trên server production, tạo file <code>.env</code> với thông tin thật</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><a href='../index.php'>🏠 Về trang chủ</a> | <a href='database.php'>⚙️ Xem config</a></p>";
?> 