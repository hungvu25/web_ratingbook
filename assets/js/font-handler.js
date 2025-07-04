/**
 * Font Handler - Xử lý việc load font thông minh
 * Đảm bảo font hiển thị tốt trên mọi hosting và điều kiện mạng
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        googleFontUrl: 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        fallbackTimeout: 3000,
        retryAttempts: 2,
        testString: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    };
    
    // Font loading state
    let fontLoadingState = {
        poppinsLoaded: false,
        localPoppinsFound: false,
        fallbackActive: false
    };
    
    /**
     * Kiểm tra xem có font Poppins local không
     */
    function checkLocalPoppins() {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // Test với font mặc định
            ctx.font = '12px monospace';
            const defaultWidth = ctx.measureText(CONFIG.testString).width;
            
            // Test với Poppins local
            ctx.font = '12px Poppins, monospace';
            const poppinsWidth = ctx.measureText(CONFIG.testString).width;
            
            const hasLocalPoppins = Math.abs(defaultWidth - poppinsWidth) > 0.1;
            
            fontLoadingState.localPoppinsFound = hasLocalPoppins;
            
            if (hasLocalPoppins) {
                console.log('✅ Font Poppins local được tìm thấy');
                document.documentElement.classList.add('poppins-local');
                resolve(true);
            } else {
                console.log('⚠️ Không tìm thấy font Poppins local');
                resolve(false);
            }
        });
    }
    
    /**
     * Load Google Fonts với retry mechanism
     */
    function loadGoogleFonts(attempt = 1) {
        return new Promise((resolve, reject) => {
            console.log(`🔄 Đang load Google Fonts (lần thử ${attempt}/${CONFIG.retryAttempts + 1})`);
            
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = CONFIG.googleFontUrl;
            
            const timeout = setTimeout(() => {
                console.log('⏰ Timeout khi load Google Fonts');
                link.remove();
                
                if (attempt <= CONFIG.retryAttempts) {
                    console.log(`🔄 Thử lại lần ${attempt + 1}`);
                    loadGoogleFonts(attempt + 1).then(resolve).catch(reject);
                } else {
                    reject(new Error('Google Fonts load failed after retries'));
                }
            }, CONFIG.fallbackTimeout);
            
            link.onload = () => {
                clearTimeout(timeout);
                console.log('✅ Google Fonts đã load thành công');
                fontLoadingState.poppinsLoaded = true;
                document.documentElement.classList.add('poppins-loaded');
                resolve(true);
            };
            
            link.onerror = () => {
                clearTimeout(timeout);
                console.log('❌ Lỗi khi load Google Fonts');
                link.remove();
                
                if (attempt <= CONFIG.retryAttempts) {
                    loadGoogleFonts(attempt + 1).then(resolve).catch(reject);
                } else {
                    reject(new Error('Google Fonts load failed'));
                }
            };
            
            document.head.appendChild(link);
        });
    }
    
    /**
     * Áp dụng fallback font
     */
    function applyFallback() {
        console.log('🔧 Áp dụng fallback font');
        fontLoadingState.fallbackActive = true;
        document.body.classList.add('font-fallback');
        document.documentElement.classList.add('font-fallback');
        
        // Dispatch event để các component khác biết
        window.dispatchEvent(new CustomEvent('fontFallbackActivated'));
    }
    
    /**
     * Verify font đã load thành công
     */
    function verifyFontLoaded() {
        return new Promise((resolve) => {
            if (!document.fonts) {
                resolve(false);
                return;
            }
            
            document.fonts.ready.then(() => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Test width với fallback font
                ctx.font = '12px Arial, sans-serif';
                const fallbackWidth = ctx.measureText(CONFIG.testString).width;
                
                // Test width với Poppins
                ctx.font = '12px Poppins, Arial, sans-serif';
                const poppinsWidth = ctx.measureText(CONFIG.testString).width;
                
                const fontLoaded = Math.abs(fallbackWidth - poppinsWidth) > 0.1;
                
                console.log(`📊 Font verification: ${fontLoaded ? 'PASSED' : 'FAILED'}`);
                resolve(fontLoaded);
            });
        });
    }
    
    /**
     * Main font loading logic
     */
    async function initializeFonts() {
        console.log('🚀 Khởi tạo font handler');
        
        try {
            // Step 1: Check local Poppins
            const hasLocalPoppins = await checkLocalPoppins();
            
            if (hasLocalPoppins) {
                // Sử dụng local font và kết thúc
                return;
            }
            
            // Step 2: Try loading Google Fonts
            try {
                await loadGoogleFonts();
                
                // Step 3: Verify font actually loaded
                const verified = await verifyFontLoaded();
                
                if (!verified) {
                    console.log('⚠️ Font verification failed, using fallback');
                    applyFallback();
                }
                
            } catch (error) {
                console.log('❌ Google Fonts failed completely:', error.message);
                applyFallback();
            }
            
        } catch (error) {
            console.error('💥 Font initialization error:', error);
            applyFallback();
        }
    }
    
    /**
     * Network-aware font loading
     */
    function handleNetworkConditions() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            // Nếu mạng chậm, áp dụng fallback ngay
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                console.log('🐌 Mạng chậm được phát hiện, sử dụng fallback');
                applyFallback();
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Preload critical fonts
     */
    function preloadFonts() {
        const preloadLink = document.createElement('link');
        preloadLink.rel = 'preload';
        preloadLink.as = 'style';
        preloadLink.href = CONFIG.googleFontUrl;
        preloadLink.onload = function() {
            this.onload = null;
            this.rel = 'stylesheet';
        };
        document.head.appendChild(preloadLink);
    }
    
    /**
     * Initialize when DOM is ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Check network conditions first
        if (handleNetworkConditions()) {
            return;
        }
        
        // Start font loading process
        initializeFonts();
    }
    
    // Expose to global scope for debugging
    window.FontHandler = {
        state: fontLoadingState,
        applyFallback: applyFallback,
        reinitialize: initializeFonts
    };
    
    // Start initialization
    init();
    
})(); 