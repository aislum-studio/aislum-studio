<?php
/**
 * Helper Functions
 *
 * Common utility functions used throughout the application.
 */

/**
 * Sanitize user input.
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address.
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Redirect to a URL.
 */
function redirect($location) {
    header('Location: ' . $location);
    exit();
}

/**
 * Set flash message.
 */
function setFlashMessage($key, $message, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][$key] = ['message' => $message, 'type' => $type];
}

/**
 * Get (and clear) a single flash message.
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
 * Get (and clear) all flash messages.
 */
function getAllFlashMessages() {
    $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

/**
 * FIX: Single canonical logActivity() — replaces the duplicate in Document.php.
 *
 * Usage anywhere in the app:
 *   logActivity($userId, 'upload', 'document', $docId, 'Uploaded invoice.pdf');
 *
 * @param int    $userId     User performing the action
 * @param string $action     Verb: 'upload', 'delete', 'update', 'login', etc.
 * @param string $entityType 'document', 'design', 'asset', etc.
 * @param int    $entityId   ID of the affected record
 * @param string $details    Optional human-readable description
 */
function logActivity($userId, $action, $entityType, $entityId, $details = '') {
    try {
        $db = Database::getInstance();
        $db->insert('activity_log', [
            'user_id'     => (int)$userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => (int)$entityId,
            'details'     => $details,
        ]);
    } catch (Exception $e) {
        // Activity logging should never crash the app — log silently
        error_log('logActivity failed: ' . $e->getMessage());
    }
}

/**
 * Format file size into human-readable string.
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate CSRF token (stored in session).
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a submitted CSRF token against the session token.
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Return an HTML hidden input containing the CSRF token.
 */
function getCSRFTokenField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Format a date string.
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Return a human-readable "time ago" string.
 */
function getTimeAgo($date) {
    $diff = time() - strtotime($date);

    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   { $m = floor($diff / 60);   return $m . ' minute' . ($m > 1 ? 's' : '') . ' ago'; }
    if ($diff < 86400)  { $h = floor($diff / 3600);  return $h . ' hour'   . ($h > 1 ? 's' : '') . ' ago'; }
    if ($diff < 604800) { $d = floor($diff / 86400); return $d . ' day'    . ($d > 1 ? 's' : '') . ' ago'; }

    return formatDate($date);
}

/**
 * Get file extension (lowercase).
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if a filename's extension is in the allowed list.
 */
function isAllowedExtension($filename) {
    return in_array(getFileExtension($filename), ALLOWED_EXTENSIONS);
}

/**
 * Generate a unique filename preserving the original extension.
 */
function generateUniqueFilename($filename) {
    $ext  = getFileExtension($filename);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    return $name . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
}

/**
 * Get current user ID from session.
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get current user record from DB.
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $db = Database::getInstance();
    return $db->fetchOne('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]);
}

/**
 * Check if the current user has the admin role.
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}
