<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirectWithMessage('../login.php', 'Bạn cần đăng nhập với quyền admin để truy cập trang này', 'error');
}

$page_title = 'Dashboard';

// Thống kê tổng quan
$stats = [];

// Tổng số sách
$stmt = $pdo->query("SELECT COUNT(*) as count FROM books");
$stats['total_books'] = $stmt->fetch()['count'];

// Tổng số người dùng
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['total_users'] = $stmt->fetch()['count'];

// Tổng số đánh giá
$stmt = $pdo->query("SELECT COUNT(*) as count FROM reviews");
$stats['total_reviews'] = $stmt->fetch()['count'];

// Tổng số thể loại
$stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
$stats['total_categories'] = $stmt->fetch()['count'];

// Sách mới nhất
$latest_books = $pdo->query("
    SELECT b.*, c.name as category_name 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    ORDER BY b.created_at DESC 
    LIMIT 5
")->fetchAll();

// Đánh giá mới nhất - Dynamic column detection
$latest_reviews = [];
try {
    // First, check what columns exist in users table
    $columnsStmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Determine which name column to use
    $nameColumn = 'id'; // fallback
    if (in_array('full_name', $columns)) {
        $nameColumn = 'full_name';
    } elseif (in_array('name', $columns)) {
        $nameColumn = 'name';
    } elseif (in_array('username', $columns)) {
        $nameColumn = 'username';
    } elseif (in_array('email', $columns)) {
        $nameColumn = 'email';
    }
    
    // Build query with detected column
    $query = "
        SELECT r.*, b.title as book_title, u.$nameColumn as user_name 
        FROM reviews r 
        JOIN books b ON r.book_id = b.id 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC 
        LIMIT 5
    ";
    
    $latest_reviews = $pdo->query($query)->fetchAll();
    
} catch (PDOException $e) {
    // If still fails, create empty array to prevent further errors
    $latest_reviews = [];
    error_log("Dashboard reviews query failed: " . $e->getMessage());
}

include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard Admin
        </h1>
    </div>
</div>

<!-- Thống kê Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số sách
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_books']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Người dùng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_users']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Đánh giá
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_reviews']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Thể loại
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_categories']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row">
    <!-- Sách mới nhất -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sách mới nhất</h6>
                <a href="books.php" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if (empty($latest_books)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có sách nào</p>
                        <a href="../add-book.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm sách đầu tiên
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tên sách</th>
                                    <th>Tác giả</th>
                                    <th>Thể loại</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_books as $book): ?>
                                <tr>
                                    <td>
                                        <a href="../book.php?id=<?php echo $book['id']; ?>">
                                            <?php echo htmlspecialchars($book['title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['category_name'] ?: 'N/A'); ?></td>
                                    <td><?php echo formatDate($book['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Đánh giá mới nhất -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Đánh giá mới nhất</h6>
                <a href="reviews.php" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if (empty($latest_reviews)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đánh giá nào</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Sách</th>
                                    <th>Điểm</th>
                                    <th>Ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_reviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                    <td>
                                        <a href="../book.php?id=<?php echo $review['book_id']; ?>">
                                            <?php echo htmlspecialchars($review['book_title']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="rating-stars">
                                            <?php echo displayStars($review['rating']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo formatDate($review['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 