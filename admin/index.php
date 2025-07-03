<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirectWithMessage('../login.php', 'Bạn cần đăng nhập với quyền admin để truy cập trang này', 'error');
}

// Redirect to dashboard
header('Location: dashboard.php');
exit;
?> 