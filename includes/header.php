<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    
    <!-- Preload critical resources cho CDN -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    
    <!-- CRITICAL: Local Fonts FIRST để tránh FOUT -->
    <link href="assets/css/local-fonts.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Font Awesome Local Fallback -->
    <link href="assets/css/fontawesome-local.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Font Fallback CSS - Enhanced system -->
    <link href="assets/css/font-fallback.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CDN với fallback detection -->
    <link id="fontawesome-cdn" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Critical inline styles for immediate rendering -->
    <style>
        /* CRITICAL: Đảm bảo text và icon hiển thị ngay lập tức */
        *, *::before, *::after {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', 'Arial', sans-serif !important;
            font-display: swap;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        body, html {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: inherit !important;
        }
        
        /* Force icon visibility với local fonts */
        i, .fas, .far, .fab, [class*="fa-"] {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: "Font Awesome 6 Free", "FontAwesome", serif !important;
        }
        
        /* Icon fallback for worst case */
        .fa-icon-fallback i[class*="fa-"]:empty::before {
            content: "•";
            font-family: inherit !important;
        }
        
        /* Status indicators for debugging */
        .font-status {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
            display: none; /* Ẩn trong production */
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
        
        /* Force immediate visibility for all common elements */
        .navbar, .navbar *, .hero-section, .hero-section *, 
        .book-card, .book-card *, .btn, .btn *, 
        h1, h2, h3, h4, h5, h6, p, span, div, a {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: inherit !important;
        }
    </style>
    
    <!-- Enhanced Font Handler - Load early -->
    <script>
        // Immediate font fallback setup
        document.documentElement.style.fontFamily = "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Arial', sans-serif";
        document.documentElement.style.visibility = 'visible';
        document.documentElement.style.opacity = '1';
        
        // Set loading state
        document.documentElement.classList.add('fonts-loading');
        
        // CDN Fallback checker
        window.checkCDNStatus = function() {
            // Check if Font Awesome CDN loaded
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-home';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const iconWidth = testIcon.offsetWidth;
            document.body.removeChild(testIcon);
            
            // If CDN failed, add fallback class
            if (iconWidth < 10) {
                console.log('🔄 Font Awesome CDN failed, using local fallback');
                document.documentElement.classList.add('fa-cdn-failed');
                
                // Remove CDN link to prevent further loading attempts
                const cdnLink = document.getElementById('fontawesome-cdn');
                if (cdnLink) cdnLink.remove();
            } else {
                console.log('✅ Font Awesome CDN loaded successfully');
                document.documentElement.classList.add('fa-cdn-loaded');
            }
        };
        
        // Check CDN status after page load
        window.addEventListener('load', function() {
            setTimeout(checkCDNStatus, 1000);
        });
    </script>
    <script src="assets/js/font-handler.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="font-loading">
    <!-- Font Status Indicator (Development only) -->
    <div id="fontStatus" class="font-status">
        Loading fonts...
    </div>

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
                                Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>
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
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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