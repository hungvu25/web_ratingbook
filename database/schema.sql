-- Sử dụng cơ sở dữ liệu đã có
USE web_ratingbook;

-- Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng thể loại sách
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng sách
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Bảng đánh giá
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);

-- Thêm dữ liệu mẫu
INSERT INTO categories (name, description) VALUES
('Tiểu thuyết', 'Các tác phẩm văn học hư cấu'),
('Khoa học viễn tưởng', 'Sách về khoa học và công nghệ tương lai'),
('Kinh doanh', 'Sách về quản lý và kinh doanh'),
('Tâm lý học', 'Sách về tâm lý và phát triển bản thân'),
('Lịch sử', 'Sách về lịch sử và văn hóa');

-- Tạo tài khoản admin mặc định (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@ratingbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Thêm một số sách mẫu
INSERT INTO books (title, author, description, category_id) VALUES
('Đắc Nhân Tâm', 'Dale Carnegie', 'Cuốn sách về nghệ thuật đối nhân xử thế và giao tiếp hiệu quả', 4),
('Nhà Giả Kim', 'Paulo Coelho', 'Câu chuyện về hành trình tìm kiếm ý nghĩa cuộc sống', 1),
('7 Thói Quen Hiệu Quả', 'Stephen R. Covey', 'Phương pháp phát triển bản thân và đạt được thành công', 4),
('Khởi Nghiệp Tinh Gọn', 'Eric Ries', 'Phương pháp khởi nghiệp hiệu quả với chi phí thấp', 3),
('Sapiens', 'Yuval Noah Harari', 'Lịch sử loài người từ thời cổ đại đến hiện đại', 5); 