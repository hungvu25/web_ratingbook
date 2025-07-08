<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Kiểm tra trạng thái tài khoản';
$message = '';
$error = '';
$userData = null;

// Process the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Vui lòng nhập email';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, email, full_name, is_verified, verification_token, verified_at FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userData = sanitizeDataFromDb($stmt->fetch());
            
            if (!$userData) {
                $error = 'Không tìm thấy tài khoản với email này';
            }
        } catch (PDOException $e) {
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        Kiểm tra trạng thái tài khoản
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($userData): ?>
                        <div class="account-status mb-4">
                            <h5>Thông tin tài khoản</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Email:</strong> <?php echo $userData['email']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Tên đăng nhập:</strong> <?php echo $userData['username']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Họ tên:</strong> <?php echo $userData['full_name']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Trạng thái xác minh:</strong>
                                    <?php if ($userData['is_verified']): ?>
                                        <span class="badge bg-success">Đã xác minh</span>
                                        <?php if ($userData['verified_at']): ?>
                                            <small class="text-muted ms-2">vào <?php echo formatDate($userData['verified_at'], 'd/m/Y H:i:s'); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Chưa xác minh</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                            
                            <?php if (!$userData['is_verified'] && $userData['verification_token']): ?>
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Tài khoản của bạn chưa được xác minh. Bạn có thể xác minh ngay bằng cách nhấp vào liên kết dưới đây:
                                    </div>
                                    
                                    <?php
                                    $verificationLink = 'http://' . $_SERVER['HTTP_HOST'] . '/verify-email.php?token=' . $userData['verification_token'] . '&email=' . urlencode($userData['email']);
                                    ?>
                                    
                                    <div class="d-grid">
                                        <a href="<?php echo $verificationLink; ?>" class="btn btn-primary">
                                            <i class="fas fa-envelope-open-text me-2"></i>
                                            Xác minh tài khoản ngay
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>
                                Email tài khoản
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required>
                            <small class="form-text text-muted">Nhập email bạn đã đăng ký để kiểm tra trạng thái xác minh</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Kiểm tra
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Quay lại trang đăng nhập
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
