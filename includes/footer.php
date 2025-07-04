    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p class="mb-0"><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                    <small class="text-muted">Phiên bản 1.0.0</small>
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

    <!-- Font loading completion handler -->
    <script>
        // Ensure visibility after font loading
        document.addEventListener('DOMContentLoaded', function() {
            // Remove loading class after a maximum of 5 seconds
            setTimeout(function() {
                document.body.classList.remove('font-loading');
                document.body.classList.add('font-loaded');
            }, 5000);
            
            // Listen for font events
            window.addEventListener('fontFallbackActivated', function() {
                document.body.classList.remove('font-loading');
                console.log('🎨 Font fallback đã được kích hoạt');
            });
            
            // Additional font loading detection
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    document.body.classList.remove('font-loading');
                    document.body.classList.add('font-loaded');
                });
            }
        });
    </script>
</body>
</html> 