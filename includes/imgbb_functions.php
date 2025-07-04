<?php
/**
 * ImgBB API functions for image upload
 */

// ImgBB API Key - Bạn cần đăng ký tài khoản tại https://api.imgbb.com/
// và thay thế API key này bằng API key của bạn

/**
 * Upload image to ImgBB
 * @param string $imageData Base64 encoded image data or file path
 * @param string $imageName Optional image name
 * @return array Response from ImgBB API
 */
function uploadToImgBB($imageData, $imageName = '') {
    $url = 'https://api.imgbb.com/1/upload';
    
    // Prepare POST data
    $postData = [
        'key' => IMGBB_API_KEY,
        'image' => $imageData
    ];
    
    if (!empty($imageName)) {
        $postData['name'] = $imageName;
    }
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'error' => 'cURL Error: ' . $error
        ];
    }
    
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $httpCode,
            'response' => $response
        ];
    }
    
    $result = json_decode($response, true);
    
    if (!$result) {
        return [
            'success' => false,
            'error' => 'Invalid JSON response'
        ];
    }
    
    return $result;
}

/**
 * Process uploaded file and upload to ImgBB
 * @param array $file $_FILES array element
 * @param string $imageName Optional image name
 * @return array Result with success status and image URL or error message
 */
function processAvatarUpload($file, $imageName = '') {
    // Validate file upload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error' => 'File upload error'
        ];
    }
    
    // Check file size (max 16MB for ImgBB)
    $maxSize = 16 * 1024 * 1024; // 16MB
    if ($file['size'] > $maxSize) {
        return [
            'success' => false,
            'error' => 'File quá lớn. Kích thước tối đa là 16MB'
        ];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'error' => 'Định dạng file không được hỗ trợ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'
        ];
    }
    
    // Convert image to base64
    $imageData = base64_encode(file_get_contents($file['tmp_name']));
    
    if (!$imageData) {
        return [
            'success' => false,
            'error' => 'Không thể đọc file ảnh'
        ];
    }
    
    // Upload to ImgBB
    $result = uploadToImgBB($imageData, $imageName);
    
    if (!$result['success']) {
        return [
            'success' => false,
            'error' => $result['data']['error']['message'] ?? 'Lỗi khi upload ảnh'
        ];
    }
    
    return [
        'success' => true,
        'url' => $result['data']['url'],
        'display_url' => $result['data']['display_url'],
        'thumb_url' => $result['data']['thumb']['url'],
        'medium_url' => $result['data']['medium']['url'] ?? $result['data']['url'],
        'delete_url' => $result['data']['delete_url']
    ];
}

/**
 * Process uploaded file and upload to ImgBB for book covers
 * @param array $file $_FILES array element
 * @param string $imageName Optional image name
 * @return array Result with success status and image URL or error message
 */
function processImageUpload($file, $imageName = '') {
    // Validate file upload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error' => 'File upload error'
        ];
    }
    
    // Check file size (max 16MB for ImgBB)
    $maxSize = 16 * 1024 * 1024; // 16MB
    if ($file['size'] > $maxSize) {
        return [
            'success' => false,
            'error' => 'File quá lớn. Kích thước tối đa là 16MB'
        ];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return [
            'success' => false,
            'error' => 'Định dạng file không được hỗ trợ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'
        ];
    }
    
    // Convert image to base64
    $imageData = base64_encode(file_get_contents($file['tmp_name']));
    
    if (!$imageData) {
        return [
            'success' => false,
            'error' => 'Không thể đọc file ảnh'
        ];
    }
    
    // Upload to ImgBB
    $result = uploadToImgBB($imageData, $imageName);
    
    if (!$result['success']) {
        return [
            'success' => false,
            'error' => $result['data']['error']['message'] ?? 'Lỗi khi upload ảnh'
        ];
    }
    
    return [
        'success' => true,
        'url' => $result['data']['url'],
        'display_url' => $result['data']['display_url'],
        'thumb_url' => $result['data']['thumb']['url'],
        'medium_url' => $result['data']['medium']['url'] ?? $result['data']['url'],
        'delete_url' => $result['data']['delete_url']
    ];
}

/**
 * Validate and resize image before upload
 * @param string $filePath Path to image file
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return string|false Base64 encoded resized image or false on error
 */
function resizeImageForAvatar($filePath, $maxWidth = 300, $maxHeight = 300) {
    $imageInfo = getimagesize($filePath);
    if (!$imageInfo) {
        return false;
    }
    
    list($width, $height, $type) = $imageInfo;
    
    // Create image resource based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($filePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($filePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($filePath);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Calculate new dimensions
    $aspectRatio = $width / $height;
    
    if ($width > $maxWidth || $height > $maxHeight) {
        if ($aspectRatio > 1) {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        } else {
            $newHeight = $maxHeight;
            $newWidth = $maxHeight * $aspectRatio;
        }
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // Create new image
    $resized = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Output to string
    ob_start();
    imagejpeg($resized, null, 90);
    $imageData = ob_get_contents();
    ob_end_clean();
    
    // Clean up memory
    imagedestroy($source);
    imagedestroy($resized);
    
    return base64_encode($imageData);
}
?> 