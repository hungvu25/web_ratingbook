<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Đánh giá sách';

// Phân trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Bộ lọc
$filter_rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$filter_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng query với filters
$where_conditions = [];
$params = [];

if ($filter_rating > 0) {
    $where_conditions[] = "r.rating = ?";
    $params[] = $filter_rating;
}

if ($filter_category > 0) {
    $where_conditions[] = "b.category_id = ?";
    $params[] = $filter_category;
}

if (!empty($search)) {
    $where_conditions[] = "(b.title LIKE ? OR b.author LIKE ? OR r.comment LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Đếm tổng số đánh giá
$count_query = "
    SELECT COUNT(*) as total
    FROM reviews r 
    JOIN books b ON r.book_id = b.id
    JOIN users u ON r.user_id = u.id
    $where_clause
";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute($params);
$total_reviews = $count_stmt->fetch()['total'];
$total_pages = ceil($total_reviews / $limit);

// Lấy đánh giá với phân trang
$reviews_query = "
    SELECT r.*, 
           b.title as book_title, b.id as book_id, b.cover_image,
           u.name as user_name,
           c.name as category_name
    FROM reviews r 
    JOIN books b ON r.book_id = b.id
    JOIN users u ON r.user_id = u.id
    LEFT JOIN categories c ON b.category_id = c.id
    $where_clause
    ORDER BY r.created_at DESC
    LIMIT $limit OFFSET $offset
";
$reviews_stmt = $pdo->prepare($reviews_query);
$reviews_stmt->execute($params);
$reviews = $reviews_stmt->fetchAll();

// Lấy danh sách thể loại cho filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Thống kê đánh giá
$stats = [];
for ($i = 1; $i <= 5; $i++) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviews WHERE rating = ?");
    $stmt->execute([$i]);
    $stats[$i] = $stmt->fetch()['count'];
}

include 'includes/header.php';
?>

<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="fas fa-star me-2 text-warning"></i>
                Đánh giá sách
            </h1>
            <p class="lead text-muted">Khám phá những đánh giá và nhận xét từ cộng đồng độc giả</p>
        </div>
    </div>
    
    <!-- Thống kê và bộ lọc -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Bộ lọc -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Tên sách, tác giả, nội dung...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Điểm đánh giá</label>
                                <select class="form-select" name="rating">
                                    <option value="0">Tất cả</option>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo $filter_rating == $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> sao
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Thể loại</label>
                                <select class="form-select" name="category">
                                    <option value="0">Tất cả</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $filter_category == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Thống kê -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thống kê đánh giá
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tổng đánh giá:</span>
                            <strong><?php echo $total_reviews; ?></strong>
                        </div>
                    </div>
                    
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?php echo $i; ?></span>
                                <div class="rating-stars">
                                    <?php echo displayStars($i); ?>
                                </div>
                            </div>
                            <span class="text-muted"><?php echo $stats[$i]; ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <?php 
                            $percentage = $total_reviews > 0 ? ($stats[$i] / $total_reviews) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danh sách đánh giá -->
    <div class="row">
        <div class="col-12">
            <?php if (!empty($search) || $filter_rating > 0 || $filter_category > 0): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">
                        Tìm thấy <?php echo $total_reviews; ?> đánh giá
                    </p>
                    <a href="reviews.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-2"></i>Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if (empty($reviews)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Không tìm thấy đánh giá nào</h4>
                    <p class="text-muted">Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                    <a href="reviews.php" class="btn btn-primary">Xem tất cả đánh giá</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($reviews as $review): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card review-card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <a href="book.php?id=<?php echo $review['book_id']; ?>">
                                            <img src="<?php echo $review['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($review['book_title']); ?>"
                                                 class="img-fluid rounded book-thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="rating-stars">
                                                <?php echo displayStars($review['rating']); ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo formatDate($review['created_at']); ?>
                                            </small>
                                        </div>
                                        
                                        <h6 class="card-title mb-2">
                                            <a href="book.php?id=<?php echo $review['book_id']; ?>" 
                                               class="text-decoration-none">
                                                <?php echo htmlspecialchars($review['book_title']); ?>
                                            </a>
                                        </h6>
                                        
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($review['user_name']); ?>
                                            <?php if ($review['category_name']): ?>
                                                | <i class="fas fa-tag me-1"></i>
                                                <?php echo htmlspecialchars($review['category_name']); ?>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p class="card-text">
                                            <?php 
                                            $comment = htmlspecialchars($review['comment']);
                                            echo strlen($comment) > 120 ? substr($comment, 0, 120) . '...' : $comment;
                                            ?>
                                        </p>
                                        
                                        <a href="book.php?id=<?php echo $review['book_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i>Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Phân trang -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Phân trang">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&rating=<?php echo $filter_rating; ?>&category=<?php echo $filter_category; ?>&search=<?php echo urlencode($search); ?>">
                                    Trước
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&rating=<?php echo $filter_rating; ?>&category=<?php echo $filter_category; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&rating=<?php echo $filter_rating; ?>&category=<?php echo $filter_category; ?>&search=<?php echo urlencode($search); ?>">
                                    Sau
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.review-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 15px;
}

.review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.book-thumbnail {
    height: 120px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.book-thumbnail:hover {
    transform: scale(1.05);
}

.rating-stars {
    color: #ffd700;
    filter: drop-shadow(0 0 3px rgba(255, 215, 0, 0.5));
}

.progress {
    border-radius: 10px;
    background: #f1f3f7;
}

.progress-bar {
    border-radius: 10px;
}

.pagination .page-link {
    border-radius: 10px;
    margin: 0 2px;
    border: none;
    color: #667eea;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
}

.pagination .page-link:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}
</style>

<?php include 'includes/footer.php'; ?>
