# 📚 BookReview - Website Đánh Giá Sách

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

> **Trang web đánh giá sách hiện đại với giao diện Bootstrap 5, admin panel đầy đủ và database cloud Aiven**

## 🌟 Tính năng nổi bật

### 👥 **Dành cho người dùng:**
- 🏠 Trang chủ với sách nổi bật và thống kê
- 📝 Đăng ký/đăng nhập an toàn
- ⭐ Đánh giá sách với hệ thống 5 sao
- 📚 Xem chi tiết sách với thông tin đầy đủ
- 🔍 Tìm kiếm và lọc sách theo thể loại
- 📱 Giao diện responsive, mobile-friendly

### 👨‍💼 **Dành cho admin:**
- 📊 Dashboard với thống kê chi tiết
- 📚 Quản lý sách (thêm/sửa/xóa)
- 🏷️ Quản lý thể loại sách
- 👥 Quản lý người dùng
- ⭐ Quản lý đánh giá
- 📈 Báo cáo và phân tích

## 🚀 Công nghệ sử dụng

### **Backend:**
- **PHP 8+** - Server-side logic
- **PDO** - Database abstraction layer
- **MySQL** - Cloud database (Aiven)
- **Session management** - User authentication

### **Frontend:**
- **Bootstrap 5** - UI framework
- **Font Awesome 6** - Icons
- **Google Fonts (Poppins)** - Typography
- **Vanilla JavaScript** - Interactive features

### **Database:**
- **Aiven Cloud MySQL** - Managed database
- **SSL connection** - Secure connection
- **Optimized indexes** - Performance tuning

## 📁 Cấu trúc dự án

```
📦 BookReview/
├── 📂 admin/                    # Admin panel
│   ├── 📂 includes/
│   │   ├── header.php          # Admin header
│   │   └── footer.php          # Admin footer
│   ├── dashboard.php           # Admin dashboard
│   ├── books.php              # Books management
│   ├── categories.php         # Categories management
│   ├── users.php              # Users management
│   ├── reviews.php            # Reviews management
│   └── index.php              # Admin entry point
├── 📂 config/
│   └── database.php           # Database configuration
├── 📂 includes/
│   ├── header.php             # Main site header
│   ├── footer.php             # Main site footer
│   └── functions.php          # Helper functions
├── 📂 uploads/                # File uploads directory
│   └── covers/                # Book cover images
├── index.php                  # Homepage
├── login.php                  # User login
├── register.php               # User registration
├── logout.php                 # User logout
├── add-book.php              # Add new book (admin)
├── test_connection.php        # Database test
├── aiven_schema.sql          # Database schema
├── HUONG_DAN_SU_DUNG.md      # User guide (Vietnamese)
└── README.md                  # This file
```

## ⚡ Cài đặt nhanh

### **1. Clone repository**
```bash
git clone [repository-url]
cd BookReview
```

### **2. Cấu hình database**
```php
// config/database.php
define('DB_HOST', 'your-aiven-host');
define('DB_PORT', 13647);
define('DB_NAME', 'your-database-name');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');
```

### **3. Import database schema**
```sql
-- Chạy nội dung file aiven_schema.sql trong Aiven console
```

### **4. Kiểm tra kết nối**
```bash
# Truy cập để test database
http://localhost/test_connection.php
```

### **5. Sử dụng website**
```bash
# Trang chủ
http://localhost/index.php

# Admin panel (admin/admin123)
http://localhost/admin/
```

## 🔐 Tài khoản demo

| Role | Username | Password | Mô tả |
|------|----------|----------|--------|
| Admin | `admin` | `admin123` | Quản trị viên với quyền đầy đủ |

## 🗂️ Database Schema

### **Tables chính:**
- **`categories`** - Thể loại sách
- **`users`** - Người dùng hệ thống
- **`books`** - Thông tin sách
- **`reviews`** - Đánh giá sách
- **`review_likes`** - Like đánh giá
- **`reading_lists`** - Danh sách đọc
- **`activity_logs`** - Log hoạt động

### **Relationships:**
```sql
books.category_id → categories.id
reviews.book_id → books.id
reviews.user_id → users.id
review_likes.review_id → reviews.id
```

## 🔧 Workflow phát triển

### **Schema SQL (1 lần duy nhất):**
```sql
-- Setup ban đầu - chỉ chạy khi tạo database
CREATE TABLE categories...
INSERT INTO categories VALUES...
```

### **Runtime PHP (hàng ngày):**
```php
// User đăng ký
INSERT INTO users (...) VALUES (...)

// Admin thêm sách
INSERT INTO books (...) VALUES (...)

// User viết review
INSERT INTO reviews (...) VALUES (...)
```

## 🛡️ Bảo mật

- ✅ **Password hashing** với `password_hash()`
- ✅ **SQL injection prevention** với PDO prepared statements
- ✅ **XSS protection** với `htmlspecialchars()`
- ✅ **CSRF protection** với tokens
- ✅ **Input validation** và sanitization
- ✅ **Session security** với secure flags
- ✅ **File upload validation** với type checking

## 📱 Responsive Design

- 📱 **Mobile-first** approach
- 🖥️ **Desktop** optimized
- 📱 **Tablet** friendly
- 🎨 **Modern UI/UX** with smooth animations
- ⚡ **Fast loading** with optimized assets

## 🔍 SEO & Performance

- 🏷️ **Semantic HTML5** structure
- 🔍 **Meta tags** optimization
- ⚡ **Database indexing** for fast queries
- 🖼️ **Image optimization** with lazy loading
- 📊 **Minimal JavaScript** footprint

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📋 TODO

- [ ] API endpoints cho mobile app
- [ ] Email verification system
- [ ] Advanced search với filters
- [ ] Book recommendations AI
- [ ] Social sharing integration
- [ ] Reading progress tracking
- [ ] Book clubs functionality
- [ ] Advanced analytics dashboard

## 🐛 Bug Reports

Nếu bạn tìm thấy bug, vui lòng tạo issue với:
- Môi trường (PHP version, browser, OS)
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (nếu có)

## 📚 Tài liệu

- 📖 [Hướng dẫn sử dụng chi tiết](HUONG_DAN_SU_DUNG.md)
- 🔧 [API Documentation](docs/api.md) *(coming soon)*
- 🎨 [UI Style Guide](docs/style-guide.md) *(coming soon)*

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Liên hệ

- 📧 Email: [your-email@example.com]
- 🌐 Website: [your-website.com]
- 💼 LinkedIn: [your-linkedin-profile]

## 🙏 Credits

- **Bootstrap** - UI Framework
- **Font Awesome** - Icons
- **Aiven** - Cloud Database
- **Google Fonts** - Typography
- **Unsplash** - Sample images

---

**⭐ Nếu project này hữu ích, hãy cho chúng tôi một star! ⭐**

---

*Được phát triển với ❤️ bằng PHP và Bootstrap* 