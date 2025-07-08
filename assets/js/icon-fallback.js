/**
 * Icon Fallback Handler
 * Xử lý fallback icon khi Font Awesome không load được
 */

(function() {
    'use strict';
    
    // Icon mappings to emoji/Unicode
    const ICON_FALLBACKS = {
        'fa-book': '📚',
        'fa-book-reader': '📚',
        'fa-home': '🏠',
        'fa-star': '⭐',
        'fa-tags': '🏷️',
        'fa-user': '👤',
        'fa-user-circle': '👤',
        'fa-sign-in-alt': '🔑',
        'fa-user-plus': '➕',
        'fa-sign-out-alt': '🚪',
        'fa-cog': '⚙️',
        'fa-comments': '💬',
        'fa-calendar': '📅',
        'fa-paper-plane': '✈️',
        'fa-bookmark': '🔖',
        'fa-envelope': '✉️',
        'fa-shield': '🛡️',
        'fa-question': '❓',
        'fa-info': 'ℹ️',
        'fa-link': '🔗',
        'fa-plus': '➕',
        'fa-edit': '✏️',
        'fa-trash': '🗑️',
        'fa-search': '🔍',
        'fa-heart': '❤️',
        'fa-check': '✅',
        'fa-times': '❌',
        'fa-arrow-left': '←',
        'fa-arrow-right': '→',
        'fa-download': '⬇️',
        'fa-upload': '⬆️'
    };
    
    // State tracking
    let iconState = {
        fontAwesomeLoaded: false,
        fallbackApplied: false,
        checkCount: 0,
        maxChecks: 50
    };
    
         /**
      * Kiểm tra xem Font Awesome đã load chưa
      */
     function isFontAwesomeLoaded() {
         try {
             // Kiểm tra nếu document.fonts API có sẵn
             if (document.fonts && document.fonts.check) {
                 return document.fonts.check('1em "Font Awesome 6 Free"');
             }
             
             // Fallback method: tạo element test
             const testElement = document.createElement('i');
             testElement.className = 'fas fa-home';
             testElement.style.position = 'absolute';
             testElement.style.left = '-9999px';
             testElement.style.fontSize = '16px';
             testElement.style.fontFamily = '"Font Awesome 6 Free"';
             
             document.body.appendChild(testElement);
             
             // Kiểm tra font family
             const computedStyle = window.getComputedStyle(testElement);
             const fontFamily = computedStyle.fontFamily.toLowerCase();
             
             document.body.removeChild(testElement);
             
             // Check if Font Awesome is in the font stack
             const isFALoaded = fontFamily.includes('font awesome') || 
                               fontFamily.includes('fontawesome') ||
                               fontFamily.includes('"font awesome 6 free"');
             
             console.log('Font Awesome check:', {
                 fontFamily: fontFamily,
                 isLoaded: isFALoaded
             });
             
             return isFALoaded;
         } catch (error) {
             console.warn('Error checking Font Awesome:', error);
             return false;
         }
     }
    
         /**
      * Áp dụng emoji fallback cho icons
      */
     function applyIconFallbacks() {
         if (iconState.fallbackApplied) return;
         
         console.log('🔄 Áp dụng icon fallbacks...');
         iconState.fallbackApplied = true;
         
         // Add fallback class to body để CSS fallback có hiệu lực
         document.body.classList.add('icon-fallback-active');
         
         // Tìm tất cả icon elements
         const icons = document.querySelectorAll('i[class*="fa-"]');
         
         console.log(`✅ Áp dụng fallback cho ${icons.length} icons via CSS`);
         
         // Dispatch event để các component khác biết
         window.dispatchEvent(new CustomEvent('iconFallbackActivated', {
             detail: { iconCount: icons.length }
         }));
     }
    
         /**
      * Xóa fallback khi Font Awesome load được
      */
     function removeFallbacks() {
         if (!iconState.fallbackApplied) return;
         
         console.log('🎯 Font Awesome loaded - removing fallbacks');
         
         // Remove fallback class - CSS sẽ tự động ẩn emoji fallbacks
         document.body.classList.remove('icon-fallback-active');
         iconState.fallbackApplied = false;
         
         // Dispatch event
         window.dispatchEvent(new CustomEvent('iconFallbackRemoved'));
     }
    
    /**
     * Kiểm tra Font Awesome loading định kỳ
     */
    function checkFontAwesome() {
        iconState.checkCount++;
        
        if (isFontAwesomeLoaded()) {
            iconState.fontAwesomeLoaded = true;
            console.log('✅ Font Awesome loaded successfully');
            
            if (iconState.fallbackApplied) {
                removeFallbacks();
            }
            return;
        }
        
                 // Nếu chưa áp dụng fallback và đã check một vài lần (tăng threshold)
         if (!iconState.fallbackApplied && iconState.checkCount > 25) {
             applyIconFallbacks();
         }
        
        // Tiếp tục check nếu chưa đạt max
        if (iconState.checkCount < iconState.maxChecks) {
            setTimeout(checkFontAwesome, 200);
        } else if (!iconState.fallbackApplied) {
            // Timeout - áp dụng fallback
            applyIconFallbacks();
        }
    }
    
    /**
     * Observer cho dynamic content
     */
    function setupDynamicIconHandler() {
        if (!window.MutationObserver) return;
        
        const observer = new MutationObserver(function(mutations) {
            let hasNewIcons = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const icons = node.querySelectorAll ? node.querySelectorAll('i[class*="fa-"]') : [];
                            if (icons.length > 0 || (node.classList && node.classList.toString().includes('fa-'))) {
                                hasNewIcons = true;
                            }
                        }
                    });
                }
            });
            
            if (hasNewIcons) {
                setTimeout(() => {
                    if (iconState.fallbackApplied && !iconState.fontAwesomeLoaded) {
                        applyIconFallbacks();
                    }
                }, 100);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    /**
     * Force apply fallbacks (public API)
     */
    function forceApplyFallbacks() {
        iconState.fallbackApplied = false;
        applyIconFallbacks();
    }
    
    /**
     * Kiểm tra trạng thái icons (public API)
     */
    function getIconStatus() {
        return {
            fontAwesomeLoaded: iconState.fontAwesomeLoaded,
            fallbackApplied: iconState.fallbackApplied,
            checkCount: iconState.checkCount,
            totalIcons: document.querySelectorAll('i[class*="fa-"]').length
        };
    }
    
    /**
     * Initialize
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        console.log('🎯 Icon Fallback Handler initialized');
        
        // Setup dynamic content observer
        setupDynamicIconHandler();
        
        // Start checking Font Awesome
        setTimeout(checkFontAwesome, 100);
        
                 // Emergency fallback after 5 seconds
         setTimeout(() => {
             if (!iconState.fontAwesomeLoaded && !iconState.fallbackApplied) {
                 console.log('⏰ Emergency icon fallback');
                 applyIconFallbacks();
             }
         }, 5000);
    }
    
    // Expose public API
    window.IconFallbackHandler = {
        getStatus: getIconStatus,
        forceApply: forceApplyFallbacks,
        removeFallbacks: removeFallbacks,
        state: iconState,
        mappings: ICON_FALLBACKS
    };
    
    // Auto-initialize
    init();
    
    // Listen for page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && !iconState.fontAwesomeLoaded) {
            setTimeout(checkFontAwesome, 500);
        }
    });
    
})(); 