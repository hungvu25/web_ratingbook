/**
 * Error Handler - Fix runtime errors and improve stability
 * Specifically targets "Could not establish connection" errors
 */

(function() {
    'use strict';
    
    // Suppress common extension-related errors
    const originalError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        
        // Filter out known extension errors
        if (message.includes('Could not establish connection') ||
            message.includes('Receiving end does not exist') ||
            message.includes('Extension context invalidated') ||
            message.includes('chrome-extension://')) {
            return; // Suppress these errors
        }
        
        // Log other errors normally
        originalError.apply(console, args);
    };
    
    // Handle uncaught errors gracefully
    window.addEventListener('error', function(e) {
        const message = e.message || '';
        
        // Suppress extension-related errors
        if (message.includes('Extension context') ||
            message.includes('chrome-extension') ||
            message.includes('Could not establish connection')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(e) {
        const reason = e.reason || '';
        
        // Suppress extension-related promise rejections
        if (reason.toString().includes('Extension context') ||
            reason.toString().includes('chrome-extension') ||
            reason.toString().includes('Could not establish connection')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Initialize error handling
    console.log('🛡️ Error handler initialized - Extension errors suppressed');
    
})(); 