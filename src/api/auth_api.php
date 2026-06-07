<?php
/**
 * Authentication API Endpoints
 * 
 * Handles login, registration, and logout operations
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../helpers/functions.php';

session_start();

$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$method = $_SERVER['REQUEST_METHOD'];

// Handle different authentication actions
switch ($action) {
    case 'register':
        if ($method === 'POST') {
            handleRegistration();
        }
        break;
    
    case 'login':
        if ($method === 'POST') {
            handleLogin();
        }
        break;
    
    case 'logout':
        handleLogout();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle user registration
 */
function handleRegistration() {
    // Get POST data
    $fullName = isset($_POST['full_name']) ? sanitize($_POST['full_name']) : '';
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validate input
    if (empty($fullName) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        setFlashMessage('registration_error', 'All fields are required', 'error');
        redirect('?page=auth&action=register');
    }
    
    if ($password !== $confirmPassword) {
        setFlashMessage('registration_error', 'Passwords do not match', 'error');
        redirect('?page=auth&action=register');
    }
    
    // Register user
    $result = Auth::register($username, $email, $password, $fullName);
    
    if ($result['success']) {
        setFlashMessage('registration_success', 'Registration successful! Please log in.', 'success');
        redirect('?page=auth&action=login');
    } else {
        setFlashMessage('registration_error', $result['message'], 'error');
        redirect('?page=auth&action=register');
    }
}

/**
 * Handle user login
 */
function handleLogin() {
    // Get POST data
    $usernameEmail = isset($_POST['username_email']) ? sanitize($_POST['username_email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate input
    if (empty($usernameEmail) || empty($password)) {
        setFlashMessage('login_error', 'Username/Email and password are required', 'error');
        redirect('?page=auth&action=login');
    }
    
    // Login user
    $result = Auth::login($usernameEmail, $password);
    
    if ($result['success']) {
        setFlashMessage('login_success', 'Login successful!', 'success');
        redirect('?page=dashboard');
    } else {
        setFlashMessage('login_error', $result['message'], 'error');
        redirect('?page=auth&action=login');
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    Auth::logout();
    setFlashMessage('logout_success', 'You have been logged out.', 'success');
    redirect('?page=auth&action=login');
}
