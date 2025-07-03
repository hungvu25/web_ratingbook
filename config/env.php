<?php
/**
 * Environment Configuration
 * Load environment variables từ .env file hoặc system environment
 */

/**
 * Load environment variables from .env file nếu có Composer
 */
function loadEnvFromFile() {
    // Check if composer autoload exists
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        if (class_exists('Dotenv\Dotenv')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->safeLoad();
            return true;
        }
    }
    
    // Fallback: Load .env file manually
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        return true;
    }
    
    return false;
}

/**
 * Get environment variable với default value
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    
    if ($value === false) {
        return $default;
    }
    
    // Convert string values to appropriate types
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    
    // Remove quotes if present
    if (preg_match('/^"(.*)"$/', $value, $matches)) {
        return $matches[1];
    }
    if (preg_match("/^'(.*)'$/", $value, $matches)) {
        return $matches[1];
    }
    
    return $value;
}

// Load environment variables
loadEnvFromFile();
?> 