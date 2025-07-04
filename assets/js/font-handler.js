/**
 * Font Handler - Tối ưu đặc biệt cho InfinityFree hosting
 * Xử lý font loading với fallback thông minh và nhanh chóng
 */

(function() {
    'use strict';
    
    // Configuration - Rất tích cực cho InfinityFree
    const CONFIG = {
        googleFontUrl: 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        fallbackTimeout: 1500, // Chỉ 1.5s cho hosting chậm
        retryAttempts: 0, // Không retry để tránh chậm trễ
        testString: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        maxLoadTime: 3000, // Chỉ 3s tối đa
        emergencyTimeout: 2000 // Emergency fallback sau 2s
    };
    
    // Font loading state
    let fontLoadingState = {
        poppinsLoaded: false,
        localPoppinsFound: false,
        fallbackActive: false,
        initialized: false,
        emergencyActive: false
    };
    
    /**
     * Áp dụng fallback font ngay lập tức - ưu tiên hiển thị
     */
    function applyFallback() {
        if (fontLoadingState.fallbackActive) return;
        
        console.log('🔧 Áp dụng fallback font cho InfinityFree');
        fontLoadingState.fallbackActive = true;
        
        // Áp dụng classes ngay lập tức
        document.body.classList.add('font-fallback');
        document.documentElement.classList.add('font-fallback');
        document.documentElement.classList.remove('font-loading');
        document.documentElement.classList.add('font-loaded');
        
        // Force hiển thị ngay
        document.body.style.visibility = 'visible';
        document.body.style.opacity = '1';
        
        // Đảm bảo tất cả elements hiển thị
        const allElements = document.querySelectorAll('*');
        allElements.forEach(el => {
            el.style.visibility = 'visible';
            el.style.fontFamily = 'var(--font-fallback)';
        });
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('fontFallbackActivated'));
    }
    
    /**
     * Emergency fallback - kích hoạt ngay nếu mọi thứ fail
     */
    function emergencyFallback() {
        if (fontLoadingState.emergencyActive) return;
        
        console.log('🚨 Emergency fallback cho InfinityFree hosting');
        fontLoadingState.emergencyActive = true;
        
        // Override tất cả font styles
        const emergencyCSS = `
            * {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Arial', sans-serif !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        `;
        
        const style = document.createElement('style');
        style.textContent = emergencyCSS;
        document.head.appendChild(style);
        
        applyFallback();
    }
    
    /**
     * Kiểm tra font local nhanh chóng
     */
    function checkLocalPoppins() {
        return new Promise((resolve) => {
            try {
                // Timeout nhanh để không block
                const timeoutId = setTimeout(() => {
                    resolve(false);
                }, 300);
                
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                if (!ctx) {
                    clearTimeout(timeoutId);
                    resolve(false);
                    return;
                }
                
                ctx.font = '12px monospace';
                const defaultWidth = ctx.measureText('test').width;
                
                ctx.font = '12px Poppins, monospace';
                const poppinsWidth = ctx.measureText('test').width;
                
                const hasLocalPoppins = Math.abs(defaultWidth - poppinsWidth) > 0.1;
                
                clearTimeout(timeoutId);
                fontLoadingState.localPoppinsFound = hasLocalPoppins;
                
                if (hasLocalPoppins) {
                    console.log('✅ Font Poppins local được tìm thấy');
                    document.documentElement.classList.add('poppins-local');
                    document.documentElement.classList.remove('font-loading');
                    document.documentElement.classList.add('font-loaded');
                    resolve(true);
                } else {
                    console.log('⚠️ Không tìm thấy font Poppins local');
                    resolve(false);
                }
            } catch (error) {
                console.log('❌ Lỗi khi kiểm tra local font:', error);
                resolve(false);
            }
        });
    }
    
    /**
     * Load Google Fonts với timeout rất ngắn
     */
    function loadGoogleFonts() {
        return new Promise((resolve, reject) => {
            console.log('🔄 Thử load Google Fonts (timeout 1.5s)');
            
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = CONFIG.googleFontUrl;
            link.crossOrigin = 'anonymous';
            
            const timeout = setTimeout(() => {
                console.log('⏰ Google Fonts timeout - chuyển sang fallback');
                link.remove();
                reject(new Error('Google Fonts timeout'));
            }, CONFIG.fallbackTimeout);
            
            link.onload = () => {
                clearTimeout(timeout);
                console.log('✅ Google Fonts loaded thành công');
                fontLoadingState.poppinsLoaded = true;
                document.documentElement.classList.add('poppins-loaded');
                document.documentElement.classList.remove('font-loading');
                document.documentElement.classList.add('font-loaded');
                resolve(true);
            };
            
            link.onerror = () => {
                clearTimeout(timeout);
                console.log('❌ Google Fonts load failed');
                link.remove();
                reject(new Error('Google Fonts failed'));
            };
            
            document.head.appendChild(link);
        });
    }
    
    /**
     * Kiểm tra hosting environment
     */
    function detectHostingEnvironment() {
        const hostname = window.location.hostname;
        const isInfinityFree = hostname.includes('infy.uk') || 
                              hostname.includes('infinityfree') || 
                              hostname.includes('epizy.com') ||
                              hostname.includes('unaux.com');
        
        if (isInfinityFree) {
            console.log('🌐 InfinityFree hosting detected - sử dụng fallback aggressive');
            return 'infinityfree';
        }
        
        return 'other';
    }
    
    /**
     * Main initialization - Tối ưu cho InfinityFree
     */
    async function initializeFonts() {
        if (fontLoadingState.initialized) return;
        fontLoadingState.initialized = true;
        
        console.log('🚀 Khởi tạo font system cho InfinityFree');
        
        const hostingType = detectHostingEnvironment();
        
        // Emergency timeout luôn có
        const emergencyTimer = setTimeout(() => {
            if (!fontLoadingState.fallbackActive && !fontLoadingState.poppinsLoaded) {
                console.log('⏰ Emergency timeout - force fallback');
                emergencyFallback();
            }
        }, CONFIG.emergencyTimeout);
        
        // Global timeout
        const globalTimer = setTimeout(() => {
            if (!fontLoadingState.fallbackActive && !fontLoadingState.poppinsLoaded) {
                console.log('⏰ Global timeout - áp dụng fallback');
                applyFallback();
            }
        }, CONFIG.maxLoadTime);
        
        try {
            // Nếu là InfinityFree, aggressive fallback
            if (hostingType === 'infinityfree') {
                console.log('🏃‍♂️ InfinityFree - áp dụng fallback ngay');
                setTimeout(applyFallback, 500); // Fallback sau 0.5s
            }
            
            // Kiểm tra local font nhanh
            const hasLocalPoppins = await checkLocalPoppins();
            
            if (hasLocalPoppins) {
                clearTimeout(emergencyTimer);
                clearTimeout(globalTimer);
                return;
            }
            
            // Thử Google Fonts (nhưng không chờ lâu)
            try {
                await loadGoogleFonts();
                clearTimeout(emergencyTimer);
                clearTimeout(globalTimer);
            } catch (error) {
                console.log('❌ Google Fonts failed:', error.message);
                applyFallback();
                clearTimeout(emergencyTimer);
                clearTimeout(globalTimer);
            }
            
        } catch (error) {
            console.error('💥 Font initialization error:', error);
            emergencyFallback();
            clearTimeout(emergencyTimer);
            clearTimeout(globalTimer);
        }
    }
    
    /**
     * Network-aware - InfinityFree thường có mạng chậm
     */
    function handleNetworkConditions() {
        try {
            // Luôn assume mạng chậm trên InfinityFree
            const hostname = window.location.hostname;
            if (hostname.includes('infy.uk') || hostname.includes('infinityfree')) {
                console.log('🐌 InfinityFree hosting - assume slow network');
                setTimeout(applyFallback, 800);
                return true;
            }
            
            if ('connection' in navigator) {
                const connection = navigator.connection;
                
                if (connection.effectiveType === 'slow-2g' || 
                    connection.effectiveType === '2g' || 
                    connection.saveData ||
                    connection.downlink < 1) {
                    console.log('🐌 Slow network detected');
                    applyFallback();
                    return true;
                }
            }
        } catch (error) {
            console.log('⚠️ Network detection failed');
        }
        
        return false;
    }
    
    /**
     * Force visibility để tránh invisible text
     */
    function forceVisibility() {
        document.body.style.visibility = 'visible';
        document.body.style.opacity = '1';
        
        // Đảm bảo tất cả text hiển thị
        const textElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, div, a, button, li');
        textElements.forEach(el => {
            el.style.visibility = 'visible';
            el.style.opacity = '1';
        });
    }
    
    /**
     * Initialize ngay khi DOM ready
     */
    function init() {
        // Force visibility ngay lập tức
        forceVisibility();
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Emergency fallback ngay lập tức cho worst case
        setTimeout(emergencyFallback, 5000);
        
        // Check network và apply fallback nếu cần
        if (handleNetworkConditions()) {
            return;
        }
        
        // Start font loading
        initializeFonts();
    }
    
    // Expose debugging tools
    window.FontHandler = {
        state: fontLoadingState,
        applyFallback: applyFallback,
        emergencyFallback: emergencyFallback,
        reinitialize: initializeFonts,
        forceVisibility: forceVisibility
    };
    
    // Start ngay lập tức
    init();
    
    // Backup initialization
    setTimeout(init, 100);
    
})(); 