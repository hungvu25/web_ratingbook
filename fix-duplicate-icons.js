/**
 * Fix Duplicate Icons - Run immediately
 */
(function() {
    'use strict';
    
    // Force remove fallback classes to prevent duplicate icons
    function forceRemoveFallback() {
        // Remove all possible fallback classes
        document.body.classList.remove('icon-fallback-active', 'font-fallback', 'font-loading');
        document.documentElement.classList.remove('icon-fallback-active', 'font-fallback', 'font-loading');
        
        // Add font-loaded class
        document.documentElement.classList.add('font-loaded');
        
        console.log('🔧 Force removed icon fallback to prevent duplicates');
    }
    
    // Check if Font Awesome CSS is loaded
    function isFontAwesomeStyleLoaded() {
        // Check for Font Awesome in stylesheets
        const stylesheets = Array.from(document.styleSheets);
        const hasFA = stylesheets.some(sheet => {
            try {
                return sheet.href && sheet.href.includes('font-awesome');
            } catch (e) {
                return false;
            }
        });
        
        // Also check for Font Awesome by testing an icon
        if (!hasFA) {
            try {
                const testElement = document.createElement('i');
                testElement.className = 'fas fa-home';
                testElement.style.position = 'absolute';
                testElement.style.left = '-9999px';
                document.body.appendChild(testElement);
                
                const computed = window.getComputedStyle(testElement);
                const hasFAFont = computed.fontFamily.includes('Font Awesome');
                
                document.body.removeChild(testElement);
                return hasFAFont;
            } catch (e) {
                return false;
            }
        }
        
        return hasFA;
    }
    
    // Immediate check and removal
    if (isFontAwesomeStyleLoaded()) {
        forceRemoveFallback();
    }
    
    // Also check when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            forceRemoveFallback();
        }, 100);
    });
    
    // Check when all resources are loaded
    window.addEventListener('load', function() {
        setTimeout(function() {
            forceRemoveFallback();
        }, 200);
    });
    
    // Periodic check to ensure no duplicates
    setInterval(function() {
        if (document.body.classList.contains('icon-fallback-active') || 
            document.body.classList.contains('font-fallback')) {
            forceRemoveFallback();
        }
    }, 1000);
    
    // Expose function for manual use
    window.fixDuplicateIcons = forceRemoveFallback;
    
})();

// Run immediately when script loads
(function() {
    if (document.body) {
        document.body.classList.remove('icon-fallback-active', 'font-fallback', 'font-loading');
        document.documentElement.classList.remove('icon-fallback-active', 'font-fallback', 'font-loading');
        document.documentElement.classList.add('font-loaded');
    }
})(); 