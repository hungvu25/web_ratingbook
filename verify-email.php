<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/email_functions.php';

$page_title = 'Xác minh email';
$error = '';
$success = '';

// Kiểm tra token từ link xác minh
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    
    try {
        // Kiểm tra xem token và email có hợp lệ không
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_token = ? AND is_verified = 0");
        $stmt->execute([$email, $token]);
        $user = sanitizeDataFromDb($stmt->fetch());
        
        if ($user) {
            // Cập nhật trạng thái xác minh
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, verified_at = NOW() WHERE id = ?");
            
            if ($stmt->execute([$user['id']])) {
                $success = 'Xác minh email thành công! Bạn có thể đăng nhập vào tài khoản ngay bây giờ.';
                
                // Log activity
                logActivity('user_verified', "User verified email: {$user['username']}");
                
                // Gửi email chào mừng
                $result = sendWelcomeEmail($user['email'], $user['username'], $user['full_name']);
                
                // Log email result
                if (!$result['success']) {
                    error_log("Failed to send welcome email to: {$user['email']} - {$result['message']}");
                }
                
                // Chuyển hướng người dùng sau 5 giây
                header("refresh:5;url=login.php");
            } else {
                $error = 'Có lỗi xảy ra khi xác minh tài khoản của bạn.';
            }
        } else {
            // Kiểm tra xem người dùng đã xác minh chưa
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_verified = 1");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Tài khoản này đã được xác minh trước đó. Vui lòng đăng nhập.';
            } else {
                $error = 'Link xác minh không hợp lệ hoặc đã hết hạn.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Lỗi hệ thống: ' . $e->getMessage();
    }
} else {
    $error = 'Thiếu thông tin xác minh.';
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        Xác minh email
                    </h4>
                </div>
                <div class="card-body p-4 text-center">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                        <div class="mt-4">
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Đến trang đăng nhập
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                        </div>
                        <div class="verification-success mt-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h3 class="mt-3">Xác minh thành công!</h3>
                            <p>Tài khoản của bạn đã được kích hoạt.</p>
                            <p>Bạn sẽ được chuyển đến trang đăng nhập sau 5 giây...</p>
                            <div class="mt-3">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Đăng nhập ngay
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
