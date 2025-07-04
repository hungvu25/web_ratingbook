<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Đăng ký tài khoản';
$error = '';
$success = '';

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif (!isValidEmail($email)) {
        $error = 'Email không hợp lệ';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm_password) {
        $error = 'Xác nhận mật khẩu không khớp';
    } else {
        try {
            // Kiểm tra username đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username hoặc email đã được sử dụng';
            } else {
                // Tạo tài khoản mới - DỮ LIỆU MỚI ĐƯỢC THÊM VÀO DATABASE
                $hashedPassword = hashPassword($password);
                $verificationToken = bin2hex(random_bytes(32));
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, name, full_name, email, password, verification_token, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                
                if ($stmt->execute([$username, $full_name, $full_name, $email, $hashedPassword, $verificationToken])) {
                    $success = 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.';
                    
                    // Log activity
                    logActivity('user_registered', "New user: $username");
                } else {
                    $error = 'Có lỗi xảy ra khi đăng ký';
                }
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
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Đăng ký tài khoản
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php else: ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Tên đăng nhập <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   required>
                            <small class="form-text text-muted">Chỉ được sử dụng chữ cái, số và dấu gạch dưới</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">
                                <i class="fas fa-id-card me-1"></i>
                                Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <small class="form-text text-muted">Tối thiểu 6 ký tự</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                Đăng ký
                            </button>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">
                        Đã có tài khoản? 
                        <a href="login.php" class="text-decoration-none">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Đăng nhập ngay
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password strength indicator -->
<script>
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password');
    
    // Simple password strength check
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    // Visual feedback could be added here
});

document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword && confirmPassword.length > 0) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>

<?php include 'includes/footer.php'; ?> 