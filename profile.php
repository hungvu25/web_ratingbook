<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Hồ sơ';

$user = getCurrentUser();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_profile';
    $errors = [];
    
    if ($action === 'upload_avatar') {
        // Xử lý upload avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $imageName = 'avatar_' . $user['id'] . '_' . time();
            $uploadResult = processAvatarUpload($_FILES['avatar'], $imageName);
            
            if ($uploadResult['success']) {
                // Cập nhật avatar URL vào database
                if (updateUserAvatar($user['id'], $uploadResult['url'])) {
                    setMessage('success', 'Cập nhật avatar thành công!');
                    header('Location: profile.php');
                    exit;
                } else {
                    $errors[] = 'Lỗi khi lưu avatar vào database';
                }
            } else {
                $errors[] = $uploadResult['error'];
            }
        } else {
            $errors[] = 'Vui lòng chọn file ảnh để upload';
        }
    } else {
        // Xử lý cập nhật thông tin cá nhân
        $name = trim($_POST['name']);
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
    
    if (empty($name)) {
        $errors[] = 'Tên không được để trống';
    }
    
    // Nếu muốn đổi mật khẩu
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Mật khẩu hiện tại không đúng';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }
    }
    
    if (empty($errors)) {
        if (!empty($new_password)) {
            // Cập nhật cả tên và mật khẩu
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $hashed_password, $user['id']]);
        } else {
            // Chỉ cập nhật tên
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $user['id']]);
        }
        
        setMessage('success', 'Cập nhật thông tin thành công!');
        header('Location: profile.php');
        exit;
        }
    }
}

// Lấy đánh giá của user
$stmt = $pdo->prepare("
    SELECT r.*, b.title as book_title, b.id as book_id
    FROM reviews r 
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$user['id']]);
$user_reviews = sanitizeDataFromDb($stmt->fetchAll());

include 'includes/header.php';
?>
<!-- Modern styles are already included in header.php -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Avatar Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Ảnh đại diện</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="<?php echo getUserAvatar($user); ?>" 
                                 alt="Avatar" 
                                 class="rounded-circle" 
                                 style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #dee2e6;">
                        </div>
                        <div class="col-md-9">
                            <form method="POST" enctype="multipart/form-data" id="avatarForm">
                                <input type="hidden" name="action" value="upload_avatar">
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Chọn ảnh mới</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar" 
                                           accept="image/*" required>
                                    <div class="form-text">
                                        Định dạng: JPG, PNG, GIF, WEBP. Kích thước tối đa: 16MB
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Cập nhật Avatar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin hồ sơ -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin hồ sơ</h5>
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
                        <input type="hidden" name="action" value="update_profile">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    <div class="form-text">Email không thể thay đổi</div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6>Đổi mật khẩu (tùy chọn)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật thông tin
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Đánh giá của tôi -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Đánh giá của tôi (<?php echo count($user_reviews); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($user_reviews)): ?>
                        <p class="text-muted text-center">Bạn chưa có đánh giá nào.</p>
                    <?php else: ?>
                        <?php foreach ($user_reviews as $review): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="book.php?id=<?php echo $review['book_id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($review['book_title']); ?>
                                        </a>
                                    </h6>
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
            <!-- Thông tin tài khoản -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin tài khoản</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Vai trò:</strong>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-danger">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">User</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Trạng thái:</strong>
                        <?php if ($user['status'] === 'active'): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Bị khóa</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Ngày tham gia:</strong><br>
                        <small class="text-muted"><?php echo formatDate($user['created_at']); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Số đánh giá:</strong><br>
                        <span class="h5 text-primary"><?php echo count($user_reviews); ?></span>
                    </div>
                    
                    <?php if (!empty($user_reviews)): ?>
                    <div class="mb-3">
                        <strong>Điểm trung bình:</strong><br>
                        <div class="rating-stars">
                            <?php 
                            $avg_rating = array_sum(array_column($user_reviews, 'rating')) / count($user_reviews);
                            echo displayStars(round($avg_rating));
                            ?>
                        </div>
                        <small class="text-muted">(<?php echo number_format($avg_rating, 1); ?>/5)</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Thống kê đánh giá -->
            <?php if (!empty($user_reviews)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Thống kê đánh giá</h6>
                </div>
                <div class="card-body">
                    <?php
                    $rating_stats = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $rating_stats[$i] = count(array_filter($user_reviews, function($r) use ($i) { 
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
                            <span class="text-muted"><?php echo $rating_stats[$i]; ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <?php 
                            $percentage = count($user_reviews) > 0 ? ($rating_stats[$i] / count($user_reviews)) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Avatar Upload JavaScript -->
<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WEBP)');
            e.target.value = '';
            return;
        }
        
        // Validate file size (16MB)
        if (file.size > 16 * 1024 * 1024) {
            alert('File quá lớn. Kích thước tối đa là 16MB');
            e.target.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImg = document.querySelector('.col-md-3 img');
            avatarImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Form submission with loading state
document.getElementById('avatarForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang upload...';
    
    // Re-enable button after 30 seconds as fallback
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 30000);
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword && confirmPassword.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else if (confirmPassword.length > 0) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value.length > 0) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php include 'includes/footer.php'; ?> 