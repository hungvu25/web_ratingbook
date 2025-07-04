<?php
/**
 * ImgBB API Configuration
 * 
 * Để sử dụng tính năng upload avatar:
 * 1. Đăng ký tài khoản tại https://imgbb.com/
 * 2. Vào https://api.imgbb.com/ để lấy API key
 * 3. Thay thế 'your_imgbb_api_key_here' bằng API key thực của bạn
 */

// ImgBB API Key - QUAN TRỌNG: Thay thế bằng API key thực của bạn
define('IMGBB_API_KEY', '77e2e30d9f873158f6e93e44cc303cb8');

// ImgBB API Endpoint
define('IMGBB_API_URL', 'https://api.imgbb.com/1/upload');

// Upload settings
define('AVATAR_MAX_SIZE', 16 * 1024 * 1024); // 16MB - giới hạn của ImgBB
define('AVATAR_MAX_WIDTH', 300);  // Chiều rộng tối đa cho avatar
define('AVATAR_MAX_HEIGHT', 300); // Chiều cao tối đa cho avatar

// Định dạng file được phép
$ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/gif',
    'image/webp'
];

// Default avatar URL (nếu user chưa có avatar)
define('DEFAULT_AVATAR_URL', 'uploads/default-avatar.png');
?> 