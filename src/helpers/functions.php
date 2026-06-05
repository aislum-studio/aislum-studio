<?php
/**
 * Helper Functions
 * 
 * Common utility functions used throughout the application
 */

/**
 * Sanitize user input
 * 
 * @param string $input User input
 * @return string Sanitized input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * 
 * @param string $email Email address
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Redirect to a page
 * 
 * @param string $location URL to redirect to
 */
function redirect($location) {
    header('Location: ' . $location);
    exit();
}

/**
 * Set flash message
 * 
 * @param string $key Message key
 * @param string $message Message text
 * @param string $type Message type (success, error, warning, info)
 */
function setFlashMessage($key, $message, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][$key] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get flash message
 * 
 * @param string $key Message key
 * @return array|null
 */
function getFlashMessage($key) {
    if (isset($_SESSION['flash_messages'][$key])) {
        $message = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }
    return null;
}

/**
 * Get all flash messages
 * 
 * @return array
 */
function getAllFlashMessages() {
    $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

/**
 * Format file size
 * 
 * @param int $bytes File size in bytes
 * @return string Formatted file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 * 
 * @return string HTML input field
 */
function getCSRFTokenField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 * 
 * @param string $date Date string
 * @return string Time ago string
 */
function getTimeAgo($date) {
    $time = strtotime($date);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($date);
    }
}

/**
 * Get file extension
 * 
 * @param string $filename Filename
 * @return string File extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file extension is allowed
 * 
 * @param string $filename Filename
 * @return bool
 */
function isAllowedExtension($filename) {
    $ext = getFileExtension($filename);
    return in_array($ext, ALLOWED_EXTENSIONS);
}

/**
 * Generate unique filename
 * 
 * @param string $filename Original filename
 * @return string Unique filename
 */
function generateUniqueFilename($filename) {
    $ext = getFileExtension($filename);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    return $name . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
}

/**
 * Log activity
 * 
 * @param int $userId User ID
 * @param string $action Action performed
 * @param string $entityType Entity type
 * @param int $entityId Entity ID
 * @param string $details Additional details
 */
function logActivity($userId, $action, $entityType, $entityId, $details = '') {
    $db = Database::getInstance();
    $db->insert('activity_log', [
        'user_id' => $userId,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'details' => $details
    ]);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user
 * 
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $db = Database::getInstance();
    return $db->fetchOne('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
}

/**
 * Check if current user is admin
 * 
 * @return bool
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}
