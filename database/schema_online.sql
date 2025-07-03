-- Schema for Online Database (Hosting/Cloud)
-- Run this script in your online database
-- NOTE: Database and user should already be created by your hosting provider

-- Use your existing database (replace 'your_database_name' with actual name)
-- USE your_database_name;

-- Table for categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(100) DEFAULT NULL,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for books
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(500),
    category_id INT,
    isbn VARCHAR(20),
    publication_year YEAR,
    publisher VARCHAR(255),
    page_count INT,
    language VARCHAR(50) DEFAULT 'vi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table for reviews
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book_review (user_id, book_id)
);

-- Table for review likes
CREATE TABLE IF NOT EXISTS review_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (review_id, user_id)
);

-- Table for reading lists (wishlist/favorites)
CREATE TABLE IF NOT EXISTS reading_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('want_to_read', 'reading', 'read') DEFAULT 'want_to_read',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Văn học kinh điển', 'Những tác phẩm văn học bất hủ qua các thời đại'),
('Tiểu thuyết hiện đại', 'Các tác phẩm văn học đương đại'),
('Khoa học viễn tưởng', 'Thể loại khoa học viễn tưởng và fantasy'),
('Tâm lý học', 'Sách về tâm lý và phát triển bản thân'),
('Kinh doanh', 'Sách về kinh doanh và khởi nghiệp'),
('Lịch sử', 'Sách về lịch sử và văn hóa'),
('Thiếu nhi', 'Sách dành cho trẻ em và thiếu niên')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, email_verified) VALUES
('admin', 'admin@bookstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin', TRUE)
ON DUPLICATE KEY UPDATE role = 'admin';

-- Insert sample books
INSERT INTO books (title, author, description, cover_image, category_id, isbn, publication_year, publisher, page_count) VALUES
('Tôi thấy hoa vàng trên cỏ xanh', 'Nguyễn Nhật Ánh', 'Một tác phẩm văn học Việt Nam về tuổi thơ và tình cảm gia đình sâu sắc.', 'uploads/covers/hoa-vang-co-xanh.jpg', 2, '9786041002456', 2010, 'NXB Trẻ', 320),
('Harry Potter và Hòn đá Phù thủy', 'J.K. Rowling', 'Cuộc phiêu lưu đầu tiên của cậu bé phù thủy nổi tiếng nhất thế giới.', 'uploads/covers/harry-potter-1.jpg', 3, '9780439708180', 1997, 'Bloomsbury', 309),
('Đắc Nhân Tâm', 'Dale Carnegie', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và xây dựng mối quan hệ.', 'uploads/covers/dac-nhan-tam.jpg', 4, '9786041146900', 1936, 'NXB Tổng hợp TP.HCM', 320),
('Sapiens: Lược sử loài người', 'Yuval Noah Harari', 'Câu chuyện về sự tiến hóa của loài người từ thời tiền sử đến hiện đại.', 'uploads/covers/sapiens.jpg', 6, '9780062316097', 2011, 'Harvill Secker', 443),
('Nhà giả kim', 'Paulo Coelho', 'Hành trình tìm kiếm kho báu và ý nghĩa cuộc sống của chàng chăn cừu Santiago.', 'uploads/covers/nha-gia-kim.jpg', 1, '9780062355300', 1988, 'NXB Hội Nhà văn', 163),
('Atomic Habits', 'James Clear', 'Cách xây dựng thói quen tốt và loại bỏ thói quen xấu một cách hiệu quả.', 'uploads/covers/atomic-habits.jpg', 4, '9780735211292', 2018, 'Avery', 320)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Insert sample reviews
INSERT INTO reviews (book_id, user_id, rating, review_text) VALUES
(1, 1, 5, 'Một tác phẩm tuyệt vời của văn học Việt Nam. Nguyễn Nhật Ánh đã viết rất hay về tuổi thơ.'),
(2, 1, 5, 'Harry Potter luôn là series fantasy tuyệt vời nhất. Rất hay cho mọi lứa tuổi.'),
(3, 1, 4, 'Cuốn sách rất hữu ích cho việc cải thiện kỹ năng giao tiếp. Recommend!'),
(4, 1, 5, 'Yuval Noah Harari viết rất hay về lịch sử loài người. Đọc xong hiểu biết thêm rất nhiều.'),
(5, 1, 4, 'Paulo Coelho viết về triết lý sống rất sâu sắc. Cuốn sách hay để đọc khi cần động lực.'),
(6, 1, 5, 'James Clear giải thích rất rõ về cách hình thành thói quen. Áp dụng được ngay.')
ON DUPLICATE KEY UPDATE rating = VALUES(rating);

-- Create indexes for better performance
CREATE INDEX idx_books_category ON books(category_id);
CREATE INDEX idx_reviews_book ON reviews(book_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);
CREATE INDEX idx_books_title ON books(title);
CREATE INDEX idx_users_email ON users(email);

SELECT 'Online database schema setup completed successfully!' as Status; 