<?php
/**
 * Aislum Studio Configuration
 * Copy this file to config.php and update with your settings.
 */

// Application Settings
define('APP_NAME', 'Aislum Studio');
define('APP_URL',  'https://aislumstudio.com'); // FIX: was hardcoded to localhost
define('APP_ENV',  'production');               // 'development' or 'production'

// Database Configuration
define('DB_TYPE', 'sqlite'); // 'mysql' or 'sqlite'

// SQLite Configuration
define('SQLITE_PATH', __DIR__ . '/../database/aislum.sqlite');

// MySQL Configuration (used when DB_TYPE = 'mysql')
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'aislum_studio');

// File Upload Settings
define('UPLOAD_DIR',          __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE',     10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXTENSIONS',  ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Session Configuration
define('SESSION_NAME',         'aislum_session');
define('SESSION_TIMEOUT',      3600);       // Absolute max session lifetime: 1 hour
define('SESSION_IDLE_TIMEOUT', 1800);       // FIX: Idle timeout — 30 min of inactivity logs out

// Security
define('HASH_ALGORITHM', 'bcrypt');
define('HASH_COST',      12);               // Increased from 10 for better security

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors',     1);
}
