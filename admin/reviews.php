<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Quản lý đánh giá';

// Xử lý xóa đánh giá
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    if ($stmt->execute([$review_id])) {
        setMessage('success', 'Xóa đánh giá thành công!');
    } else {
        setMessage('error', 'Có lỗi xảy ra khi xóa đánh giá!');
    }
    
    header('Location: reviews.php');
    exit;
}

// Lấy danh sách đánh giá
$reviews = $pdo->query("
    SELECT r.*, 
           b.title as book_title,
           u.name as user_name,
           u.email as user_email
    FROM reviews r 
    JOIN books b ON r.book_id = b.id
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Quản lý đánh giá</h1>
        </div>
    </div>
    
    <!-- Danh sách đánh giá -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Sách</th>
                            <th>Điểm</th>
                            <th>Bình luận</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['id']; ?></td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($review['user_email']); ?></small>
                                </div>
                            </td>
                            <td>
                                <a href="../book.php?id=<?php echo $review['book_id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($review['book_title']); ?>
                                </a>
                            </td>
                            <td>
                                <div class="rating-stars">
                                    <?php echo displayStars($review['rating']); ?>
                                </div>
                                <small class="text-muted">(<?php echo $review['rating']; ?>/5)</small>
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                </div>
                            </td>
                            <td><?php echo formatDate($review['created_at']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $review['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Thống kê -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đánh giá
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count($reviews); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Điểm trung bình
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $avg_rating = count($reviews) > 0 ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;
                                echo number_format($avg_rating, 1);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đánh giá 5 sao
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count(array_filter($reviews, function($r) { return $r['rating'] == 5; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Đánh giá 1 sao
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count(array_filter($reviews, function($r) { return $r['rating'] == 1; })); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Biểu đồ phân bố đánh giá -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố đánh giá</h6>
                </div>
                <div class="card-body">
                    <?php
                    $rating_distribution = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $rating_distribution[$i] = count(array_filter($reviews, function($r) use ($i) { 
                            return $r['rating'] == $i; 
                        }));
                    }
                    ?>
                    
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?php echo $i; ?> sao</span>
                                <div class="rating-stars">
                                    <?php echo displayStars($i); ?>
                                </div>
                            </div>
                            <span class="text-muted"><?php echo $rating_distribution[$i]; ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <?php 
                            $percentage = count($reviews) > 0 ? ($rating_distribution[$i] / count($reviews)) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Đánh giá gần đây</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $recent_reviews = array_slice($reviews, 0, 5);
                    if (empty($recent_reviews)): 
                    ?>
                        <p class="text-muted">Chưa có đánh giá nào</p>
                    <?php else: ?>
                        <?php foreach ($recent_reviews as $review): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                    <small class="text-muted">đánh giá</small>
                                    <a href="../book.php?id=<?php echo $review['book_id']; ?>" class="text-decoration-none">
                                        <strong><?php echo htmlspecialchars($review['book_title']); ?></strong>
                                    </a>
                                </div>
                                <div class="rating-stars">
                                    <?php echo displayStars($review['rating']); ?>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo formatDate($review['created_at']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 