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

// Check session timeout for authenticated users
if (Auth::isLoggedIn()) {
    if (!Auth::checkSessionTimeout()) {
        setFlashMessage('session_expired', 'Your session has expired. Please log in again.', 'warning');
        redirect('?page=auth&action=login');
    }
}

// Handle authentication actions (login, register, logout)
if (isset($_GET['page']) && $_GET['page'] === 'auth' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../src/api/auth_api.php';
    exit();
}

// Handle document API actions (only for POST or AJAX)
if (isset($_GET['page']) && $_GET['page'] === 'documents' && isset($_GET['action'])) {
    $apiActions = ['upload', 'delete', 'list', 'get', 'search', 'categories'];
    if (in_array($_GET['action'], $apiActions)) {
        // Only route to API if it's a POST request or specifically an AJAX action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || in_array($_GET['action'], ['list', 'get', 'search', 'categories'])) {
            require __DIR__ . '/../src/api/document_api.php';
            exit();
        }
    }
}

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
