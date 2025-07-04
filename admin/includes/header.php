<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Admin - ' . SITE_NAME : 'Admin - ' . SITE_NAME; ?></title>
    <meta name="description" content="Quản trị <?php echo SITE_NAME; ?>">
    
    <!-- Preload critical resources -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    
    <!-- CRITICAL: Local Fonts FIRST -->
    <link href="../assets/css/local-fonts.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Font Awesome Local Fallback -->
    <link href="../assets/css/fontawesome-local.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Font Fallback CSS -->
    <link href="../assets/css/font-fallback.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CDN với fallback -->
    <link id="fontawesome-cdn" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Chart.js for dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Admin CSS -->
    <link href="assets/css/admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- Critical inline styles -->
    <style>
        /* CRITICAL: Immediate visibility for admin panel */
        *, *::before, *::after {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Arial', sans-serif !important;
            font-display: swap;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        body, html {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: inherit !important;
        }
        
        /* Force icon visibility */
        i, .fas, .far, .fab, [class*="fa-"] {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: "Font Awesome 6 Free", "FontAwesome", serif !important;
        }
        
        /* Admin specific improvements */
        .sidebar {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Loading indicator for admin */
        .admin-loading {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 9999;
            display: none;
        }
        
        /* Force visibility for admin elements */
        .sidebar, .sidebar *, .main-content, .main-content *,
        .navbar, .navbar *, .stats-card, .stats-card *,
        .table, .table *, .btn, .btn * {
            visibility: visible !important;
            opacity: 1 !important;
            font-family: inherit !important;
        }
    </style>
    
    <!-- Enhanced Font Handler for Admin -->
    <script>
        // Immediate font setup for admin
        document.documentElement.style.fontFamily = "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Arial', sans-serif";
        document.documentElement.style.visibility = 'visible';
        document.documentElement.style.opacity = '1';
        document.documentElement.classList.add('admin-fonts-loading');
        
        // CDN Status checker for admin
        window.checkAdminCDNStatus = function() {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-cog';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const iconWidth = testIcon.offsetWidth;
            document.body.removeChild(testIcon);
            
            if (iconWidth < 10) {
                console.log('🔄 Admin: Font Awesome CDN failed, using local fallback');
                document.documentElement.classList.add('admin-fa-cdn-failed');
                const cdnLink = document.getElementById('fontawesome-cdn');
                if (cdnLink) cdnLink.remove();
            } else {
                console.log('✅ Admin: Font Awesome CDN loaded successfully');
                document.documentElement.classList.add('admin-fa-cdn-loaded');
            }
        };
        
        window.addEventListener('load', function() {
            setTimeout(checkAdminCDNStatus, 800);
        });
    </script>
    <script src="../assets/js/font-handler.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="font-loading admin-panel">
    <!-- Admin Loading Indicator -->
    <div id="adminFontStatus" class="admin-loading">
        Loading admin fonts...
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">
                            <i class="fas fa-cogs me-2"></i>
                            Admin Panel
                        </h5>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'books.php' ? 'active' : ''; ?>" href="books.php">
                                <i class="fas fa-book me-2"></i>
                                Quản lý sách
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                                <i class="fas fa-tags me-2"></i>
                                Thể loại
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>" href="reviews.php">
                                <i class="fas fa-star me-2"></i>
                                Đánh giá
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                                <i class="fas fa-cog me-2"></i>
                                Cài đặt
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home me-2"></i>
                                Về trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <?php displayFlashMessage(); ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 