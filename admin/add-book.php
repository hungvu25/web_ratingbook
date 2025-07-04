<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/imgbb_functions.php';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirectWithMessage('../login.php', 'Bạn cần đăng nhập với quyền admin để thêm sách', 'error');
}

$page_title = 'Thêm sách mới';
$errors = [];
$success = false;

// Xử lý form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    
    // Validation
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
    
    // Xử lý upload ảnh bìa
    $cover_image_url = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $imageName = 'book_' . time() . '_' . rand(1000, 9999);
        $uploadResult = processImageUpload($_FILES['cover_image'], $imageName);
        
        if ($uploadResult['success']) {
            $cover_image_url = $uploadResult['url'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }
    
    // Nếu không có lỗi, thêm sách vào database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, description, category_id, cover_image, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $currentUser = getCurrentUser();
            $created_by = $currentUser['id'];
            
            if ($stmt->execute([$title, $author, $description, $category_id, $cover_image_url, $created_by])) {
                $book_id = $pdo->lastInsertId();
                setMessage('success', 'Thêm sách thành công! Cảm ơn bạn đã đóng góp.');
                header('Location: ../book.php?id=' . $book_id);
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi thêm sách. Vui lòng thử lại.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}

// Lấy danh sách thể loại
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="row justify-content-center">
    <div class="col-lg-10">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Thêm sách mới
                    </h4>
                    <p class="text-muted mb-0 mt-2">Chia sẻ cuốn sách yêu thích của bạn với cộng đồng</p>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="addBookForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-book me-2"></i>Tên sách <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                           placeholder="Nhập tên sách..." required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="author" class="form-label">
                                        <i class="fas fa-user-edit me-2"></i>Tác giả <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="author" name="author" 
                                           value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>" 
                                           placeholder="Nhập tên tác giả..." required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-tags me-2"></i>Thể loại <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Chọn thể loại sách</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="cover_image" class="form-label">
                                        <i class="fas fa-image me-2"></i>Ảnh bìa sách
                                    </label>
                                    <div class="position-relative">
                                        <input type="file" class="form-control" id="cover_image" name="cover_image" 
                                               accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Định dạng: JPG, PNG, GIF, WEBP. Tối đa 16MB
                                        </div>
                                    </div>
                                    
                                    <!-- Preview area -->
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" 
                                             class="img-thumbnail" style="max-width: 200px; max-height: 300px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeImage()">
                                                <i class="fas fa-times me-1"></i>Xóa ảnh
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Default placeholder -->
                                    <div id="imagePlaceholder" class="mt-3 text-center">
                                        <div class="border border-dashed border-2 p-4 rounded">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chọn ảnh bìa sách</p>
                                            <small class="text-muted">Không bắt buộc</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Mô tả sách <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5" placeholder="Nhập mô tả chi tiết về cuốn sách..." required><?php 
                                echo htmlspecialchars($_POST['description'] ?? ''); 
                            ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-lightbulb me-1"></i>
                                Mô tả chi tiết giúp người đọc hiểu rõ hơn về nội dung cuốn sách
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="books.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus-circle me-2"></i>Thêm sách
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Guidelines Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Hướng dẫn thêm sách
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-check-circle me-2"></i>Nên làm:
                            </h6>
                            <ul class="text-muted">
                                <li>Nhập đầy đủ thông tin chính xác</li>
                                <li>Viết mô tả chi tiết và hấp dẫn</li>
                                <li>Chọn thể loại phù hợp</li>
                                <li>Upload ảnh bìa chất lượng cao</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger">
                                <i class="fas fa-times-circle me-2"></i>Không nên:
                            </h6>
                            <ul class="text-muted">
                                <li>Thêm sách trùng lặp</li>
                                <li>Sử dụng ngôn ngữ không phù hợp</li>
                                <li>Upload ảnh có bản quyền</li>
                                <li>Để trống thông tin quan trọng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<script>
// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('imagePlaceholder').style.display = 'none';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('cover_image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('imagePlaceholder').style.display = 'block';
}

// Form validation and submission
document.getElementById('addBookForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm sách...';
    
    // Re-enable after timeout as fallback
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 10000);
});

// Auto-resize textarea
document.getElementById('description').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Form validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addBookForm');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 