<?php
/**
 * Aislum Studio - Main Entry Point
 * 
 * This is the main entry point for the application.
 * All requests are routed through this file.
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core classes
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/Auth.php';
require_once __DIR__ . '/../src/helpers/functions.php';

// Initialize database connection
$db = Database::getInstance();

// Get the requested page/action
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'dashboard';
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'index';

// Check if user is logged in (except for auth pages)
if (!in_array($page, ['auth'])) {
    if (!Auth::isLoggedIn()) {
        redirect('?page=auth&action=login');
    }
}

// Route to appropriate page
switch ($page) {
    case 'auth':
        require __DIR__ . '/../src/views/auth/index.php';
        break;
    
    case 'dashboard':
        require __DIR__ . '/../src/views/dashboard/index.php';
        break;
    
    case 'documents':
        require __DIR__ . '/../src/views/documents/index.php';
        break;
    
    case 'designs':
        require __DIR__ . '/../src/views/designs/index.php';
        break;
    
    case 'profile':
        require __DIR__ . '/../src/views/profile/index.php';
        break;
    
    default:
        require __DIR__ . '/../src/views/dashboard/index.php';
        break;
}
