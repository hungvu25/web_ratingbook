<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Log activity trước khi logout
if (isLoggedIn()) {
    logActivity('user_logout', 'User logged out');
}

// Xóa tất cả session data
session_unset();
session_destroy();

// Tạo session mới và thông báo
session_start();
redirectWithMessage('index.php', 'Đã đăng xuất thành công!', 'success');
?> 