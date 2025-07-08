<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Preload Font Awesome -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'";
    
    <!-- Roboto Local Font CSS - Load first -->
    <link href="assets/css/roboto-local.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Font Fallback CSS - MUST load second để tránh FOUT -->
    <link href="assets/css/font-fallback.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome - load with high priority -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Icon Fixes CSS - load after Font Awesome -->
    <link href="assets/css/icon-fixes.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Fix Duplicate Icons Script - Load immediately after Font Awesome -->
    <script src="fix-duplicate-icons.js?v=<?php echo time(); ?>"></script>
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Critical inline styles for immediate rendering -->
    <style>
        /* CRITICAL: Đảm bảo text và icon hiển thị ngay lập tức */
        *, *::before, *::after {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', 'Arial', sans-serif !important;
            font-display: swap;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        body, html {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: inherit !important;
        }
        
        /* Force icon visibility - hiển thị ngay lập tức */
        i, .fas, .far, .fab, [class*="fa-"] {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: "Font Awesome 6 Free", "FontAwesome", serif !important;
            display: inline-block !important;
            font-weight: 900 !important;
        }
        
        /* Specific variants */
        .far {
            font-weight: 400 !important;
        }
        
        .fab {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
        }
        
        /* Icon fallback */
        i[class*="fa-"]:empty::before {
            content: "•";
            font-family: inherit !important;
        }
        
        /* Loading state - vẫn hiển thị text */
        .font-loading, .font-loading * {
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Enhanced hero section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            margin-top: 76px;
        }
        
        /* Enhanced book cards */
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .book-cover {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .book-card:hover .book-cover {
            transform: scale(1.05);
        }
        
        .rating-stars {
            font-size: 0.9rem;
            color: #ffd700;
            text-shadow: 0 0 3px rgba(255, 215, 0, 0.5);
        }
        
        /* Enhanced navbar */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-2px);
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            margin-top: 50px;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
                margin-top: 56px;
            }
            
            .book-card {
                margin-bottom: 1rem;
            }
        }
        
        /* CRITICAL: Force Roboto on all elements immediately with highest specificity */
        html, body, *, *::before, *::after,
        html.roboto-loaded *, html.roboto-loaded body *,
        body.roboto-loaded *, .roboto-loaded * {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
        
        /* Force immediate visibility for all common elements */
        .navbar, .navbar *, .hero-section, .hero-section *, 
        .book-card, .book-card *, .btn, .btn *, 
        h1, h2, h3, h4, h5, h6, p, span, div, a {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
        
        /* Specific font weights for immediate application */
        h1, h2, .fw-bold, .card-title, .navbar-brand { 
            font-weight: 700 !important; 
        }
        .text-muted, .card-text { 
            font-weight: 400 !important; 
        }
    </style>
    
    <!-- Error Handler - Load first -->
    <script src="assets/js/error-handler.js?v=<?php echo time(); ?>"></script>
    
    <!-- Roboto Font Handler - Load early -->
    <script>
        // Immediate font fallback with Roboto priority
        document.documentElement.style.fontFamily = '"Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Arial", sans-serif';
        document.documentElement.style.visibility = 'visible';
        document.documentElement.style.opacity = '1';
        
        // Force apply to body and all elements
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.fontFamily = '"Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
            document.body.classList.add('roboto-loaded');
            document.documentElement.classList.add('roboto-loaded');
            
            // Apply to all existing elements
            const allElements = document.querySelectorAll('*');
            allElements.forEach(el => {
                el.style.fontFamily = '"Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
            });
            
            console.log('🎯 Roboto applied immediately to all elements');
        });
        
        // Force icons to be visible
        document.addEventListener('DOMContentLoaded', function() {
            const icons = document.querySelectorAll('i[class*="fa-"]');
            icons.forEach(icon => {
                icon.style.fontFamily = '"Font Awesome 6 Free"';
                icon.style.visibility = 'visible';
                icon.style.opacity = '1';
                icon.style.display = 'inline-block';
            });
            console.log('🎯 Icons forced visible:', icons.length);
        });
    </script>

    <script src="assets/js/roboto-handler.js?v=<?php echo time(); ?>" defer></script>
    <script src="assets/js/font-handler.js?v=<?php echo time(); ?>" defer></script>
    <!-- Tạm thời tắt icon-fallback để debug -->
    <!-- <script src="assets/js/icon-fallback.js?v=<?php echo time(); ?>" defer></script> -->
</head>
<body class="font-loading-roboto">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-reader me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php">
                            <i class="fas fa-book me-1"></i>Sách
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reviews.php">
                            <i class="fas fa-star me-1"></i>Đánh giá
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags me-1"></i>Thể loại
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                Xin chào, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user-circle me-2"></i>Hồ sơ
                                </a></li>
                                <li><a class="dropdown-item" href="profile.php#reviews">
                                    <i class="fas fa-star me-2"></i>Đánh giá của tôi
                                </a></li>
                                <li><a class="dropdown-item" href="profile.php#reading-list">
                                    <i class="fas fa-bookmark me-2"></i>Danh sách đọc
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/">
                                        <i class="fas fa-cog me-2"></i>Quản trị
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Đăng ký
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <?php displayFlashMessage(); ?>
    </main>
</body>
</html>