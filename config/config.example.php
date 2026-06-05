<?php
/**
 * Aislum Studio Configuration
 * Copy this file to config.php and update with your settings
 */

// Application Settings
define('APP_NAME', 'Aislum Studio');
define('APP_URL', 'http://localhost:8000');
define('APP_ENV', 'development'); // development, production

// Database Configuration
// Choose between MySQL or SQLite
define('DB_TYPE', 'sqlite'); // 'mysql' or 'sqlite'

// SQLite Configuration
define('SQLITE_PATH', __DIR__ . '/../database/aislum.sqlite');

// MySQL Configuration (if using MySQL)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aislum_studio');

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_NAME', 'aislum_session');

// Security
define('HASH_ALGORITHM', 'bcrypt');
define('HASH_COST', 10);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}
