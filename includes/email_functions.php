<?php
/**
 * Email Functions for Zoho Mail
 */

// Email configuration for Zoho
define('EMAIL_HOST', 'smtp.zoho.com');
define('EMAIL_PORT', 587); // or 465 for SSL
define('EMAIL_USERNAME', 'account@dichvutot.site'); // Replace with your Zoho email
define('EMAIL_PASSWORD', '#4Vrorcl'); // Replace with your Zoho password or app password
define('EMAIL_FROM_NAME', 'Book Review Portal'); // Your website name
define('EMAIL_FROM_ADDRESS', 'account@dichvutot.site'); // Replace with your Zoho email

/**
 * Send an email using PHP's mail function with Zoho SMTP settings
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $altBody Plain text version of the email
 * @return array Success status and message
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    // Define headers
    $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_ADDRESS . ">\r\n";
    $headers .= "Reply-To: " . EMAIL_FROM_ADDRESS . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // For debugging - log email attempt
    error_log("Attempting to send email to: $to, Subject: $subject");
    
    // Use PHP's mail function
    $success = mail($to, $subject, $body, $headers);
    
    // Log the result for debugging
    if ($success) {
        error_log("Email to $to was accepted for delivery");
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    } else {
        error_log("Failed to send email to $to: " . error_get_last()['message']);
        return [
            'success' => false,
            'message' => 'Không thể gửi email: ' . (error_get_last()['message'] ?? 'Unknown error')
        ];
    }
}

/**
 * Send verification email to the user
 * 
 * @param string $email User's email address
 * @param string $username User's username
 * @param string $token Verification token
 * @param string $fullName User's full name
 * @return array Success status and message
 */
function sendVerificationEmail($email, $username, $token, $fullName = '') {
    $subject = 'Xác minh tài khoản của bạn';
    
    // Create verification link
    $verificationLink = 'http://' . $_SERVER['HTTP_HOST'] . '/verify-email.php?token=' . $token . '&email=' . urlencode($email);
    
    // Display name - use full name if available, otherwise username
    $displayName = !empty($fullName) ? $fullName : $username;
    
    // Create email body
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #4a6fdc;">Xác minh tài khoản</h2>
        </div>
        
        <p>Xin chào <strong>' . htmlspecialchars($displayName) . '</strong>,</p>
        
        <p>Cảm ơn bạn đã đăng ký tài khoản tại Book Review Portal. Để hoàn tất quá trình đăng ký, vui lòng xác minh email của bạn bằng cách nhấp vào nút bên dưới:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="' . $verificationLink . '" style="background-color: #4a6fdc; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Xác minh email
            </a>
        </div>
        
        <p>Hoặc bạn có thể sao chép và dán liên kết sau vào trình duyệt:</p>
        <p style="word-break: break-all;">' . $verificationLink . '</p>
        
        <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.</p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666;">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
    ';
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send welcome email to the user after verification
 * 
 * @param string $email User's email address
 * @param string $username User's username
 * @param string $fullName User's full name
 * @return array Success status and message
 */
function sendWelcomeEmail($email, $username, $fullName = '') {
    $subject = 'Chào mừng bạn đến với Book Review Portal!';
    
    // Display name - use full name if available, otherwise username
    $displayName = !empty($fullName) ? $fullName : $username;
    
    // Website URL
    $websiteUrl = 'http://' . $_SERVER['HTTP_HOST'];
    
    // Create email body
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #4a6fdc;">Chào mừng đến với Book Review Portal!</h2>
        </div>
        
        <p>Xin chào <strong>' . htmlspecialchars($displayName) . '</strong>,</p>
        
        <p>Cảm ơn bạn đã xác minh email và hoàn tất đăng ký tài khoản. Chúng tôi rất vui mừng chào đón bạn tham gia vào cộng đồng của chúng tôi!</p>
        
        <p>Với tài khoản của bạn, bạn có thể:</p>
        <ul style="padding-left: 20px; line-height: 1.5;">
            <li>Đọc và viết đánh giá sách</li>
            <li>Lưu sách yêu thích vào danh sách cá nhân</li>
            <li>Tương tác với các thành viên khác</li>
            <li>Nhận thông báo về sách mới và các chủ đề bạn quan tâm</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="' . $websiteUrl . '" style="background-color: #4a6fdc; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Khám phá ngay
            </a>
        </div>
        
        <p>Nếu bạn có bất kỳ câu hỏi hoặc góp ý nào, đừng ngần ngại liên hệ với chúng tôi.</p>
        
        <p>Chúc bạn có những trải nghiệm tuyệt vời!</p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666;">
            <p>Email này được gửi tự động từ Book Review Portal.</p>
        </div>
    </div>
    ';
    
    return sendEmail($email, $subject, $body);
}
