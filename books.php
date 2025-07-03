<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Danh sách sách';

// Xử lý tìm kiếm và sắp xếp
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;

// Xây dựng query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(b.title LIKE ? OR b.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category > 0) {
    $where_conditions[] = "b.category_id = ?";
    $params[] = $category;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Query để đếm tổng số sách
$count_query = "
    SELECT COUNT(*) as total 
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id 
    $where_clause
";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_books = $stmt->fetch()['total'];

// Tính phân trang
$total_pages = ceil($total_books / $per_page);
$offset = ($page - 1) * $per_page;

// Sắp xếp
$order_by = 'b.created_at DESC';
switch ($sort) {
    case 'rating':
        $order_by = 'avg_rating DESC, review_count DESC';
        break;
    case 'title':
        $order_by = 'b.title ASC';
        break;
    case 'latest':
    default:
        $order_by = 'b.created_at DESC';
        break;
}

// Query lấy sách
$query = "
    SELECT b.*, c.name as category_name, 
           AVG(r.rating) as avg_rating, 
           COUNT(r.id) as review_count
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id
    LEFT JOIN reviews r ON b.id = r.book_id
    $where_clause
    GROUP BY b.id
    ORDER BY $order_by
    LIMIT $per_page OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Lấy danh sách thể loại cho filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="">
                        <!-- Tìm kiếm -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Tên sách, tác giả...">
                        </div>
                        
                        <!-- Thể loại -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Thể loại</label>
                            <select class="form-select" id="category" name="category">
                                <option value="0">Tất cả thể loại</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Sắp xếp -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sắp xếp theo</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest" <?php echo $sort == 'latest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Điểm đánh giá</option>
                                <option value="title" <?php echo $sort == 'title' ? 'selected' : ''; ?>>Tên A-Z</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Lọc
                            </button>
                        </div>
                        
                        <?php if (!empty($search) || $category > 0): ?>
                            <div class="mt-3">
                                <a href="books.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Xóa bộ lọc
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Danh sách sách</h2>
                    <p class="text-muted mb-0">
                        Hiển thị <?php echo $total_books; ?> cuốn sách
                        <?php if (!empty($search)): ?>
                            cho từ khóa "<strong><?php echo htmlspecialchars($search); ?></strong>"
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <?php if (empty($books)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-3"></i>
                    <h4>Không tìm thấy sách</h4>
                    <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($books as $book): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card book-card h-100">
                            <img src="<?php echo $book['cover_image'] ?: 'uploads/default-cover.jpg'; ?>" 
                                 class="card-img-top book-cover" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($book['author']); ?></p>
                                
                                <?php if ($book['category_name']): ?>
                                    <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($book['category_name']); ?></span>
                                <?php endif; ?>
                                
                                <div class="rating-stars mb-2">
                                    <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                                    <small class="text-muted ms-1">(<?php echo $book['review_count']; ?>)</small>
                                </div>
                                
                                <p class="card-text small text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Phân trang -->
                <?php if ($total_pages > 1): ?>
                    <div class="mt-4">
                        <?php 
                        $base_url = 'books.php?' . http_build_query(array_filter([
                            'search' => $search,
                            'category' => $category,
                            'sort' => $sort
                        ]));
                        echo pagination($total_books, $per_page, $page, $base_url);
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 