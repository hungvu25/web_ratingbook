# Hệ thống Đánh giá Sách - Book Review Portal

Hệ thống Đánh giá Sách là một nền tảng web cho phép người dùng khám phá, đánh giá và chia sẻ ý kiến về các cuốn sách. Ứng dụng cung cấp trải nghiệm đầy đủ từ việc duyệt danh mục sách đến đánh giá chi tiết và quản lý tài khoản người dùng.

## Chức năng chính

### Dành cho người dùng
1. **Đăng ký và Đăng nhập**
   - Đăng ký tài khoản với xác thực email
   - Đăng nhập an toàn với mật khẩu được mã hóa
   - Khôi phục mật khẩu qua email

2. **Quản lý Hồ sơ**
   - Cập nhật thông tin cá nhân
   - Thay đổi avatar bằng tích hợp ImgBB
   - Đổi mật khẩu

3. **Khám phá Sách**
   - Tìm kiếm sách theo tiêu đề, tác giả
   - Lọc theo thể loại
   - Sắp xếp theo đánh giá, ngày xuất bản

4. **Đánh giá Sách**
   - Đánh giá sách theo thang điểm
   - Viết nhận xét chi tiết
   - Xem lại các đánh giá của mình

### Dành cho Quản trị viên
1. **Quản lý Người dùng**
   - Xem danh sách tất cả người dùng
   - Quản lý trạng thái tài khoản (khóa/mở khóa)
   - Phân quyền người dùng

2. **Quản lý Sách**
   - Thêm/sửa/xóa sách
   - Quản lý ảnh bìa sách
   - Phân loại sách theo thể loại

3. **Quản lý Thể loại**
   - Thêm/sửa/xóa thể loại sách

4. **Quản lý Đánh giá**
   - Kiểm duyệt đánh giá
   - Xóa đánh giá vi phạm

5. **Bảng điều khiển**
   - Thống kê tổng quan về sách, người dùng, đánh giá
   - Biểu đồ và phân tích dữ liệu

## Công nghệ sử dụng

### Backend
- **PHP 8.0+**: Ngôn ngữ lập trình phía máy chủ
- **MySQL/MariaDB**: Hệ quản trị cơ sở dữ liệu
- **PDO**: PHP Data Objects cho kết nối cơ sở dữ liệu an toàn
- **PHPMailer**: Thư viện gửi email với SMTP
- **Session Management**: Quản lý phiên đăng nhập và bảo mật

### Frontend
- **HTML5/CSS3**: Xây dựng giao diện người dùng
- **JavaScript/jQuery**: Tương tác người dùng
- **Bootstrap**: Framework CSS cho giao diện đáp ứng
- **Font Awesome**: Thư viện biểu tượng
- **Roboto Fonts**: Phông chữ chính

### APIs & Tích hợp
- **ImgBB API**: Lưu trữ và quản lý hình ảnh
- **SMTP (Zoho Mail)**: Gửi email thông báo và xác thực

### Bảo mật
- **Password Hashing**: Mã hóa mật khẩu an toàn
- **SQL Injection Prevention**: Sử dụng PDO prepared statements
- **XSS Prevention**: Lọc và kiểm duyệt đầu vào người dùng
- **CSRF Protection**: Bảo vệ chống tấn công giả mạo yêu cầu
- **Email Verification**: Xác thực email cho người dùng mới

## Cấu trúc Cơ sở dữ liệu

### Bảng `users`
- Thông tin người dùng (username, email, mật khẩu, v.v.)
- Quyền và trạng thái tài khoản
- Thông tin xác thực email

### Bảng `books`
- Thông tin sách (tiêu đề, tác giả, mô tả, ảnh bìa)
- Phân loại và ngày tạo

### Bảng `categories`
- Thể loại sách

### Bảng `reviews`
- Đánh giá và nhận xét của người dùng về sách
- Điểm đánh giá và nội dung

## Cách cài đặt

### Yêu cầu hệ thống
- XAMPP/WAMP/LAMP với PHP 8.0+
- MySQL/MariaDB
- Composer (khuyến nghị)

### Các bước cài đặt
1. **Chuẩn bị cơ sở dữ liệu**:
   - Tạo cơ sở dữ liệu `web_ratingbook`
   - Import file `database/schema_local.sql`

2. **Cấu hình hệ thống**:
   - Cập nhật thông tin kết nối cơ sở dữ liệu trong `config/database.php`
   - Cấu hình SMTP cho gửi email trong `includes/email_functions.php`
   - Cập nhật API key ImgBB trong `config/imgbb_config.php`

3. **Cài đặt thư viện**:
   ```bash
   composer install
   ```

4. **Thiết lập quyền truy cập**:
   - Đảm bảo thư mục `uploads` có quyền ghi

5. **Tạo tài khoản admin**:
   - Đăng ký tài khoản
   - Cập nhật quyền admin trong cơ sở dữ liệu:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```

## Tính năng đặc biệt

1. **Hệ thống xác thực email**: Tăng cường bảo mật và xác minh người dùng thực
2. **Tích hợp ImgBB**: Lưu trữ ảnh bìa sách và avatar người dùng
3. **Responsive Design**: Giao diện tương thích với nhiều thiết bị
4. **Hệ thống đánh giá đa dạng**: Cho phép người dùng đánh giá chi tiết với thang điểm và nhận xét

## Liên hệ và Hỗ trợ

Nếu có bất kỳ câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ qua:
- Email: account@dichvutot.site

---
© 2025 Book Review Portal. All rights reserved.
