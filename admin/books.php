<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Quản lý sách';

// Xử lý xóa sách
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $book_id = (int)$_GET['delete'];
    
    // Xóa ảnh bìa cũ
    $stmt = $pdo->prepare("SELECT cover_image FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    
    if ($book && $book['cover_image'] && file_exists($book['cover_image'])) {
        unlink($book['cover_image']);
    }
    
    // Xóa sách
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    if ($stmt->execute([$book_id])) {
        setMessage('success', 'Xóa sách thành công!');
    } else {
        setMessage('error', 'Có lỗi xảy ra khi xóa sách!');
    }
    
    header('Location: books.php');
    exit;
}

// Xử lý thêm/sửa sách
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Tên sách không được để trống';
    }
    
    if (empty($author)) {
        $errors[] = 'Tác giả không được để trống';
    }
    
    if (empty($description)) {
        $errors[] = 'Mô tả không được để trống';
    }
    
    if ($category_id <= 0) {
        $errors[] = 'Vui lòng chọn thể loại';
    }
    
    if (empty($errors)) {
        $cover_image = null;
        
        // Xử lý upload ảnh
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image = uploadImage($_FILES['cover_image'], '../uploads/');
            if (!$cover_image) {
                $errors[] = 'Lỗi upload ảnh. Vui lòng thử lại.';
            }
        }
        
        if (empty($errors)) {
            if ($book_id > 0) {
                // Cập nhật sách
                if ($cover_image) {
                    // Xóa ảnh cũ
                    $stmt = $pdo->prepare("SELECT cover_image FROM books WHERE id = ?");
                    $stmt->execute([$book_id]);
                    $old_book = $stmt->fetch();
                    if ($old_book && $old_book['cover_image'] && file_exists($old_book['cover_image'])) {
                        unlink($old_book['cover_image']);
                    }
                    
                    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ?, category_id = ?, cover_image = ? WHERE id = ?");
                    $stmt->execute([$title, $author, $description, $category_id, $cover_image, $book_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, description = ?, category_id = ? WHERE id = ?");
                    $stmt->execute([$title, $author, $description, $category_id, $book_id]);
                }
                setMessage('success', 'Cập nhật sách thành công!');
            } else {
                // Thêm sách mới
                $stmt = $pdo->prepare("INSERT INTO books (title, author, description, category_id, cover_image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $description, $category_id, $cover_image]);
                setMessage('success', 'Thêm sách thành công!');
            }
            
            header('Location: books.php');
            exit;
        }
    }
}

// Lấy thông tin sách để sửa (nếu có)
$edit_book = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $book_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $edit_book = $stmt->fetch();
}

// Lấy danh sách thể loại
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Lấy danh sách sách
$books = $pdo->query("
    SELECT b.*, c.name as category_name, 
           COUNT(r.id) as review_count,
           AVG(r.rating) as avg_rating
    FROM books b 
    LEFT JOIN categories c ON b.category_id = c.id
    LEFT JOIN reviews r ON b.id = r.book_id
    GROUP BY b.id
    ORDER BY b.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Quản lý sách</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookModal">
                    <i class="fas fa-plus me-2"></i>Thêm sách mới
                </button>
            </div>
        </div>
    </div>
    
    <!-- Danh sách sách -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh bìa</th>
                            <th>Tên sách</th>
                            <th>Tác giả</th>
                            <th>Thể loại</th>
                            <th>Đánh giá</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo $book['id']; ?></td>
                            <td>
                                <img src="<?php echo $book['cover_image'] ? '../' . $book['cover_image'] : '../uploads/default-cover.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     style="width: 50px; height: 70px; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['category_name'] ?: 'N/A'); ?></td>
                            <td>
                                <div class="rating-stars">
                                    <?php echo displayStars(round($book['avg_rating'] ?: 0)); ?>
                                </div>
                                <small class="text-muted">(<?php echo $book['review_count']; ?>)</small>
                            </td>
                            <td><?php echo formatDate($book['created_at']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $book['id']; ?>" class="btn btn-sm btn-warning me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sách này?')">
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
</div>

<!-- Modal thêm/sửa sách -->
<div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo $edit_book ? 'Sửa sách' : 'Thêm sách mới'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($edit_book): ?>
                        <input type="hidden" name="book_id" value="<?php echo $edit_book['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tên sách *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['title']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="author" class="form-label">Tác giả *</label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo $edit_book ? htmlspecialchars($edit_book['author']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Thể loại *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Chọn thể loại</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($edit_book && $edit_book['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Ảnh bìa</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                                <?php if ($edit_book && $edit_book['cover_image']): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo $edit_book['cover_image']; ?>" 
                                             alt="Ảnh hiện tại" class="img-thumbnail" style="max-width: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php 
                            echo $edit_book ? htmlspecialchars($edit_book['description']) : ''; 
                        ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_book ? 'Cập nhật' : 'Thêm sách'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tự động mở modal nếu có lỗi hoặc đang sửa
<?php if (!empty($errors) || $edit_book): ?>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('bookModal'));
    modal.show();
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?> 