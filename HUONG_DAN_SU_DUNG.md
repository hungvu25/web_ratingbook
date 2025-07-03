# 📚 HƯỚNG DẪN SỬ DỤNG WEBSITE ĐÁNH GIÁ SÁCH

## 🚀 Cài đặt và khởi chạy

### 1. **Chuẩn bị database**
```bash
# Truy cập Aiven Console hoặc phpMyAdmin
# Copy và chạy nội dung file: aiven_schema.sql
```

### 2. **Cấu hình kết nối database**
Cập nhật file `config/database.php` với thông tin database của bạn:
```php
define('DB_HOST', 'your-host');
define('DB_PORT', 13647);
define('DB_NAME', 'your-database');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');
```

### 3. **Test kết nối**
Truy cập: `http://localhost/test_connection.php`

### 4. **Truy cập website**
- **Trang chủ:** `http://localhost/index.php`
- **Admin:** `http://localhost/admin/`

---

## 👤 TÀI KHOẢN DEMO

### **Admin**
- **Username:** `admin`
- **Password:** `admin123`
- **Quyền:** Quản lý toàn bộ hệ thống

---

## 🏠 TRANG CHỦ (index.php)

### **Tính năng chính:**
- ✅ Hiển thị sách nổi bật (theo rating cao nhất)
- ✅ Hiển thị sách mới nhất
- ✅ Thống kê tổng quan (số sách, user, đánh giá, thể loại)
- ✅ Giao diện responsive với Bootstrap 5

### **Cách sử dụng:**
1. Xem danh sách sách nổi bật
2. Click "Chi tiết" để xem thông tin sách
3. Đăng ký/đăng nhập để tương tác

---

## 🔐 HỆ THỐNG ĐĂNG NHẬP

### **Đăng ký (register.php)**
- Username (duy nhất)
- Email (duy nhất) 
- Họ tên
- Mật khẩu (tối thiểu 6 ký tự)

### **Đăng nhập (login.php)**
- Dùng username hoặc email
- Mật khẩu
- Tự động chuyển hướng theo role (admin/user)

### **Đăng xuất (logout.php)**
- Xóa session và chuyển về trang chủ

---

## 👨‍💼 TRANG QUẢN TRỊ ADMIN

### **Truy cập:** `/admin/`
**Yêu cầu:** Đăng nhập với quyền admin

### **Dashboard (admin/dashboard.php)**
- 📊 Thống kê tổng quan
- 📚 Danh sách sách mới nhất
- ⭐ Đánh giá mới nhất
- 📈 Biểu đồ và số liệu

### **Quản lý sách (admin/books.php)**
- ➕ Thêm sách mới
- ✏️ Sửa thông tin sách
- 🗑️ Xóa sách
- 📋 Danh sách tất cả sách
- 🔍 Tìm kiếm và lọc

### **Quản lý thể loại (admin/categories.php)**
- ➕ Thêm thể loại mới
- ✏️ Sửa thể loại
- 🗑️ Xóa thể loại (nếu không có sách sử dụng)
- 📊 Thống kê số sách theo thể loại

### **Quản lý người dùng (admin/users.php)**
- 👥 Danh sách tất cả user
- 🔧 Sửa thông tin user
- 🚫 Khóa/mở khóa tài khoản
- 👤 Thay đổi role (user/admin)

### **Quản lý đánh giá (admin/reviews.php)**
- ⭐ Danh sách tất cả đánh giá
- 🗑️ Xóa đánh giá không phù hợp
- 📊 Thống kê rating

---

## 📚 QUẢN LÝ SÁCH

### **Thêm sách mới (add-book.php)**
**Thông tin bắt buộc:**
- Tên sách
- Tác giả
- Mô tả
- Thể loại

**Thông tin tùy chọn:**
- Ảnh bìa (JPG, PNG, GIF, WEBP - tối đa 5MB)
- ISBN
- Năm xuất bản
- Nhà xuất bản
- Số trang
- Ngôn ngữ

### **Quy trình thêm sách:**
1. Admin đăng nhập
2. Vào "Thêm sách mới" từ sidebar
3. Điền thông tin sách
4. Upload ảnh bìa (nếu có)
5. Click "Thêm sách"
6. **➡️ Dữ liệu được lưu vào database qua PHP, KHÔNG phải schema SQL!**

---

## ⭐ HỆ THỐNG ĐÁNH GIÁ

### **Viết đánh giá:**
- Chọn sách
- Đánh giá 1-5 sao
- Viết nhận xét
- Lưu đánh giá

### **Xem đánh giá:**
- Hiển thị rating trung bình
- Đếm số lượt đánh giá
- Danh sách chi tiết đánh giá

---

## 🗂️ CẤU TRÚC DATABASE

### **Tables chính:**
```sql
categories     - Thể loại sách
users         - Người dùng
books         - Thông tin sách  
reviews       - Đánh giá sách
review_likes  - Like đánh giá
reading_lists - Danh sách đọc
activity_logs - Log hoạt động admin
```

### **Mối quan hệ:**
- `books.category_id` → `categories.id`
- `reviews.book_id` → `books.id` 
- `reviews.user_id` → `users.id`
- `review_likes.review_id` → `reviews.id`

---

## 🔧 WORKFLOW DỮ LIỆU

### **🔄 Quy trình thêm dữ liệu:**

1. **Setup ban đầu (1 lần duy nhất):**
   ```sql
   -- Chạy aiven_schema.sql để tạo cấu trúc
   CREATE TABLE categories...
   CREATE TABLE users...
   INSERT INTO categories VALUES...
   ```

2. **Sử dụng hàng ngày:**
   ```php
   // User đăng ký → PHP thêm vào bảng users
   INSERT INTO users (...) VALUES (...)
   
   // Admin thêm sách → PHP thêm vào bảng books  
   INSERT INTO books (...) VALUES (...)
   
   // User viết review → PHP thêm vào bảng reviews
   INSERT INTO reviews (...) VALUES (...)
   ```

### **⚠️ Lưu ý quan trọng:**
- **Schema SQL chỉ chạy 1 lần** khi setup ban đầu
- **Dữ liệu mới được PHP quản lý** thông qua các form và tương tác user
- **KHÔNG bao giờ chạy lại schema SQL** sau khi đã có dữ liệu

---

## 🎨 GIAO DIỆN VÀ UX

### **Frontend:**
- **Framework:** Bootstrap 5
- **Icons:** Font Awesome 6
- **Fonts:** Google Fonts (Poppins)
- **Responsive:** Mobile-first design

### **Tính năng UX:**
- ✨ Hover effects trên cards
- 🔄 Loading states
- 📱 Mobile-friendly navigation
- 🎯 Auto-hide alerts
- 🔍 Search functionality
- 📄 Pagination
- 🌙 Dark/Light mode ready

---

## 🚀 TỐI ƯU HÓA & BẢO MẬT

### **Performance:**
- 🗂️ Database indexing
- 📊 Optimized queries
- 🖼️ Image optimization
- 💾 Session management

### **Security:**
- 🔐 Password hashing (PHP password_hash)
- 🛡️ SQL injection prevention (PDO prepared statements)  
- 🔒 XSS protection (htmlspecialchars)
- 🎫 CSRF token protection
- 📝 Input validation và sanitization

---

## 🔧 TROUBLESHOOTING

### **Lỗi kết nối database:**
```
Access denied for user 'bookuser'@'localhost'
```
**Giải pháp:**
1. Kiểm tra thông tin kết nối trong `config/database.php`
2. Chạy `test_connection.php` để verify
3. Đảm bảo database và user đã được tạo

### **Lỗi "Call to a member function prepare() on null":**
**Nguyên nhân:** `$pdo` chưa được khởi tạo
**Giải pháp:**
1. Kiểm tra require `config/database.php`
2. Verify kết nối database thành công

### **Lỗi "Undefined array key":**
**Nguyên nhân:** Truy cập session/array key không tồn tại
**Giải pháp:**
1. Sử dụng `$_POST['key'] ?? ''` thay vì `$_POST['key']`
2. Kiểm tra `isLoggedIn()` trước khi truy cập user data

---

## 📞 HỖ TRỢ

### **File logs:**
- Database errors → `error_log`
- User activities → `activity_logs` table

### **Testing:**
- `test_connection.php` - Test database
- `admin/dashboard.php` - Kiểm tra admin functions
- Browser DevTools - Debug JavaScript

---

## 🎉 HOÀN THÀNH!

Website BookReview đã sẵn sàng sử dụng với đầy đủ tính năng:

✅ **Frontend:** Giao diện hiện đại, responsive  
✅ **Backend:** PHP với PDO, bảo mật tốt  
✅ **Database:** MySQL cloud (Aiven)  
✅ **Admin:** Panel quản trị đầy đủ  
✅ **User:** Đăng ký, đăng nhập, đánh giá  
✅ **Security:** HTTPS ready, input validation  

**Chúc bạn sử dụng thành công! 🚀📚** 