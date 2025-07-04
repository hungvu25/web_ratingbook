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
    
    <!-- Font Fallback CSS - MUST load first để tránh FOUT -->
    <link href="assets/css/font-fallback.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Critical inline styles for immediate rendering -->
    <style>
        /* CRITICAL: Đảm bảo text hiển thị ngay lập tức */
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', 'Arial', sans-serif !important;
            font-display: swap;
            visibility: visible !important;
        }
        
        body {
            visibility: visible !important;
            opacity: 1 !important;
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
    </style>
    
    <!-- Smart Font Handler -->
    <script src="assets/js/font-handler.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="font-loading">
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
                    <?php if (isLoggedIn()): ?>
                        <?php 
                        $currentUser = getCurrentUser(); 
                        $displayName = 'User';
                        if ($currentUser) {
                            $displayName = $currentUser['full_name'] ?? $currentUser['username'] ?? 'User';
                        }
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo getUserAvatar($currentUser); ?>" 
                                     alt="Avatar" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <?php echo htmlspecialchars($displayName); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Hồ sơ
                                </a></li>
                                <li><a class="dropdown-item" href="my-reviews.php">
                                    <i class="fas fa-star me-2"></i>Đánh giá của tôi
                                </a></li>
                                <li><a class="dropdown-item" href="reading-list.php">
                                    <i class="fas fa-bookmark me-2"></i>Danh sách đọc
                                </a></li>
                                <?php if (isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/">
                                    <i class="fas fa-cog me-2"></i>Quản trị
                                </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
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