# Thiết lập Xác minh Email và Chào mừng người dùng

## Tổng quan
Hệ thống này được thiết lập để gửi hai loại email khi người dùng đăng ký:
1. Email xác minh (verification email) - để xác nhận email của người dùng
2. Email chào mừng (welcome email) - gửi sau khi người dùng đã xác minh email thành công

## Cài đặt

### 1. Cài đặt PHPMailer
Đã thêm PHPMailer vào file `composer.json`. Chạy lệnh sau để cài đặt:

```bash
composer update
```

hoặc

```bash
composer install
```

### 2. Cấu hình cơ sở dữ liệu
Chạy file SQL sau để cập nhật cấu trúc bảng users:

```sql
-- Add is_verified field to users table
ALTER TABLE `users` 
ADD COLUMN `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- Rename email_verified_at to verified_at (nếu cần)
ALTER TABLE `users` 
CHANGE COLUMN `email_verified_at` `verified_at` TIMESTAMP NULL DEFAULT NULL;

-- Update existing users to be verified
UPDATE `users` SET `is_verified` = 1 WHERE `role` = 'admin';
```

### 3. Cấu hình email Zoho
Mở file `includes/email_functions.php` và cập nhật các thông tin sau:

```php
define('EMAIL_HOST', 'smtp.zoho.com');
define('EMAIL_PORT', 587); // hoặc 465 cho SSL
define('EMAIL_USERNAME', 'your-email@zoho.com'); // Email Zoho của bạn
define('EMAIL_PASSWORD', 'your-app-password'); // Mật khẩu ứng dụng (App password) từ Zoho
define('EMAIL_FROM_NAME', 'Book Review Portal'); // Tên hiển thị
define('EMAIL_FROM_ADDRESS', 'your-email@zoho.com'); // Email Zoho của bạn
```

Lưu ý: Bạn cần tạo App Password trong tài khoản Zoho Mail để sử dụng SMTP.

## Các file đã cập nhật/tạo mới
1. **includes/email_functions.php** - Các hàm gửi email
2. **verify-email.php** - Xử lý việc xác minh email
3. **register.php** - Đã cập nhật để gửi email xác minh
4. **login.php** - Đã cập nhật để kiểm tra trạng thái xác minh

## Quy trình hoạt động
1. Người dùng đăng ký tài khoản
2. Hệ thống gửi email xác minh
3. Người dùng nhấp vào liên kết trong email để xác minh
4. Hệ thống xác minh token và cập nhật trạng thái người dùng
5. Hệ thống gửi email chào mừng
6. Người dùng có thể đăng nhập

## Lưu ý về bảo mật
- Token xác minh được tạo bằng hàm `random_bytes(32)` đảm bảo tính an toàn
- Token được xóa sau khi người dùng xác minh thành công
- Liên kết xác minh chỉ hoạt động một lần

## Xử lý lỗi
- Nếu email không gửi được, lỗi sẽ được ghi vào error log
- Người dùng sẽ nhận được thông báo phù hợp
