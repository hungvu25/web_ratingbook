/**
 * Roboto Local Font Handler
 * Xử lý việc load và áp dụng font Roboto từ file local
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        testString: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        fallbackTimeout: 2000, // 2 giây timeout
        checkInterval: 100, // Kiểm tra mỗi 100ms
        maxChecks: 20 // Tối đa 20 lần kiểm tra (2 giây)
    };
    
    // Font loading state
    let fontState = {
        robotoLoaded: false,
        fallbackApplied: false,
        initialized: false,
        checkCount: 0
    };
    
    /**
     * Kiểm tra xem font Roboto đã load chưa
     */
    function isRobotoLoaded() {
        try {
            // Tạo canvas để test font
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            if (!context) {
                return false;
            }
            
            // Test với font fallback
            context.font = '16px monospace';
            const fallbackWidth = context.measureText(CONFIG.testString).width;
            
            // Test với Roboto
            context.font = '16px Roboto, monospace';
            const robotoWidth = context.measureText(CONFIG.testString).width;
            
            // Nếu độ rộng khác nhau thì font đã load
            return Math.abs(fallbackWidth - robotoWidth) > 1;
            
        } catch (error) {
            console.warn('Lỗi khi kiểm tra font Roboto:', error);
            return false;
        }
    }
    
    /**
     * Áp dụng font Roboto khi đã load thành công
     */
    function applyRoboto() {
        if (fontState.robotoLoaded) return;
        
        console.log('✅ Font Roboto đã load thành công!');
        fontState.robotoLoaded = true;
        
        // Thêm class để áp dụng Roboto
        document.documentElement.classList.add('roboto-loaded');
        document.body.classList.add('roboto-loaded');
        
        // Xóa class loading
        document.documentElement.classList.remove('font-loading-roboto');
        document.body.classList.remove('font-loading-roboto');
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('robotoLoaded', {
            detail: { success: true }
        }));
        
        console.log('🎨 Font Roboto đã được áp dụng cho toàn bộ website');
    }
    
    /**
     * Áp dụng fallback font khi Roboto không load được
     */
    function applyFallback() {
        if (fontState.fallbackApplied) return;
        
        console.log('⚠️ Font Roboto không load được, sử dụng fallback font');
        fontState.fallbackApplied = true;
        
        // Thêm class fallback
        document.documentElement.classList.add('roboto-fallback');
        document.body.classList.add('roboto-fallback');
        
        // Xóa class loading
        document.documentElement.classList.remove('font-loading-roboto');
        document.body.classList.remove('font-loading-roboto');
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('robotoFallback', {
            detail: { success: false }
        }));
        
        console.log('🔄 Fallback font đã được áp dụng');
    }
    
    /**
     * Kiểm tra font với timeout
     */
    function checkFontLoading() {
        fontState.checkCount++;
        
        if (isRobotoLoaded()) {
            applyRoboto();
            return;
        }
        
        // Nếu đã quá số lần kiểm tra tối đa, áp dụng fallback
        if (fontState.checkCount >= CONFIG.maxChecks) {
            applyFallback();
            return;
        }
        
        // Tiếp tục kiểm tra
        setTimeout(checkFontLoading, CONFIG.checkInterval);
    }
    
    /**
     * Khởi tạo font loading
     */
    function initializeRoboto() {
        if (fontState.initialized) return;
        fontState.initialized = true;
        
        console.log('🚀 Khởi tạo Roboto font loading...');
        
        // Thêm class loading
        document.documentElement.classList.add('font-loading-roboto');
        document.body.classList.add('font-loading-roboto');
        
        // Kiểm tra ngay lập tức
        if (isRobotoLoaded()) {
            applyRoboto();
            return;
        }
        
        // Bắt đầu kiểm tra định kỳ
        setTimeout(checkFontLoading, CONFIG.checkInterval);
        
        // Timeout fallback
        setTimeout(() => {
            if (!fontState.robotoLoaded && !fontState.fallbackApplied) {
                applyFallback();
            }
        }, CONFIG.fallbackTimeout);
    }
    
    /**
     * Force reload font (dành cho debugging)
     */
    function forceReload() {
        fontState = {
            robotoLoaded: false,
            fallbackApplied: false,
            initialized: false,
            checkCount: 0
        };
        
        // Xóa tất cả classes
        document.documentElement.classList.remove('roboto-loaded', 'roboto-fallback', 'font-loading-roboto');
        document.body.classList.remove('roboto-loaded', 'roboto-fallback', 'font-loading-roboto');
        
        // Khởi tạo lại
        initializeRoboto();
    }
    
    /**
     * Kiểm tra xem các file font có tồn tại không
     */
    function checkFontFiles() {
        const fontFiles = [
            'static/Roboto-Regular.ttf',
            'static/Roboto-Medium.ttf',
            'static/Roboto-Bold.ttf'
        ];
        
        const promises = fontFiles.map(file => {
            return fetch(file, { method: 'HEAD' })
                .then(response => ({
                    file: file,
                    exists: response.ok
                }))
                .catch(() => ({
                    file: file,
                    exists: false
                }));
        });
        
        Promise.all(promises).then(results => {
            const missingFiles = results.filter(r => !r.exists);
            if (missingFiles.length > 0) {
                console.warn('⚠️ Một số file font Roboto không tìm thấy:', missingFiles.map(f => f.file));
            } else {
                console.log('✅ Tất cả file font Roboto đã sẵn sàng');
            }
        });
    }
    
    /**
     * Initialize khi DOM ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        console.log('🎯 Roboto Font Handler initialized');
        
        // Kiểm tra file fonts (optional)
        checkFontFiles();
        
        // Khởi tạo font loading
        initializeRoboto();
    }
    
    // Expose global API
    window.RobotoFontHandler = {
        state: fontState,
        initialize: initializeRoboto,
        forceReload: forceReload,
        isLoaded: () => fontState.robotoLoaded,
        applyFallback: applyFallback,
        checkFiles: checkFontFiles
    };
    
    // Auto-initialize
    init();
    
    // Event listeners
    window.addEventListener('load', () => {
        if (!fontState.robotoLoaded && !fontState.fallbackApplied) {
            setTimeout(() => {
                if (!fontState.robotoLoaded) {
                    applyFallback();
                }
            }, 1000);
        }
    });
    
})(); 