<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Admin - ' . SITE_NAME : 'Admin - ' . SITE_NAME; ?></title>
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Font Fallback CSS - Load first for immediate fallback -->
    <link href="../assets/css/font-fallback.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Admin Custom CSS -->
    <link href="assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Critical inline styles for immediate rendering -->
    <style>
        /* Critical font styling to prevent FOUT */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif !important;
            font-display: swap;
        }
        
        /* Loading state styling */
        .font-loading body {
            visibility: hidden;
        }
        
        .font-loaded body,
        .font-fallback body {
            visibility: visible;
        }
        
        /* Ensure proper layout even during font loading */
        .main-content .container-fluid {
            padding: 2.5rem 3rem !important;
            box-sizing: border-box !important;
        }
        
        .main-content {
            margin-left: 260px !important;
            transition: all 0.3s ease;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
            }
            
            .main-content .container-fluid {
                padding: 1.5rem 1rem !important;
            }
        }
        
        /* Enhanced animations */
        .card {
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    <!-- Smart Font Handler -->
    <script src="../assets/js/font-handler.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="font-loading">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="p-4">
            <a href="dashboard.php" class="navbar-brand d-flex align-items-center">
                <i class="fas fa-book-reader me-2"></i>
                <span><?php echo SITE_NAME; ?> Admin</span>
            </a>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a href="books.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'books.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i>
                    Quản lý sách
                </a>
            </li>
            
            <li class="nav-item">
                <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i>
                    Thể loại
                </a>
            </li>
            
            <li class="nav-item">
                <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    Người dùng
                </a>
            </li>
            
            <li class="nav-item">
                <a href="reviews.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'reviews.php' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i>
                    Đánh giá
                </a>
            </li>
            
            <li class="nav-item">
                <a href="add-book.php" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    Thêm sách mới
                </a>
            </li>
            
            <hr class="sidebar-divider my-3" style="border-color: rgba(255,255,255,0.2);">
            
            <li class="nav-item">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>
            </li>
            
            <li class="nav-item">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Đăng xuất
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-shield me-2"></i>
                        <?php 
                        $currentUser = getCurrentUser();
                        if ($currentUser) {
                            $displayName = $currentUser['full_name'] ?? $currentUser['name'] ?? $currentUser['username'] ?? 'Admin';
                        } else {
                            $displayName = 'Admin';
                        }
                        echo htmlspecialchars($displayName); 
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../profile.php">
                            <i class="fas fa-user me-2"></i>Hồ sơ
                        </a></li>
                        <li><a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2"></i>Cài đặt
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <?php displayFlashMessage(); ?>
            
            <!-- Content wrapper -->
            <div class="content-wrapper">
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 