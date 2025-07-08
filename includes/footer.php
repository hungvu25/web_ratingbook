    </main>

    <!-- JavaScript xử lý Vietnamese text -->
    <script src="/assets/js/vietnamese-text.js?v=<?php echo time(); ?>"></script>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book-reader me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="mb-2"><?php echo SITE_DESCRIPTION; ?></p>
                    <p class="small text-muted">© 2024 BookReview. All rights reserved.</p>
                </div>
                <div class="col-md-3">
                    <h6><i class="fas fa-link me-2"></i>Liên kết hữu ích</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a></li>
                        <li><a href="books.php" class="text-light text-decoration-none">
                            <i class="fas fa-book me-1"></i>Tất cả sách
                        </a></li>
                        <li><a href="categories.php" class="text-light text-decoration-none">
                            <i class="fas fa-tags me-1"></i>Thể loại
                        </a></li>
                        <li><a href="reviews.php" class="text-light text-decoration-none">
                            <i class="fas fa-star me-1"></i>Đánh giá
                        </a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6><i class="fas fa-info-circle me-2"></i>Thông tin</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">
                            <i class="fas fa-envelope me-1"></i>Liên hệ
                        </a></li>
                        <li><a href="#" class="text-light text-decoration-none">
                            <i class="fas fa-shield-alt me-1"></i>Chính sách
                        </a></li>
                        <li><a href="#" class="text-light text-decoration-none">
                            <i class="fas fa-question-circle me-1"></i>Hỗ trợ
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle" 
            style="width: 50px; height: 50px; display: none; z-index: 1000;" title="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Alert auto-hide -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    <!-- Font loading completion handler -->
    <script>
        // Ensure visibility after font loading
        document.addEventListener('DOMContentLoaded', function() {
            // Remove loading class after a maximum of 3 seconds (reduced)
            setTimeout(function() {
                document.body.classList.remove('font-loading');
                document.body.classList.add('font-loaded');
                
                // Force visibility for all elements
                document.body.style.visibility = 'visible';
                document.body.style.opacity = '1';
                
                // Force icon visibility
                const icons = document.querySelectorAll('i[class*="fa-"]');
                icons.forEach(icon => {
                    icon.style.visibility = 'visible';
                    icon.style.opacity = '1';
                });
                
                console.log('🎨 Font loading completed');
            }, 3000);
            
            // Listen for font events
            window.addEventListener('fontFallbackActivated', function() {
                document.body.classList.remove('font-loading');
                document.body.classList.add('font-loaded');
                console.log('🎨 Font fallback đã được kích hoạt');
            });
            
            // Additional font loading detection
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    document.body.classList.remove('font-loading');
                    document.body.classList.add('font-loaded');
                    console.log('🎨 Document fonts ready');
                });
            }
            
            // Immediate fallback for icons if Font Awesome fails
            setTimeout(function() {
                const icons = document.querySelectorAll('i[class*="fa-"]');
                icons.forEach(icon => {
                    if (window.getComputedStyle(icon).fontFamily.indexOf('Font Awesome') === -1) {
                        // Font Awesome didn't load, apply emoji fallbacks
                        const className = icon.className;
                        if (className.includes('fa-book')) icon.textContent = '📚';
                        else if (className.includes('fa-home')) icon.textContent = '🏠';
                        else if (className.includes('fa-star')) icon.textContent = '⭐';
                        else if (className.includes('fa-tags')) icon.textContent = '🏷️';
                        else if (className.includes('fa-user')) icon.textContent = '👤';
                        else if (className.includes('fa-envelope')) icon.textContent = '✉️';
                        else if (className.includes('fa-shield')) icon.textContent = '🛡️';
                        else if (className.includes('fa-question')) icon.textContent = '❓';
                        else if (className.includes('fa-info')) icon.textContent = 'ℹ️';
                        else if (className.includes('fa-link')) icon.textContent = '🔗';
                        else if (className.includes('fa-sign-in')) icon.textContent = '🔑';
                        else if (className.includes('fa-sign-out')) icon.textContent = '🚪';
                        else if (className.includes('fa-cog')) icon.textContent = '⚙️';
                        else if (className.includes('fa-plus')) icon.textContent = '➕';
                        else icon.textContent = '•';
                    }
                });
            }, 2000);
        });
        
        // Force immediate visibility
        document.body.style.visibility = 'visible';
        document.body.style.opacity = '1';
    </script>
</body>
</html> 