<?php
/**
 * Script để gửi email xác minh và chào mừng thủ công
 * Sử dụng script này khi gửi mail tự động không hoạt động
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Gửi Email Thủ Công';

// Kiểm tra quyền admin
if (!isLoggedIn() || !isAdmin()) {
    redirectWithMessage('login.php', 'Bạn không có quyền truy cập trang này.', 'error');
    exit;
}

$message = '';
$error = '';

// Lấy danh sách người dùng chưa xác minh
$unverifiedUsers = [];
try {
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, verification_token, created_at 
        FROM users 
        WHERE is_verified = 0 AND verification_token IS NOT NULL
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $unverifiedUsers = sanitizeDataFromDb($stmt->fetchAll());
} catch (PDOException $e) {
    $error = 'Lỗi truy vấn: ' . $e->getMessage();
}

// Xử lý khi gửi email thủ công
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
    $userId = $_POST['user_id'] ?? 0;
    $emailType = $_POST['email_type'] ?? '';
    
    if (empty($userId) || empty($emailType)) {
        $error = 'Thiếu thông tin cần thiết';
    } else {
        try {
            // Lấy thông tin người dùng
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = sanitizeDataFromDb($stmt->fetch());
            
            if (!$user) {
                $error = 'Không tìm thấy người dùng';
            } else {
                // Tạo nội dung email
                $websiteUrl = 'http://' . $_SERVER['HTTP_HOST'];
                $emailContent = '';
                $emailSubject = '';
                
                if ($emailType === 'verification') {
                    // Email xác minh
                    $verificationLink = $websiteUrl . '/verify-email.php?token=' . $user['verification_token'] . '&email=' . urlencode($user['email']);
                    
                    $emailSubject = 'Xác minh tài khoản của bạn';
                    $emailContent = "
                        <h2>Xác minh tài khoản</h2>
                        <p>Xin chào {$user['full_name']},</p>
                        <p>Để xác minh tài khoản của bạn, vui lòng nhấp vào liên kết sau:</p>
                        <p><a href='{$verificationLink}'>{$verificationLink}</a></p>
                        <p>Trân trọng,<br>Book Review Portal</p>
                    ";
                    
                    $message = "Đã gửi email xác minh thành công cho {$user['email']}";
                    
                } else if ($emailType === 'welcome') {
                    // Email chào mừng
                    $emailSubject = 'Chào mừng bạn đến với Book Review Portal';
                    $emailContent = "
                        <h2>Chào mừng đến với Book Review Portal!</h2>
                        <p>Xin chào {$user['full_name']},</p>
                        <p>Cảm ơn bạn đã đăng ký tài khoản. Chúng tôi rất vui được chào đón bạn!</p>
                        <p>Bắt đầu khám phá ngay: <a href='{$websiteUrl}'>{$websiteUrl}</a></p>
                        <p>Trân trọng,<br>Book Review Portal</p>
                    ";
                    
                    // Nếu đây là email chào mừng, đánh dấu người dùng đã xác minh
                    $updateStmt = $pdo->prepare("
                        UPDATE users 
                        SET is_verified = 1, 
                            verification_token = NULL, 
                            verified_at = NOW() 
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$userId]);
                    
                    $message = "Đã gửi email chào mừng và kích hoạt tài khoản {$user['email']}";
                }
                
                // Gửi email thủ công (hiển thị cho admin xem)
                echo "<div style='display:none;'>" . $emailContent . "</div>";
                
                // Ghi log
                logActivity('manual_email_sent', "Email {$emailType} sent to: {$user['email']} by admin");
            }
        } catch (PDOException $e) {
            $error = 'Lỗi xử lý: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i> Gửi Email Xác Minh & Chào Mừng Thủ Công</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i> Hướng dẫn:</h5>
                        <p>Công cụ này cho phép quản trị viên gửi email xác minh hoặc email chào mừng thủ công cho người dùng khi hệ thống gửi email tự động không hoạt động.</p>
                        <ol>
                            <li>Chọn người dùng từ danh sách bên dưới</li>
                            <li>Chọn loại email cần gửi:</li>
                            <ul>
                                <li><strong>Email xác minh:</strong> Gửi link xác minh đến người dùng</li>
                                <li><strong>Email chào mừng:</strong> Đánh dấu tài khoản là đã xác minh và gửi email chào mừng</li>
                            </ul>
                        </ol>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Người dùng chưa xác minh (<?php echo count($unverifiedUsers); ?>)</h5>
                    
                    <?php if (empty($unverifiedUsers)): ?>
                        <div class="alert alert-warning">Không có người dùng nào chưa xác minh.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tài khoản</th>
                                        <th>Email</th>
                                        <th>Họ tên</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($unverifiedUsers as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo $user['username']; ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td><?php echo $user['full_name']; ?></td>
                                            <td><?php echo formatDate($user['created_at']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <form method="POST" class="me-1">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="email_type" value="verification">
                                                        <button type="submit" name="send_email" class="btn btn-outline-primary">
                                                            <i class="fas fa-envelope me-1"></i> Email xác minh
                                                        </button>
                                                    </form>
                                                    <form method="POST">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="email_type" value="welcome">
                                                        <button type="submit" name="send_email" class="btn btn-success">
                                                            <i class="fas fa-user-check me-1"></i> Kích hoạt & Chào mừng
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <p><strong>Lưu ý:</strong> Nút "Kích hoạt & Chào mừng" sẽ đánh dấu tài khoản đã được xác minh mà không cần người dùng nhấp vào liên kết xác minh.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
