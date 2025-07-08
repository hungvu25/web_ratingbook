<?php
// Email debug test file
require_once 'includes/email_functions.php';

// Set page title
$page_title = 'Email Test';

// Initialize variables
$result = null;
$email = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $message = 'Please enter an email address';
    } else {
        // Try to send a test email
        $token = bin2hex(random_bytes(8)); // Generate a small token for test
        $result = sendVerificationEmail($email, 'testuser', $token, 'Test User');
        
        // Store result message
        if ($result['success']) {
            $message = 'Test email sent successfully. Please check your inbox (and spam folder).';
        } else {
            $message = 'Failed to send test email: ' . $result['message'];
        }
        
        // Log the attempt
        error_log("Test email to $email result: " . ($result['success'] ? 'Success' : 'Failed'));
    }
}

// Include header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        Email Test
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
                            <label for="email" class="form-label">Test Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                            <div class="form-text">Enter an email address to test the verification email.</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Test Email
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <h5>Email Configuration</h5>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
