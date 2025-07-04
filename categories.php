<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Thể loại sách';

// Lấy thể loại được chọn (nếu có)
$selected_category = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy danh sách tất cả thể loại với số lượng sách
$categories = $pdo->query("
    SELECT c.*, COUNT(b.id) as book_count 
    FROM categories c 
    LEFT JOIN books b ON c.id = b.category_id 
    GROUP BY c.id 
    ORDER BY c.name
")->fetchAll();

// Nếu có thể loại được chọn, lấy thông tin thể loại đó
$category_info = null;
$books = [];
if ($selected_category > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$selected_category]);
    $category_info = $stmt->fetch();
    
    if ($category_info) {
        // Lấy sách thuộc thể loại này
        $stmt = $pdo->prepare("
            SELECT b.*, c.name as category_name, 
                   AVG(r.rating) as avg_rating, 
                   COUNT(r.id) as review_count
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id
            LEFT JOIN reviews r ON b.id = r.book_id
            WHERE b.category_id = ?
            GROUP BY b.id
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$selected_category]);
        $books = $stmt->fetchAll();
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <?php if ($selected_category > 0 && $category_info): ?>
        <!-- Hiển thị sách theo thể loại -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="categories.php">Thể loại</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($category_info['name']); ?></li>
                    </ol>
                </nav>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2">
                            <i class="fas fa-tag me-2 text-primary"></i>
                            <?php echo htmlspecialchars($category_info['name']); ?>
                        </h1>
                        <?php if ($category_info['description']): ?>
                            <p class="text-muted lead"><?php echo htmlspecialchars($category_info['description']); ?></p>
                        <?php endif; ?>
                        <p class="text-muted">
                            <i class="fas fa-book me-2"></i>
                            <?php echo count($books); ?> cuốn sách
                        </p>
                    </div>
                    <a href="categories.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Tất cả thể loại
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Danh sách sách -->
        <?php if (empty($books)): ?>
            <div class="text-center py-5">
                <i class="fas fa-book fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có sách nào trong thể loại này</h4>
                <p class="text-muted">Hãy quay lại sau hoặc khám phá các thể loại khác.</p>
                <a href="categories.php" class="btn btn-primary">Xem thể loại khác</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($books as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card book-card h-100">
                        <img src="<?php echo $book['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                             class="card-img-top book-cover" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h6>
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-user-edit me-1"></i>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <div class="rating-stars mb-2">
                                <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                                <small class="text-muted ms-1">(<?php echo $book['review_count']; ?>)</small>
                            </div>
                            <div class="mt-auto">
                                <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-2"></i>Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Hiển thị tất cả thể loại -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-4">
                    <i class="fas fa-tags me-2 text-primary"></i>
                    Thể loại sách
                </h1>
                <p class="lead text-muted">Khám phá sách theo các thể loại khác nhau</p>
            </div>
        </div>
        
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có thể loại nào</h4>
                <p class="text-muted">Quản trị viên chưa tạo thể loại sách nào.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card category-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="fas fa-tag fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <?php if ($category['description']): ?>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($category['description'], 0, 100)); ?>
                                    <?php if (strlen($category['description']) > 100): ?>...<?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <span class="badge bg-primary fs-6">
                                    <i class="fas fa-book me-1"></i>
                                    <?php echo $category['book_count']; ?> cuốn sách
                                </span>
                            </div>
                            
                            <?php if ($category['book_count'] > 0): ?>
                                <a href="categories.php?id=<?php echo $category['id']; ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>Xem sách
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Chưa có sách
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.category-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.category-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
}

.book-card {
    transition: all 0.4s ease;
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.book-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.book-cover {
    height: 280px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.book-card:hover .book-cover {
    transform: scale(1.05);
}
</style>

<?php include 'includes/footer.php'; ?> 