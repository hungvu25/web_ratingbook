<?php
// Email debug test file
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/email_functions.php';

// Chỉ cho phép admin truy cập
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Set page title
$page_title = 'Kiểm Tra Gửi Email';

// Initialize variables
$result = null;
$email = '';
$message = '';
$email_system = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $email_system = $_POST['email_system'] ?? 'phpmailer';
    
    if (empty($email)) {
        $message = 'Vui lòng nhập địa chỉ email';
        $result = ['success' => false];
    } else {
        // Try to send a test email
        $token = bin2hex(random_bytes(8)); // Generate a small token for test
        
        if ($email_system == 'phpmailer') {
            // Use PHPMailer
            $result = sendEmail($email, 'Test Email từ Book Review Portal', "<p>Đây là email kiểm tra sử dụng PHPMailer.</p><p>Token: $token</p>");
        } else {
            // Use PHP mail() directly
            $result = sendEmailFallback($email, 'Test Email từ Book Review Portal', "<p>Đây là email kiểm tra sử dụng PHP mail().</p><p>Token: $token</p>");
        }
        
        // Store result message
        if ($result['success']) {
            $message = 'Email kiểm tra đã được gửi thành công. Vui lòng kiểm tra hộp thư đến (và thư rác).';
        } else {
            $message = 'Không thể gửi email kiểm tra: ' . ($result['message'] ?? 'Lỗi không xác định');
        }
        
        // Log the attempt
        error_log("Test email to $email result: " . ($result['success'] ? 'Success' : 'Failed'));
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        Kiểm Tra Hệ Thống Email
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($message)): ?>
                        <div class="alert <?php echo ($result && $result['success']) ? 'alert-success' : 'alert-danger'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ email nhận thư kiểm tra</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                            <div class="form-text">Nhập địa chỉ email để nhận thư kiểm tra.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hệ thống gửi email</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_system" id="phpmailer" value="phpmailer" 
                                       <?php echo ($email_system != 'mail') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="phpmailer">
                                    Sử dụng PHPMailer (SMTP Zoho)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_system" id="mail" value="mail"
                                       <?php echo ($email_system == 'mail') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="mail">
                                    Sử dụng PHP mail() (fallback)
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Gửi Email Kiểm Tra
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <h5>Cấu hình email</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>Host:</strong> <?php echo EMAIL_HOST; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Port:</strong> <?php echo EMAIL_PORT; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Username:</strong> <?php echo EMAIL_USERNAME; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>From Name:</strong> <?php echo EMAIL_FROM_NAME; ?>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <h5>PHP Mail Configuration</h5>
                        <?php
                        $mailConfig = ini_get('sendmail_path');
                        if (empty($mailConfig)) {
                            $mailConfig = 'Using PHP default mail configuration';
                        }
                        ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($mailConfig); ?>
                        </div>
                        
                        <h6>PHP Info (mail related)</h6>
                        <div class="card">
                            <div class="card-body bg-light" style="max-height: 200px; overflow-y: auto; font-size: 0.8rem;">
                                <pre><?php
                                    ob_start();
                                    phpinfo(INFO_CONFIGURATION);
                                    $phpinfo = ob_get_clean();
                                    
                                    // Extract mail related settings
                                    if (preg_match('/<h2>.*mail.*<\/h2>.*?<table.*?>(.*?)<\/table>/is', $phpinfo, $matches)) {
                                        echo htmlspecialchars($matches[1]);
                                    } else {
                                        echo "No mail configuration found in phpinfo()";
                                    }
                                ?></pre>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Kiểm tra lỗi</h5>
                        <div class="alert alert-secondary">
                            <p>Nếu không nhận được email, hãy kiểm tra các nguyên nhân sau:</p>
                            <ol>
                                <li>Kiểm tra thư mục spam/rác</li>
                                <li>Kiểm tra cấu hình SMTP trong file <code>includes/email_functions.php</code></li>
                                <li>Kiểm tra cấu hình PHP mail() trong file php.ini</li>
                                <li>Xem log lỗi của PHP hoặc web server</li>
                            </ol>
                            <p>Có thể cần cài đặt một local mail server như MailHog hoặc một SMTP server thật sự để gửi email.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="admin/dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
