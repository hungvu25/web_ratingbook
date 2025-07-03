<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Chi tiết sách';

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$book_id) {
    header('Location: books.php');
    exit;
}

// Lấy thông tin sách
$stmt = $pdo->prepare("
    SELECT b.*, c.name as category_name, 
           AVG(r.rating) as avg_rating, 
           COUNT(r.id) as review_count
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id
    LEFT JOIN reviews r ON b.id = r.book_id
    WHERE b.id = ?
    GROUP BY b.id
");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: books.php');
    exit;
}

$page_title = $book['title'];

// Xử lý đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    
    $errors = [];
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Điểm đánh giá phải từ 1-5 sao';
    }
    
    if (empty($comment)) {
        $errors[] = 'Vui lòng nhập nội dung bình luận';
    }
    
    if (empty($errors)) {
        // Kiểm tra xem user đã đánh giá sách này chưa
        $stmt = $pdo->prepare("SELECT id FROM reviews WHERE book_id = ? AND user_id = ?");
        $stmt->execute([$book_id, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            // Cập nhật đánh giá
            $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE book_id = ? AND user_id = ?");
            $stmt->execute([$rating, $comment, $book_id, $_SESSION['user_id']]);
            setMessage('success', 'Cập nhật đánh giá thành công!');
        } else {
            // Thêm đánh giá mới
            $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$book_id, $_SESSION['user_id'], $rating, $comment]);
            setMessage('success', 'Đánh giá thành công!');
        }
        
        header("Location: book.php?id=$book_id");
        exit;
    }
}

// Lấy đánh giá của user hiện tại (nếu có)
$user_review = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE book_id = ? AND user_id = ?");
    $stmt->execute([$book_id, $_SESSION['user_id']]);
    $user_review = $stmt->fetch();
}

// Lấy danh sách đánh giá
$stmt = $pdo->prepare("
    SELECT r.*, u.name as user_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.book_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$book_id]);
$reviews = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Thông tin sách -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo $book['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h2 class="fw-bold"><?php echo htmlspecialchars($book['title']); ?></h2>
                            <p class="text-muted mb-2">Tác giả: <strong><?php echo htmlspecialchars($book['author']); ?></strong></p>
                            
                            <?php if ($book['category_name']): ?>
                                <p class="mb-2">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($book['category_name']); ?></span>
                                </p>
                            <?php endif; ?>
                            
                            <div class="rating-stars mb-3">
                                <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                                <span class="ms-2">
                                    <?php echo number_format($book['avg_rating'], 1); ?>/5 
                                    (<?php echo $book['review_count']; ?> đánh giá)
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <h5>Mô tả:</h5>
                                <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                            </div>
                            
                            <div class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                Thêm vào: <?php echo formatDate($book['created_at']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form đánh giá -->
            <?php if (isLoggedIn()): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        <?php echo $user_review ? 'Cập nhật đánh giá' : 'Đánh giá sách'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Điểm đánh giá:</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" 
                                           id="star<?php echo $i; ?>" 
                                           <?php echo ($user_review && $user_review['rating'] == $i) ? 'checked' : ''; ?> required>
                                    <label for="star<?php echo $i; ?>">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Bình luận:</label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" 
                                      placeholder="Chia sẻ cảm nhận của bạn về cuốn sách này..." required><?php 
                                echo $user_review ? htmlspecialchars($user_review['comment']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            <?php echo $user_review ? 'Cập nhật' : 'Gửi đánh giá'; ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card mt-4">
                <div class="card-body text-center">
                    <p class="mb-3">Bạn cần đăng nhập để đánh giá sách này</p>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Danh sách đánh giá -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Đánh giá (<?php echo count($reviews); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <p class="text-muted text-center">Chưa có đánh giá nào cho sách này.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                    <div class="rating-stars mb-2">
                                        <?php echo displayStars($review['rating']); ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?php echo formatDate($review['created_at']); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Sách liên quan -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-book me-2"></i>Sách liên quan</h6>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT b.*, AVG(r.rating) as avg_rating 
                        FROM books b 
                        LEFT JOIN reviews r ON b.id = r.book_id
                        WHERE b.category_id = ? AND b.id != ?
                        GROUP BY b.id
                        ORDER BY avg_rating DESC
                        LIMIT 5
                    ");
                    $stmt->execute([$book['category_id'], $book_id]);
                    $related_books = $stmt->fetchAll();
                    ?>
                    
                    <?php if (empty($related_books)): ?>
                        <p class="text-muted small">Không có sách liên quan</p>
                    <?php else: ?>
                        <?php foreach ($related_books as $related): ?>
                        <div class="d-flex mb-3">
                            <img src="<?php echo $related['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                                 class="rounded me-3" style="width: 60px; height: 80px; object-fit: cover;" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="book.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($related['author']); ?></p>
                                <div class="rating-stars">
                                    <?php echo displayStars(round($related['avg_rating'] ?: 0)); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 24px;
    color: #ddd;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}
</style>

<?php include 'includes/footer.php'; ?> 