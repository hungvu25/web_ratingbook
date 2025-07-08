<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Trang chủ';

// Lấy sách nổi bật (có điểm đánh giá cao nhất)
$stmt = $pdo->query("
    SELECT b.*, c.name as category_name, 
           AVG(r.rating) as avg_rating, 
           COUNT(r.id) as review_count
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id
    LEFT JOIN reviews r ON b.id = r.book_id
    GROUP BY b.id
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 6
");
$featured_books = sanitizeDataFromDb($stmt->fetchAll());

// Lấy sách mới nhất
$stmt = $pdo->query("
    SELECT b.*, c.name as category_name, 
           AVG(r.rating) as avg_rating, 
           COUNT(r.id) as review_count
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id
    LEFT JOIN reviews r ON b.id = r.book_id
    GROUP BY b.id
    ORDER BY b.created_at DESC
    LIMIT 6
");
$latest_books = sanitizeDataFromDb($stmt->fetchAll());

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Khám phá thế giới sách tuyệt vời</h1>
        <p class="lead mb-5">Đọc, đánh giá và chia sẻ những cuốn sách hay nhất. Tham gia cộng đồng đọc sách lớn nhất Việt Nam.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="books.php" class="btn btn-light btn-lg px-4">
                <i class="fas fa-search me-2"></i>Khám phá sách
            </a>
            <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Books Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold">Sách nổi bật</h2>
                <p class="text-muted">Những cuốn sách được đánh giá cao nhất</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="books.php" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($featured_books as $book): ?>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card book-card h-100">
                    <img src="<?php echo $book['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                         class="card-img-top book-cover" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="rating-stars mb-2">
                            <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                            <small class="text-muted ms-1">(<?php echo $book['review_count']; ?>)</small>
                        </div>
                        <div class="mt-auto">
                            <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Books Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold">Sách mới nhất</h2>
                <p class="text-muted">Những cuốn sách vừa được thêm vào hệ thống</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="books.php" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($latest_books as $book): ?>
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card book-card h-100">
                    <img src="<?php echo $book['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                         class="card-img-top book-cover" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="rating-stars mb-2">
                            <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                            <small class="text-muted ms-1">(<?php echo $book['review_count']; ?>)</small>
                        </div>
                        <div class="mt-auto">
                            <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-primary text-white">
                    <div class="card-body">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h4 class="card-title">
                            <?php 
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
                            echo $stmt->fetch()['count'];
                            ?>
                        </h4>
                        <p class="card-text">Cuốn sách</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-success text-white">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h4 class="card-title">
                            <?php 
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                            echo $stmt->fetch()['count'];
                            ?>
                        </h4>
                        <p class="card-text">Thành viên</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-warning text-white">
                    <div class="card-body">
                        <i class="fas fa-star fa-3x mb-3"></i>
                        <h4 class="card-title">
                            <?php 
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM reviews");
                            echo $stmt->fetch()['count'];
                            ?>
                        </h4>
                        <p class="card-text">Đánh giá</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-info text-white">
                    <div class="card-body">
                        <i class="fas fa-tags fa-3x mb-3"></i>
                        <h4 class="card-title">
                            <?php 
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
                            echo $stmt->fetch()['count'];
                            ?>
                        </h4>
                        <p class="card-text">Thể loại</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?> 