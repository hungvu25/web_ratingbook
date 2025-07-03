    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-book-reader me-2"></i>
                        <?php echo SITE_NAME; ?>
                    </h5>
                    <p class="mb-3"><?php echo SITE_DESCRIPTION; ?></p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3" title="Facebook">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-white me-3" title="Twitter">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-white me-3" title="Instagram">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-white" title="YouTube">
                            <i class="fab fa-youtube fa-lg"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Liên kết</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="text-white-50 text-decoration-none">Trang chủ</a>
                        </li>
                        <li class="mb-2">
                            <a href="books.php" class="text-white-50 text-decoration-none">Sách</a>
                        </li>
                        <li class="mb-2">
                            <a href="reviews.php" class="text-white-50 text-decoration-none">Đánh giá</a>
                        </li>
                        <li class="mb-2">
                            <a href="categories.php" class="text-white-50 text-decoration-none">Thể loại</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="help.php" class="text-white-50 text-decoration-none">Trợ giúp</a>
                        </li>
                        <li class="mb-2">
                            <a href="contact.php" class="text-white-50 text-decoration-none">Liên hệ</a>
                        </li>
                        <li class="mb-2">
                            <a href="privacy.php" class="text-white-50 text-decoration-none">Bảo mật</a>
                        </li>
                        <li class="mb-2">
                            <a href="terms.php" class="text-white-50 text-decoration-none">Điều khoản</a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Đăng ký nhận tin</h6>
                    <p class="text-white-50 mb-3">Nhận thông báo về sách mới và đánh giá hay nhất</p>
                    <form class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email của bạn">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <small class="text-white-50">
                        Chúng tôi tôn trọng quyền riêng tư của bạn và không spam.
                    </small>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-white-50">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. 
                        Tất cả quyền được bảo lưu.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-white-50">
                        <i class="fas fa-heart text-danger"></i>
                        Được tạo với tình yêu sách
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle" 
            style="width: 50px; height: 50px; display: none; z-index: 1000;" title="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Scroll to top functionality
        window.addEventListener('scroll', function() {
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.display = 'block';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });

        document.getElementById('scrollTopBtn').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html> 