<?php
/**
 * Authentication View Controller
 * 
 * Routes to login or registration view based on the action parameter
 */

$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'login';

// Redirect to login if already logged in
if (Auth::isLoggedIn() && in_array($action, ['login', 'register'])) {
    redirect('?page=dashboard');
}

// Include appropriate view
switch ($action) {
    case 'register':
        include __DIR__ . '/register.php';
        break;
    
    case 'login':
    default:
        include __DIR__ . '/login.php';
        break;
}
