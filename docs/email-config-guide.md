# Hướng dẫn Cấu hình Email trong XAMPP

## Vấn đề và Giải pháp

Khi sử dụng hệ thống xác minh email trong XAMPP, bạn có thể gặp phải một số vấn đề vì mặc định XAMPP không cấu hình sẵn để gửi email. Có một số cách để giải quyết vấn đề này:

## Giải pháp 1: Sử dụng PHPMailer với SMTP của Zoho

Giải pháp này đã được triển khai trong hệ thống. Thông tin đăng nhập của bạn đã được cấu hình trong file `includes/email_functions.php`:

```php
define('EMAIL_HOST', 'smtp.zoho.com');
define('EMAIL_PORT', 587); // or 465 for SSL
define('EMAIL_USERNAME', 'account@dichvutot.site');
define('EMAIL_PASSWORD', '#4Vrorcl');
define('EMAIL_FROM_NAME', 'Book Review Portal');
define('EMAIL_FROM_ADDRESS', 'account@dichvutot.site');
```

Nếu không gửi được email, có thể do:
1. Tài khoản Zoho cần được kích hoạt hoặc xác minh
2. Mật khẩu không chính xác hoặc bạn cần dùng "App Password" thay vì mật khẩu chính
3. Port 587 hoặc 465 bị chặn bởi firewall

## Giải pháp 2: Cấu hình sendmail trong XAMPP

1. Tìm file `php.ini` trong thư mục `xampp/php`
2. Tìm phần `[mail function]` và cấu hình như sau:

```ini
[mail function]
SMTP = localhost
smtp_port = 25
sendmail_from = your-email@example.com
sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"
```

3. Tiếp theo, mở file `sendmail.ini` trong thư mục `xampp/sendmail` và cấu hình:

```ini
[sendmail]
smtp_server=smtp.zoho.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=account@dichvutot.site
auth_password=#4Vrorcl
force_sender=account@dichvutot.site
```

4. Khởi động lại Apache

## Giải pháp 3: Sử dụng MailHog (cho môi trường phát triển)

[MailHog](https://github.com/mailhog/MailHog) là một email testing tool rất hữu ích cho việc phát triển:

1. Tải MailHog từ [https://github.com/mailhog/MailHog/releases](https://github.com/mailhog/MailHog/releases)
2. Chạy MailHog.exe
3. Cấu hình PHP để sử dụng MailHog:

```ini
[mail function]
SMTP = localhost
smtp_port = 1025
```

4. Truy cập giao diện web của MailHog tại [http://localhost:8025](http://localhost:8025) để xem email đã gửi

## Giải pháp 4: Không sử dụng email, dùng phương pháp thay thế

Đã có sẵn 2 phương pháp thay thế không cần gửi email:

1. **Kích hoạt tự động**: Admin có thể kích hoạt tài khoản người dùng thông qua trang `manual-email.php`

2. **Kích hoạt thông qua link**: Người dùng có thể kích hoạt tài khoản thông qua link trực tiếp trên trang `check-account.php`

## Kiểm tra Hệ thống Email

Trang `email-test.php` trong thư mục admin giúp bạn kiểm tra việc gửi email và xem các thông tin cấu hình email của hệ thống.

## Lỗi thường gặp

1. **Không thể gửi email**: Kiểm tra log lỗi trong thư mục `xampp/php/logs` hoặc `xampp/apache/logs`

2. **Email được gửi nhưng không nhận được**: Kiểm tra thư mục spam/rác

3. **PHPMailer không hoạt động**: Kiểm tra thông tin đăng nhập SMTP

4. **Lỗi SMTP connect() failed**: Có thể do port bị chặn, thử đổi sang port khác (587 hoặc 465)
