<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirectWithMessage('../login.php', 'Bạn cần đăng nhập với quyền admin để truy cập trang này', 'error');
}

$page_title = 'Quản lý thể loại';
$message = '';
$message_type = '';

// Xử lý thêm/sửa/xóa category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        
        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                if ($stmt->execute([$name, $description])) {
                    $message = "Đã thêm thể loại '$name' thành công!";
                    $message_type = 'success';
                    logActivity('category_added', "Added category: $name");
                } else {
                    $message = 'Có lỗi xảy ra khi thêm thể loại';
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = 'Tên thể loại đã tồn tại';
                } else {
                    $message = 'Lỗi hệ thống: ' . $e->getMessage();
                }
                $message_type = 'error';
            }
        } else {
            $message = 'Vui lòng nhập tên thể loại';
            $message_type = 'error';
        }
    }
    
    elseif ($action === 'edit') {
        $id = intval($_POST['id']);
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        
        if (!empty($name) && $id > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                if ($stmt->execute([$name, $description, $id])) {
                    $message = "Đã cập nhật thể loại '$name' thành công!";
                    $message_type = 'success';
                    logActivity('category_updated', "Updated category: $name (ID: $id)");
                } else {
                    $message = 'Có lỗi xảy ra khi cập nhật thể loại';
                    $message_type = 'error';
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = 'Tên thể loại đã tồn tại';
                } else {
                    $message = 'Lỗi hệ thống: ' . $e->getMessage();
                }
                $message_type = 'error';
            }
        } else {
            $message = 'Dữ liệu không hợp lệ';
            $message_type = 'error';
        }
    }
    
    elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        
        if ($id > 0) {
            try {
                // Kiểm tra xem có sách nào sử dụng category này không
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM books WHERE category_id = ?");
                $stmt->execute([$id]);
                $book_count = $stmt->fetch()['count'];
                
                if ($book_count > 0) {
                    $message = "Không thể xóa thể loại này vì có $book_count sách đang sử dụng";
                    $message_type = 'error';
                } else {
                    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $message = 'Đã xóa thể loại thành công!';
                        $message_type = 'success';
                        logActivity('category_deleted', "Deleted category ID: $id");
                    } else {
                        $message = 'Có lỗi xảy ra khi xóa thể loại';
                        $message_type = 'error';
                    }
                }
            } catch (PDOException $e) {
                $message = 'Lỗi hệ thống: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Lấy danh sách categories với số lượng sách
$categories = $pdo->query("
    SELECT c.*, COUNT(b.id) as book_count 
    FROM categories c 
    LEFT JOIN books b ON c.id = b.category_id 
    GROUP BY c.id 
    ORDER BY c.name
")->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tags me-2"></i>
            Quản lý thể loại sách
        </h1>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Thêm thể loại mới
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Thêm thể loại
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh sách thể loại
                </h5>
                <span class="badge bg-info"><?php echo count($categories); ?> thể loại</span>
            </div>
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có thể loại nào được tạo</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên thể loại</th>
                                    <th>Mô tả</th>
                                    <th>Số sách</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo $category['description'] ? htmlspecialchars(substr($category['description'], 0, 50)) . '...' : '<em class="text-muted">Không có mô tả</em>'; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $category['book_count']; ?></span>
                                    </td>
                                    <td>
                                        <?php echo formatDate($category['created_at']); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-id="<?php echo $category['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                    data-description="<?php echo htmlspecialchars($category['description']); ?>"
                                                    title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <?php if ($category['book_count'] == 0): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal"
                                                    data-id="<?php echo $category['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary" disabled title="Không thể xóa vì có sách đang sử dụng">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Sửa thể loại
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Xác nhận xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    
                    <p>Bạn có chắc chắn muốn xóa thể loại <strong id="delete_name"></strong>?</p>
                    <p class="text-muted small">Thao tác này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Xóa thể loại
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle edit modal
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const description = button.getAttribute('data-description');
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description;
    });
    
    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_name').textContent = name;
    });
});
</script>

<?php include 'includes/footer.php'; ?> 