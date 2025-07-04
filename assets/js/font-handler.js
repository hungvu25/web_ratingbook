/**
 * Enhanced Font Handler - Xử lý font & icon thông minh cho InfinityFree hosting
 * Hỗ trợ: Poppins local/CDN fallback + Font Awesome local/CDN fallback
 */

(function() {
    'use strict';
    
    // Configuration
    const CONFIG = {
        googleFontUrl: 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        fallbackTimeout: 2500, // Giảm timeout cho hosting chậm
        retryAttempts: 1, // Giảm retry cho hosting infinity
        testString: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        iconTestClass: 'fas fa-home'
    };
    
    // Font loading state
    let fontLoadingState = {
        poppinsLoaded: false,
        poppinsLocal: true, // Assume local fonts work first
        fontAwesomeLoaded: false,
        fontAwesomeLocal: true,
        fallbackActive: false,
        isInfinityHosting: false
    };
    
    /**
     * Detect if running on InfinityFree or similar free hosting
     */
    function detectHostingEnvironment() {
        const hostname = window.location.hostname;
        const isInfinity = hostname.includes('infinityfree') || 
                          hostname.includes('000webhostapp') ||
                          hostname.includes('freehostia') ||
                          hostname.endsWith('.rf.gd') ||
                          hostname.endsWith('.tk') ||
                          hostname.endsWith('.ml');
        
        fontLoadingState.isInfinityHosting = isInfinity;
        
        if (isInfinity) {
            console.log('🔍 Detected free hosting environment - optimizing font loading');
            CONFIG.fallbackTimeout = 1500; // Faster fallback
            CONFIG.retryAttempts = 0; // No retry
        }
        
        return isInfinity;
    }
    
    /**
     * Check local Poppins fonts availability
     */
    function checkLocalPoppins() {
        return new Promise((resolve) => {
            try {
                // Test if local Poppins fonts are working
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                ctx.font = '12px monospace';
                const defaultWidth = ctx.measureText(CONFIG.testString).width;
                
                ctx.font = '12px Poppins, monospace';
                const poppinsWidth = ctx.measureText(CONFIG.testString).width;
                
                const hasLocalPoppins = Math.abs(defaultWidth - poppinsWidth) > 0.1;
                
                fontLoadingState.poppinsLocal = hasLocalPoppins;
                
                if (hasLocalPoppins) {
                    console.log('✅ Local Poppins fonts detected and working');
                    document.documentElement.classList.add('poppins-local');
                    fontLoadingState.poppinsLoaded = true;
                    resolve(true);
                } else {
                    console.log('⚠️ Local Poppins fonts not detected');
                    resolve(false);
                }
            } catch (error) {
                console.log('❌ Error checking local Poppins:', error);
                resolve(false);
            }
        });
    }
    
    /**
     * Check Font Awesome icons availability
     */
    function checkFontAwesome() {
        return new Promise((resolve) => {
            try {
                const testIcon = document.createElement('i');
                testIcon.className = CONFIG.iconTestClass;
                testIcon.style.position = 'absolute';
                testIcon.style.left = '-9999px';
                testIcon.style.fontSize = '16px';
                document.body.appendChild(testIcon);
                
                // Wait a moment for rendering
                setTimeout(() => {
                    const iconWidth = testIcon.offsetWidth;
                    const iconHeight = testIcon.offsetHeight;
                    document.body.removeChild(testIcon);
                    
                    // Font Awesome icons should have reasonable dimensions
                    const hasFontAwesome = iconWidth > 8 && iconHeight > 8;
                    
                    fontLoadingState.fontAwesome = hasFontAwesome;
                    
                    if (hasFontAwesome) {
                        console.log('✅ Font Awesome icons working');
                        document.documentElement.classList.add('fa-loaded');
                    } else {
                        console.log('⚠️ Font Awesome icons not working, applying fallback');
                        applyIconFallback();
                    }
                    
                    resolve(hasFontAwesome);
                }, 100);
                
            } catch (error) {
                console.log('❌ Error checking Font Awesome:', error);
                applyIconFallback();
                resolve(false);
            }
        });
    }
    
    /**
     * Apply icon fallback
     */
    function applyIconFallback() {
        document.documentElement.classList.add('fa-fallback');
        
        // Add fallback class to all icons
        const icons = document.querySelectorAll('i[class*="fa-"]');
        icons.forEach(icon => {
            icon.classList.add('fa-fallback');
        });
        
        console.log('🔧 Applied Font Awesome fallback to ' + icons.length + ' icons');
    }
    
    /**
     * Load Google Fonts with retry mechanism (optimized for free hosting)
     */
    function loadGoogleFonts(attempt = 1) {
        return new Promise((resolve, reject) => {
            // Skip if local fonts are working
            if (fontLoadingState.poppinsLocal) {
                resolve(true);
                return;
            }
            
            console.log(`🔄 Loading Google Fonts (attempt ${attempt}/${CONFIG.retryAttempts + 1})`);
            
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = CONFIG.googleFontUrl;
            link.crossOrigin = 'anonymous';
            
            const timeout = setTimeout(() => {
                console.log('⏰ Google Fonts timeout');
                link.remove();
                
                if (attempt <= CONFIG.retryAttempts) {
                    console.log(`🔄 Retrying Google Fonts (${attempt + 1})`);
                    loadGoogleFonts(attempt + 1).then(resolve).catch(reject);
                } else {
                    reject(new Error('Google Fonts failed after retries'));
                }
            }, CONFIG.fallbackTimeout);
            
            link.onload = () => {
                clearTimeout(timeout);
                console.log('✅ Google Fonts loaded successfully');
                fontLoadingState.poppinsLoaded = true;
                document.documentElement.classList.add('poppins-loaded');
                resolve(true);
            };
            
            link.onerror = () => {
                clearTimeout(timeout);
                console.log('❌ Google Fonts failed to load');
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
     * Apply font fallback
     */
    function applyFontFallback() {
        console.log('🔧 Applying font fallback');
        fontLoadingState.fallbackActive = true;
        document.body.classList.add('font-fallback');
        document.documentElement.classList.add('font-fallback');
        
        // Update status
        updateFontStatus('fallback');
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('fontFallbackActivated', {
            detail: fontLoadingState
        }));
    }
    
    /**
     * Update font status indicator
     */
    function updateFontStatus(status) {
        const statusEl = document.getElementById('fontStatus');
        if (!statusEl) return;
        
        switch (status) {
            case 'loading':
                statusEl.textContent = '🔄 Loading fonts...';
                statusEl.className = 'font-status loading';
                break;
            case 'loaded':
                statusEl.textContent = '✅ Fonts loaded';
                statusEl.className = 'font-status loaded';
                setTimeout(() => statusEl.style.display = 'none', 2000);
                break;
            case 'fallback':
                statusEl.textContent = '⚠️ Using fallback fonts';
                statusEl.className = 'font-status fallback';
                setTimeout(() => statusEl.style.display = 'none', 3000);
                break;
        }
    }
    
    /**
     * Verify fonts loaded correctly
     */
    function verifyFontsLoaded() {
        return new Promise((resolve) => {
            if (!document.fonts) {
                resolve(false);
                return;
            }
            
            document.fonts.ready.then(() => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Test fallback font
                ctx.font = '12px Arial, sans-serif';
                const fallbackWidth = ctx.measureText(CONFIG.testString).width;
                
                // Test Poppins
                ctx.font = '12px Poppins, Arial, sans-serif';
                const poppinsWidth = ctx.measureText(CONFIG.testString).width;
                
                const fontLoaded = Math.abs(fallbackWidth - poppinsWidth) > 0.1;
                
                console.log(`📊 Font verification: ${fontLoaded ? 'PASSED' : 'FAILED'}`);
                resolve(fontLoaded);
            }).catch(() => resolve(false));
        });
    }
    
    /**
     * Handle network-aware loading
     */
    function handleNetworkConditions() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            // On slow networks, use fallback immediately
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                console.log('� Slow network detected, using fallback immediately');
                applyFontFallback();
                return true;
            }
            
            // On 3G, reduce timeout
            if (connection.effectiveType === '3g') {
                CONFIG.fallbackTimeout = Math.min(CONFIG.fallbackTimeout, 1500);
            }
        }
        
        return false;
    }
    
    /**
     * Main initialization function
     */
    async function initializeFonts() {
        console.log('🚀 Initializing enhanced font system');
        updateFontStatus('loading');
        
        try {
            // Detect hosting environment
            detectHostingEnvironment();
            
            // Check network conditions
            if (handleNetworkConditions()) {
                return;
            }
            
            // Step 1: Check local fonts
            const hasLocalPoppins = await checkLocalPoppins();
            
            // Step 2: Check Font Awesome
            setTimeout(() => checkFontAwesome(), 500);
            
            // Step 3: Load Google Fonts if needed
            if (!hasLocalPoppins) {
                try {
                    await loadGoogleFonts();
                    
                    // Verify fonts loaded
                    const verified = await verifyFontsLoaded();
                    if (!verified) {
                        console.log('⚠️ Font verification failed');
                        applyFontFallback();
                    } else {
                        updateFontStatus('loaded');
                    }
                } catch (error) {
                    console.log('❌ Google Fonts failed:', error.message);
                    applyFontFallback();
                }
            } else {
                updateFontStatus('loaded');
            }
            
        } catch (error) {
            console.error('💥 Font initialization error:', error);
            applyFontFallback();
        }
        
        // Remove loading class
        document.body.classList.remove('font-loading');
        document.documentElement.classList.remove('fonts-loading');
    }
    
    /**
     * Preload critical resources
     */
    function preloadResources() {
        // Preload Google Fonts if not on free hosting
        if (!fontLoadingState.isInfinityHosting) {
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
    }
    
    /**
     * Initialize when ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Start initialization
        initializeFonts();
        
        // Preload resources for better performance
        preloadResources();
    }
    
    // Expose to global scope for debugging
    window.FontHandler = {
        state: fontLoadingState,
        config: CONFIG,
        applyFallback: applyFontFallback,
        applyIconFallback: applyIconFallback,
        reinitialize: initializeFonts,
        checkLocalPoppins: checkLocalPoppins,
        checkFontAwesome: checkFontAwesome
    };
    
    // Start initialization
    init();
    
})(); 